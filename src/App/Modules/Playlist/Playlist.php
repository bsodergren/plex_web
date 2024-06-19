<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Playlist;

use Plex\Modules\Database\PlexSql;
use UTMTemplate\HTML\Elements;

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
        $this->data    = $data;
        $this->db      = PlexSql::$DB;
        $this->library = $_SESSION['library'];
        if (isset($data['playlist_id'])) {
            $this->playlist_id = $data['playlist_id'];
        }
    }

    public function showAllPlaylists($library = false)
    {
        $where = '';
        if (false !== $library) {
            $where = ' and d.Library = "'.$this->library.'"';
        }

        $sql = 'select count(p.playlist_video_id) as count, p.playlist_id, d.name,
        d.Library from '.Db_TABLE_PLAYLIST_DATA.' as d, '.Db_TABLE_PLAYLIST_VIDEOS.' as
        p where (p.playlist_id = d.id) and d.hide = 0 '.$where.' group by p.playlist_id ORDER BY Library ASC;';

        // $sql = 'select id, name, Library from '.Db_TABLE_PLAYLIST_DATA.'  where hide = 0 '.$where.'  ORDER BY Library ASC;';

        // utminfo($sql);
        $results = $this->db->query($sql);

        return $results;
    }

    public function showPlaylists($library = false)
    {
        $where = '';
        if (false !== $library) {
            $where = ' and Library = "'.$this->library.'"   ';
        }

        // $sql = 'select count(p.playlist_video_id) as count, p.playlist_id, d.name,
        // d.Library from '.Db_TABLE_PLAYLIST_DATA.' as d, '.Db_TABLE_PLAYLIST_VIDEOS.' as
        // p where (p.playlist_id = d.id) and d.hide = 0 '.$where.' group by p.playlist_id ORDER BY Library ASC;';

        $sql     = 'select id, name, Library from '.Db_TABLE_PLAYLIST_DATA.'  where hide = 0 '.$where.'  ORDER BY Library ASC;';
        $results = $this->db->query($sql);

        return $results;
    }

    public function showPlaylistPreview($playlist_id)
    {
        $res = $this->getPlaylist($playlist_id, 'limit 4');

        return $res;
    }

    public function getPlaylistJsonOptions($playlist_id = null, $disabled_id = null)
    {
        $selected = [];
        $plArray  =[];
        if (null !== $playlist_id) {
            $selected =  ['value'=>$playlist_id];
        }
        $res = $this->showPlaylists();
        foreach ($res as $i => $row) {
            $selected       = false;
            $optionDisabled = false;
            if ('Favorites' == $row['name']) {
                continue;
            }
            if ($row['Library'] == $this->library) {
                if (str_contains($disabled_id, $row['id'])) {
                    $optionDisabled = true;
                }
                if ($row['id'] == $playlist_id) {
                    $selected       = true;
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

    public function getPlaylistSelectOptions($playlist_id = null, $disabled_id = null)
    {
        $selected = [];
        $plArray  =[];

        if (null !== $playlist_id) {
            $selected =  ['value'=>$playlist_id];
        }
        $res = $this->showPlaylists();
        foreach ($res as $i => $row) {
            if ('Favorites' == $row['name']) {
                continue;
            }
            if ($row['Library'] == $this->library) {
                $plArray[] = [
                    'value' => $row['id'],
                    'text'  => $row['name'],
                ];
            }
        }

        return Elements::SelectOptions(array: $plArray, selected: $selected,
            blank: 'Add to Playlist',
            disabled: $disabled_id,
            class: 'filter-option text-dark');
    }

    public static function getVideoPlaylists($id)
    {
        if ('' == $id) {
            return null;
        }

        $sql = 'SELECT * FROM '.Db_TABLE_PLAYLIST_VIDEOS.' v, '.Db_TABLE_PLAYLIST_DATA.' as d WHERE
         v.playlist_video_id = '.$id.' and v.playlist_id = d.id and d.hide = 0 ';

        //  SELECT * FROM plexweb_playlist_videos v, plexweb_playlist_data as d WHERE
        //          v.playlist_video_id = 7982 and v.playlist_id = d.id and d.hide = 0 and d.name not like 'favorites'
        // $sql = 'SELECT * FROM '.Db_TABLE_PLAYLIST_VIDEOS.' WHERE playlist_video_id = '.$id;

        $results = (new self())->db->query($sql);

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
        $sql     = 'SELECT name FROM '.Db_TABLE_PLAYLIST_DATA.' WHERE id = '.$playlist_id;
        $results = (new self())->db->query($sql);

        return $results[0]['name'];
    }
}
