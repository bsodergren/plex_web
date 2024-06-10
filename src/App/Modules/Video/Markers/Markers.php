<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Video\Markers;

use Plex\Modules\Database\PlexSql;
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
        utmdump($data);
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

        utmdump($data);
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
                if (null === $row['name']) {
                    $row['name'] = 'Timestamp';
                }

                $this->markerIndex[] = [
                    'time'     => $row['timeCode'],
                    'label'    => $row['name'],
                    'markerId' => $row['id'],
                ];
            }
        }

        return $this->markerIndex;
    }

    public function getMarkerButtons()
    {
        $html  = '';
        $index = $this->getMarkers();
        if (null === $index) {
            return '';
        }
        foreach ($index as $i => $row) {
            // $editableClass = 'edit'.$row['time'];
            // $functionName  = 'make'.$row['time'].'Editable';

            $row['MarkerId']   =  $row['markerId'];
            $row['videoId']    =  $this->id;
            $row['javascript'] = '';

            $row['DisplayVideo'] = $this->displayVideo;

            if ('true' == $this->displayVideo) {
                $row['javascript'] = ' onclick="seektoTime('.$row['time'].')" ';
            }
            // $row['VIDEOINFO_EDIT_JS'] = Render::javascript(
            //     Functions::$MarkerDir.'/marker',
            //     [
            //         'MarkerId'   => $row['markerId'],
            //         'EDITABLE'  => $editableClass,
            //         'FUNCTION'  => $functionName,
            //         'VIDEO_KEY' => $this->id,
            //     ]
            // );
            $html .= Render::html(Functions::$MarkerDir.'/markerButton', $row);
        }

        $buttonHtml = Render::html(Functions::$MarkerDir.'/markerButtons', ['MarkerButtons' => $html]);

        return $buttonHtml;
    }

    public function displayMarkers()
    {
        $html = $this->getMarkerButtons();

        return Render::html(Functions::$MarkerDir.'/marker', ['MarkerButton' => $html]);
    }
}
