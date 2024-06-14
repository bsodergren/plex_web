<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Process;

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Process\Traits\DbWrapper;
use Plex\Modules\Video\Markers\Markers;

class Marker extends Forms
{
    use DbWrapper;
    public object $db;
    public object $Markers;
    public $data;
    public $library;
    public $playlist_id;
    public $id;

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
        $this->Markers = new Markers($data);
    }

    public function getMarkerVideos()
    {
        utminfo($this->data);
        $type=null;
        if (\array_key_exists('edit', $this->data)) {
            $type = "Editor";
        }
        return $this->Markers->getMarkerButtons($type);
    }

    public function addMarkerVideo()
    {
        $data = [
            'timeCode' => $this->data['timeCode'],
            'video_id' => $this->data['videoId'],
            'markerText'     => $this->data['markerText'],
        ];
        $res = $this->db->insert(Db_TABLE_VIDEO_CHAPTER, $data);

        return $this->getMarkerVideos();
        //     $urlQuery = '?id='.$this->data['videoId'];

        //     if (\array_key_exists('playlistid', $this->data)) {
        //         $urlQuery .= '&playlist_id='.$this->data['playlistid'];
        //     }
        //     //return $this->data['timeCode'];
        //     $url =  __URL_HOME__.'/video.php'.$urlQuery;
        //    // utmdd($url);
        //     echo $this->myHeader($url);
        //     exit;
    }

    public function updateMarker()
    {
        if (\array_key_exists('markerText', $this->data)) {
            $markerText = $this->data['markerText'];
            if (\array_key_exists('markerId', $this->data)) {
                $markerId = $this->data['markerId'];
                $sql      = 'UPDATE '.Db_TABLE_VIDEO_CHAPTER." SET markerText = '".$markerText."' WHERE id = ".$markerId.'';
                $this->db->query($sql);
            }
        }

        return $this->getMarkerVideos();
    }

    public function deleteMarker()
    {
        if (\array_key_exists('markerId', $this->data)) {
            $markerId  = $this->data['markerId'];
            $sql       = 'DELETE FROM '.Db_TABLE_VIDEO_CHAPTER.' WHERE id = '.$markerId;
            utminfo($sql);
            $this->db->query($sql);
        }

        return $this->getMarkerVideos();
    }
}
