<?php
/**
 *  Plexweb
 */

namespace Plex\Template\Functions\Traits;

use Plex\Template\Render;
use Plex\Modules\Database\PlexSql;
use Plex\Modules\VideoCard\VideoCard;
use Plex\Modules\Video\Markers\Markers as vMarkers;

trait Markers
{
    public object $Markers;

    public function showMarkers($matches)
    {
        $var = $this->parseVars($matches);
        $this->Markers = new vMarkers($var);
        $Markers       = $this->Markers->displayMarkers();

        return $Markers;
    }



    public function markerCloud($matches)
    {
        $db         = PlexSql::$DB;
        $url        = __THIS_PAGE__.'.php';

        if (\array_key_exists('marker', $_GET)) {
            $markerText = urldecode($_GET['marker']);
            $sql        = 'select vm.markerText,vm.video_id,vm.timeCode,
            f.id, f.thumbnail from
            '.Db_TABLE_VIDEO_CHAPTER.' vm,
            '.Db_TABLE_VIDEO_FILE." f
             WHERE
             vm.video_id = f.id and
             vm.markerText = '".$markerText."' ORDER BY vm.video_id ASC";
            $url          = '#';
            $markerList   = $db->query($sql);
            $currId       = 0;

            foreach ($markerList as $i => $row) {
                $videoArray[$row['video_id']][] = $row;
            }
            $cell_html = '';
            foreach ($videoArray as $vid =>$video) {
                $thumbnail  = __URL_HOME__.$video[0]['thumbnail'];
                $buttonHTML = '';

                foreach ($video as $vrow) {
                    unset($url_params);
                    $url_params['id']= urlencode($vrow['video_id']);
                    $url_params['tc']= urlencode($vrow['timeCode']);

                    $buttonHTML .= Render::html('pages/Markers/Box/markerLink', [
                        'VideoID'    => $vrow['video_id'],
                        'javascript' => $this->markerPopup('js', '#', $url_params),
                        'timeCode'   => VideoCard::videoDuration($vrow['timeCode'],1),
                        'markerText' => $vrow['markerText'],
                    ]);
                }

                $thumbnail_html =  Render::html('pages/Markers/Box/thumbnail', [
                    'FILE_ID' => $vid,
                    // 'javascript' => $this->markerPopup('js',$url,$url_params),
                    'THUMBNAIL' => $thumbnail,
                ]);

                $cell_html .=  Render::html('pages/Markers/Box/cell', [
                    'thumbnail_html' => $thumbnail_html,
                    // 'javascript' => $this->markerPopup('js',$url,$url_params),
                    'video_markers' => $buttonHTML,
                ]);

            }
                $html =  Render::html('pages/Markers/Box/grid', [
                    'FILE_ID' => $vid,
                    // 'javascript' => $this->markerPopup('js',$url,$url_params),
                    'Grid_Cells_html' => $cell_html,
                ]);

        //     if(array_key_exists('video_id',$row)){
        //         $url_params['id']= urlencode($row['video_id']);
        //         unset($url_params['marker']);
        //     }
        //     if(array_key_exists('timeCode',$row)){
        //         $url_params['tc']= urlencode($row['timeCode']);
        //         unset($url_params['marker']);
        //     }

        //     if($currId != $row['video_id'])
        //     {
        //         $previewBox .=  Render::html('pages/Markers/Box/markerPreview', [
        //             'FILE_ID' => $row['video_id'],
        //             'javascript' => $this->markerPopup('js',$url,$url_params),
        //             'THUMBNAIL' => $row['thumbnail'],
        //             'VideoID' => $row['video_id'],
        //             'timeCode' => $row['timeCode'],
        //             'markerText' => $row['markerText'],
        //         ]);
        //         $currId = $row['video_id'];
        //     }
        } else {
            $url = '/plex/markers.php';
            $sql = 'select count(`markerText`) as cnt,`markerText` from '.Db_TABLE_VIDEO_CHAPTER.' GROUP BY `markerText` ORDER BY cnt ASC';
            $html = '';
            $markerList   = $db->query($sql);
            foreach ($markerList as $row) {
                $url_params['marker'] = urlencode($row['markerText']);
                $html .= Render::html('pages/Markers/markerButton', [
                    'url'        => $this->markerPopup('url', $url, $url_params),
                    'Count'      => $row['cnt'],
                    'markerText' => $row['markerText'],
                ]);
            }
        }

        return $html;
    }

    private function markerPopup($type, $url, $params=[])
    {
        $urlparams = http_build_query($params);
        if ('url' == $type) {
            if ('#' == $url) {
                return '';
            }

            return 'href="'.$url.'?'.$urlparams.'" ';
        }

        if ('js' == $type) {
            if ('#' == $url) {
                $window = 'video_popup';
                $url    = __URL_HOME__.'/video.php?'.$urlparams;

                return " onclick=\"popup('".$url."', '".$window."')\"";
            }

            return '';
        }
    }
}
