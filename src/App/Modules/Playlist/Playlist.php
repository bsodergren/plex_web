<?php

namespace Plex\Modules\Playlist;

use UTMTemplate\HTML\Elements;
use Plex\Modules\Database\PlexSql;

/**
 * plex web viewer.
 */

/**
 * plex web viewer.
 */
class Playlist 
{
    public object $db;

    public $data;
    public $library;
    public $playlist_id;

    public function __construct($data = [])
    {
        global $_SESSION;
        $this->data = $data;
        $this->db = PlexSql::$DB;
        $this->library = $_SESSION['library'];
        if (isset($data['playlist_id'])) {
            $this->playlist_id = $data['playlist_id'];
        }

    }

    public function showPlaylists()
    {
        $sql = 'select count(p.playlist_video_id) as count, p.playlist_id, d.name,
        d.library from '.Db_TABLE_PLAYLIST_DATA.' as d, '.Db_TABLE_PLAYLIST_VIDEOS.' as 
        p where (p.playlist_id = d.id) and d.hide = 0 group by p.playlist_id ORDER BY library ASC;';
        $results = $this->db->query($sql);
        return $results;
    }

    public function showPlaylistPreview($playlist_id){
        $res = $this->getPlaylist($playlist_id, "limit 4");
        return $res;
    }

    public function getPlaylistSelectOptions()
    {
        $res = $this->showPlaylists();
        foreach($res as $i => $row){
            if($row['library'] == $this->library){
                $plArray[] = ['value' => $row['playlist_id'],
                'text' => $row['name']];
            }
        }
        return Elements::SelectOptions($plArray,'','');
        
    }
    public static function getVideoPlaylists($id){
        if($id == ''){
            return null;
        }
        $sql = 'SELECT * FROM '.Db_TABLE_PLAYLIST_VIDEOS.' WHERE playlist_video_id = '.$id;
        $results = (new Playlist())->db->query($sql);
        return $results;
    }
    
    public function getPlaylist($playlist_id, $limit= '')
    {

        $sql = 'select f.thumbnail,f.id,d.name,d.genre,p.id as playlist_video_id,m.title from  '.Db_TABLE_PLAYLIST_DATA.' as d,
        '.Db_TABLE_VIDEO_FILE.' as f, '.Db_TABLE_PLAYLIST_VIDEOS.' as p, '.Db_TABLE_VIDEO_TAGS.' as m
         where (p.playlist_id = '.$playlist_id.' and p.playlist_video_id = f.id and d.id = p.playlist_id and f.video_key = m.video_key) '.$limit.';';
       $results = $this->db->query($sql);
       return $results;

    }

    public static function getPlaylistName($playlist_id)
    {
        $sql = 'SELECT name FROM '.Db_TABLE_PLAYLIST_DATA.' WHERE id = '.$playlist_id;
        $results = (new Playlist())->db->query($sql);
        return $results[0]['name'];
    }
   


}
