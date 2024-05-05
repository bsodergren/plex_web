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
    public function showAllPlaylists($library = false)
    {
        $where = '';
        if($library !== false ) {
            $where = ' and d.Library = "'.$this->library.'"';
        }

        $sql = 'select count(p.playlist_video_id) as count, p.playlist_id, d.name,
        d.library from '.Db_TABLE_PLAYLIST_DATA.' as d, '.Db_TABLE_PLAYLIST_VIDEOS.' as
        p where (p.playlist_id = d.id) and d.hide = 0 '.$where.' group by p.playlist_id ORDER BY library ASC;';

        // $sql = 'select id, name, library from '.Db_TABLE_PLAYLIST_DATA.'  where hide = 0 '.$where.'  ORDER BY library ASC;';

        // utmdump($sql);
        $results = $this->db->query($sql);
        return $results;
    }
    public function showPlaylists($library = false)
    {
        $where = '';
        if($library !== false ) {
            $where = ' and Library = "'.$this->library.'"';
        }

        // $sql = 'select count(p.playlist_video_id) as count, p.playlist_id, d.name,
        // d.library from '.Db_TABLE_PLAYLIST_DATA.' as d, '.Db_TABLE_PLAYLIST_VIDEOS.' as
        // p where (p.playlist_id = d.id) and d.hide = 0 '.$where.' group by p.playlist_id ORDER BY library ASC;';

        $sql = 'select id, name, library from '.Db_TABLE_PLAYLIST_DATA.'  where hide = 0 '.$where.'  ORDER BY library ASC;';

        $results = $this->db->query($sql);
        return $results;
    }

    public function showPlaylistPreview($playlist_id){
        $res = $this->getPlaylist($playlist_id, "limit 4");
        return $res;
    }
    public function getPlaylistJsonOptions($playlist_id = null,$disabled_id = null)
    {
        $selected = [];
        $plArray =[] ;
        if($playlist_id !== null){
            $selected =  ['value'=>$playlist_id];
        }
        $res = $this->showPlaylists();
        foreach($res as $i => $row)
        {
            $selected = false;
            $optionDisabled = false;
            if($row['library'] == $this->library){
                if (str_contains($disabled_id, $row['id'])) {
                    $optionDisabled = true;
                }
             if($row['id'] == $playlist_id){
                $selected = true;
                $optionDisabled = false;
             }
                $plArray[] = [
                    $row['id'],
                     $row['name'],
                     $selected,
                     $optionDisabled,

            ];
            }
        }
        return json_encode($plArray);

        // return Elements::SelectOptions(array: $plArray,selected: $selected,
        // blank: '',
        //  disabled: $disabled_id);

    }
    public function getPlaylistSelectOptions($playlist_id = null,$disabled_id = null)
    {
        $selected = [];
        $plArray=[];

        if($playlist_id !== null){
            $selected =  ['value'=>$playlist_id];
        }
        $res = $this->showPlaylists();
        foreach($res as $i => $row)
        {
            if($row['library'] == $this->library){

                $plArray[] = [
                    'value' => $row['id'],
                    'text' => $row['name'],
            ];
            }
        }

        return Elements::SelectOptions(array: $plArray,selected: $selected,
        blank: 'Add to Playlist',
         disabled: $disabled_id,
         class: 'filter-option text-dark');

    }
    public static function getVideoPlaylists($id){
        if($id == ''){
            return null;
        }

        $sql = 'SELECT * FROM '.Db_TABLE_PLAYLIST_VIDEOS.' v, '.Db_TABLE_PLAYLIST_DATA.' as d WHERE
         v.playlist_video_id = '.$id.' and v.playlist_id = d.id and d.hide = 0';
        //$sql = 'SELECT * FROM '.Db_TABLE_PLAYLIST_VIDEOS.' WHERE playlist_video_id = '.$id;
        $results = (new Playlist())->db->query($sql);
        return $results;
    }

    public function getPlaylist($playlist_id, $limit= '')
    {

        $sql = 'select v.thumbnail,v.id,d.name,d.genre,p.id as playlist_video_id,m.title from  '.Db_TABLE_PLAYLIST_DATA.' as d,
        '.Db_TABLE_VIDEO_FILE.' as v, '.Db_TABLE_PLAYLIST_VIDEOS.' as p, '.Db_TABLE_VIDEO_METADATA.' as m
         where (p.playlist_id = '.$playlist_id.' and p.playlist_video_id = v.id and d.id = p.playlist_id and v.video_key = m.video_key) '.$limit.';';
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
