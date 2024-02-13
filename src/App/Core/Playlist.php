<?php
namespace Plex\Core;
/**
 * plex web viewer
 */

/**
 * plex web viewer.
 */
class Playlist extends ProcessForms
{
    public object $db;

    public $data;
    public $library;
    public $playlist_id;

    public function __construct($data)
    {
        global $db,$_SESSION;
        $this->data    = $data;
        $this->db      = $db;
        $this->library = $_SESSION['library'];
        if (isset($data['playlist_id'])) {
            $this->playlist_id = $data['playlist_id'];
        }
    }

    public function addAllPlaylist()
    {
        $url = $this->createPlaylist();
        echo $this->myHeader($url);
    }

    private function addPlaylistData()
    {
        $hide      = 0;
        $search_id = null;

        $name      = 'User Playlist';
        $studio    = [];

        if (array_key_exists('substudio', $this->data)) {
            $name     = '';
            $studio[] = $this->data['substudio'];
        }
        if (array_key_exists('studio', $this->data)) {
            $name     = '';
            $studio[] = $this->data['studio'];
        }

        if (array_key_exists('playlist_name', $this->data)) 
        {
            if($this->data['playlist_name'] != '') {
                $name     = $this->data['playlist_name'];
            }
        }

        if (array_key_exists('PlayAll', $this->data)) {
            if (array_key_exists('search_id', $this->data)) {
                $search_id = $this->data['search_id'];
                $this->db->where('search_id', $search_id);
                $pl_search = $this->db->getOne(Db_TABLE_PLAYLIST_DATA);

                if (null === $pl_search) {
                    $hide                   = true;
                    $this->db->where('id', $search_id);
                    $search_data            = $this->db->getOne('metatags_search_data');

                    $this->data['playlist'] = $search_data['video_list'];
                } else {
                    $playlist_id = $pl_search['id'];

                    //  $search_id              = null;
                    return __URL_HOME__.'/video.php?playlist_id='.$playlist_id.'';
                }
            }
        }

        if (null === $pl_search) {
            $data        = [
                'name'      => $name.implode(' ', $studio),
                'genre'     => 'mmf,mff',
                'library'   => $this->library,
                'search_id' => $search_id,
                'hide'      => $hide,
            ];

            $playlist_id = $this->db->insert(Db_TABLE_PLAYLIST_DATA, $data);
        }

        return $playlist_id;
    }

    public function createPlaylist()
    {
        $playlist_id = $this->addPlaylistData();

        if (!array_key_exists('playlist', $this->data)) {
            return $playlist_id;
        }

        if (!is_array($this->data['playlist'])) {
            $this->data['playlist'] = explode(',', $this->data['playlist']);
        }

        foreach ($this->data['playlist'] as $_ => $id) {
            $data  = [
                'playlist_id'       => $playlist_id,
                'playlist_video_id' => $id,
                'library'           => $this->library,
            ];
            $ids[] = $this->db->insert(Db_TABLE_PLAYLIST_VIDEOS, $data);
        }
        if (array_key_exists('PlayAll', $this->data)) {
            return __URL_HOME__.'/video.php?playlist_id='.$playlist_id.'';
        }

        return __URL_HOME__.'/playlist.php?playlist_id='.$playlist_id.'';
    }

    public function addPlaylist()
    {
        // dump([__METHOD__,$this->data]);

        $data = [
            'playlist_id'       => $this->playlist_id,
            'playlist_video_id' => $this->data['video_id'],
            'library'           => $this->library,
        ];
        $res  = $this->db->insert(Db_TABLE_PLAYLIST_VIDEOS, $data);

        return 0;
    }

    public function deletePlaylist()
    {
        // dump([__METHOD__,$this->playlist_id]);

        $sql     = 'delete d,v from '.Db_TABLE_PLAYLIST_DATA.'  d join '.Db_TABLE_PLAYLIST_VIDEOS.' v on d.id = v.playlist_id where d.id = '.$this->playlist_id.'';
        $results = $this->db->query($sql);
        $this->myHeader('playlist.php');

        return 0;
    }

    public function savePlaylist()
    {
        // dump([__METHOD__,$this->data]);
        if (isset($this->data['playlist_name'])) {
            $playlist_name = $this->data['playlist_name'];
            if ('' != $playlist_name) {
                $update[] = " name = '".$playlist_name."' ";
            }
        }

        if (isset($this->data['playlist_genre'])) {
            $playlist_genre = $this->data['playlist_genre'];
            if ('' != $playlist_genre) {
                $update[] = " genre = '".$playlist_genre."' ";
            }
        }

        if (isset($update)) {
            $update_str = implode(', ', $update);
            $sql        = 'UPDATE '.Db_TABLE_PLAYLIST_DATA.' SET '.$update_str.' WHERE id = '.$this->playlist_id.'';
            $results    = $this->db->query($sql);
        }

        if (isset($this->data['prune_playlist'])) {
            $video_ids     = $this->data['prune_playlist'];
            foreach ($video_ids as $_ => $id) {
                $video_id_array[] = $id;
            }
            $video_ids_str = implode(', ', $video_id_array);
            $sql           = 'delete from '.Db_TABLE_PLAYLIST_VIDEOS.' where id in ('.$video_ids_str.')';
            $results       = $this->db->query($sql);
        }

        $form_url = __URL_HOME__.'/playlist.php?playlist_id='.$this->playlist_id.'';
        $this->myHeader($form_url);
    }
}
