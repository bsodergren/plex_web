<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Video\Markers;

use Plex\Modules\Database\PlexSql;
use Plex\Modules\VideoCard\VideoCard;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;

class Markers
{
    public object $db;

    public $data;
    public $library;
    public $playlist_id;
    public $id;
    public $markerIndex;
    private $displayVideo = 'true';

    public function __construct($data)
    {
        global $_SESSION;
        $this->data    = $data;
        $this->db      = PlexSql::$DB;
        $this->library = $_SESSION['library'];
        if (isset($data['playlist_id'])) {
            $this->playlist_id = $data['playlist_id'];
        }
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['video'])) {
            // if($data['video'] == "false" ) {
            //     $data['video'] = false;
            // }
            $this->displayVideo = $data['video'];
        }

        utminfo( $data);
    }

    public function getMarkerJson()
    {
        return json_encode($this->Markers->getMarkers());
    }

    public function getMarkers()
    {
        if (null == $this->markerIndex) {
            $this->db->where('video_id', $this->id);
            $this->db->orderBy('timeCode', 'ASC');
            $search_result = $this->db->get(Db_TABLE_VIDEO_CHAPTER);
            foreach ($search_result as $i => $row) {
                if (null === $row['markerText']) {
                    $row['markerText'] = 'Timestamp';
                }

                $this->markerIndex[] = [
                    'markerTime'          => $row['timeCode'],
                    'markerText'    => $row['markerText'],
                    'markerId'      => $row['id'],
                    'markerThumbnail' => @str_replace(APP_HTML_ROOT,__URL_HOME__,$row['markerThumbnail']),
                    'videoId'=>$row['video_id'],
                ];
            }
        }

        return $this->markerIndex;
    }

    private function markerButton($markerArray, $type)
    {
        if ('player' == $type) {
            $markerArray['javascript']   = ' onclick="seektoTime('.$markerArray['markerTime'].')" ';
            $markerArray['DisplayVideo'] = true;
        }
        if ('card' == $type) {
            $window                 = 'video_popup';
            $url                    = __URL_HOME__.'/video.php?id='.$markerArray['videoId'].'&tc='.$markerArray['markerTime'];
            $markerArray['javascript']   = " onclick=\"popup('".$url."', '".$window."')\"";
            $markerArray['DisplayVideo'] = false;
        }
        if ('Editor' == $type) {
            $markerArray['javascript']   = '';
            $markerArray['DisplayVideo'] = false;
            $markerArray['btnClass'] = ' btnHover';

            $markerArray['thumb'] = Render::html(Functions::$MarkerDir.'/image',['THUMBNAIL' => $markerArray['markerThumbnail']]);
            $markerArray['prefix'] = "<div class='btnContaner'>";
            $markerArray['end'] = "</div>";
        }


        $markerArray['DurationText'] = VideoCard::videoDuration($markerArray['markerTime'], 1);

        return Render::html(Functions::$MarkerDir.'/markerButton', $markerArray);
    }

    public function getMarkerButtons($displayType=null)
    {
        $html  = '';
        $index = $this->getMarkers();
        if (null === $index) {
            return '';
        }
        $editButton = '';

        foreach ($index as $i => $row) {
            $jsEditURL = __URL_HOME__.'/markers.php?edit='.$this->id;

            if($displayType === null){
                if ('true' == $this->displayVideo) {
                    $type = 'player';
                } else {
                    $type = 'card';
                }
            } else {
                $type = $displayType;
            }

            $html .= $this->markerButton(
                $row,
                $type);
        }
    if($type != 'Editor') {
            $editButton = Render::html(Functions::$MarkerDir.'/markerEditBtn', ['javascript'=> " onclick=\"popup('".$jsEditURL."', 'markerPopup')\"", 'markerText'=>'Edit']);
    }

        $buttonHtml = Render::html(Functions::$MarkerDir.'/markerButtons', [
            'MarkerButtons'     => $html,
            'MarkerEditButtons' => $editButton,
        ]);

        return $buttonHtml;
    }

    public function displayMarkers($type=null)
    {

        $html = $this->getMarkerButtons($type);

        return Render::html(Functions::$MarkerDir.'/marker', ['MarkerButton' => $html]);
    }
}
