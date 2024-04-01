<?php

namespace Plex\Modules\Process\Traits;

use Plex\Modules\Database\VideoDb;
use Plex\Template\Display\VideoDisplay;



/**
 * plex web viewer.
 */

/**
 * plex web viewer.
 */
trait Playlist 
{

public function getExistingVidsFromPl($playlist_id){
    $existingIds=[];
    if($playlist_id != ''){
    $this->db->where('playlist_id', $playlist_id);
    $pl_search = $this->db->get(Db_TABLE_PLAYLIST_VIDEOS,null,['playlist_video_id']);
    if ($this->db->count > 0){

            foreach($pl_search as $k => $row){
                $existingIds[$row['playlist_video_id']] = true ;
            }
        }
    }
        return $existingIds;
    
}
    public function addAllPlaylist()
    {
        $url = $this->createPlaylist();
        utmdump([__METHOD__,$url]);
        echo $this->myHeader($url);
    }

    private function addPlaylistData()
    {
        $hide = 0;
        $search_id = null;

        $name = 'User Playlist';
        $studio = [];

        if (\array_key_exists('substudio', $this->postArray)) {
            $name = '';
            $studio[] = $this->postArray['substudio'];
        }
        if (\array_key_exists('studio', $this->postArray)) {
            $name = '';
            $studio[] = $this->postArray['studio'];
        }

        if (\array_key_exists('playlist_name', $this->postArray)) {
            if ('' != $this->postArray['playlist_name']) {
                $name = $this->postArray['playlist_name'];
            }
        }

        if (\array_key_exists('AddToPlaylist', $this->postArray))
        {

           return $this->postArray['PlaylistID'];

        }
        utmdump([__METHOD__,$this->postArray]);

        if (\array_key_exists('PlayAll', $this->postArray)) {
            if (\array_key_exists('search_id', $this->postArray)) {
                $search_id = $this->postArray['search_id'];
                $this->db->where('search_id', $search_id);
                $pl_search = $this->db->getOne(Db_TABLE_PLAYLIST_DATA);

                if (null === $pl_search) {
                    $hide = true;
                    $name = 'Play All List';
                    $this->db->where('id', $search_id);
                    $search_data = $this->db->getOne(Db_TABLE_SEARCH_DATA);

                    $this->postArray['playlist'] = $search_data['video_list'];
                } else {
                    $playlist_id = $pl_search['id'];

                    //  $search_id              = null;
                    return __URL_HOME__.'/video.php?playlist_id='.$playlist_id.'';
                }
            }
        }

        if (null === $pl_search) {
            $data = [
                'name' => $name.implode(' ', $studio),
                'genre' => 'mmf,mff',
                'library' => $this->library,
                'search_id' => $search_id,
                'hide' => $hide,
            ];

            $playlist_id = $this->db->insert(Db_TABLE_PLAYLIST_DATA, $data);
        }

        return $playlist_id;
    }

    public function createPlaylist()
    {

utmdump([__METHOD__,$this->postArray]);
        $playlist_id = $this->addPlaylistData();
        if (!\array_key_exists('playlist', $this->postArray)) {
            return $playlist_id;
        }

        if (!\is_array($this->postArray['playlist'])) {
            $this->postArray['playlist'] = explode(',', $this->postArray['playlist']);
        }

        $existingIds = $this->getExistingVidsFromPl($playlist_id);
        foreach ($this->postArray['playlist'] as $_ => $id) {
            if(array_key_exists($id,$existingIds)) {
                continue;
            }
            $data = [
                'playlist_id' => $playlist_id,
                'playlist_video_id' => $id,
                'library' => $this->library,
            ];
            utmdump($data);
            $ids[] = $this->db->insert(Db_TABLE_PLAYLIST_VIDEOS, $data);
        }
        if (\array_key_exists('PlayAll', $this->postArray) || 
          //  \array_key_exists('AddToPlaylist', $this->postArray) ||
            \array_key_exists('refresh', $this->postArray)
        ) {
            return __URL_HOME__.'/video.php?playlist_id='.$playlist_id.'';
        }
     
        if (\array_key_exists('VideoPlayer', $this->postArray)) {
            if($this->postArray['VideoPlayer'] == 'video'){
                if (\array_key_exists('currentPl', $this->postArray)) {
                    if ('' != $this->postArray['currentPl']) {
                        $playlist_id = $this->postArray['currentPl'];
                    }
                }
                return __URL_HOME__.'/video.php?id='. $id.'&playlist_id='.$playlist_id.'';
            }
            if($this->postArray['VideoPlayer'] == 'grid')
            {
                $videoInfo = (new VideoDb)->getVideoDetails($this->postArray['Video_ID']);
                $videoInfo[0]['rownum'] = $this->postArray["currentId"];
            
                $grid = (new VideoDisplay('Grid'))->init();
                $grid->totalRecords = $this->postArray["total"];
                $html = $grid->videoCell($videoInfo[0]);
                utmdump($videoInfo[0]);
                return $html;
            }

      }
        

        return __URL_HOME__.'/playlist.php?playlist_id='.$playlist_id.'';
    }

    

    public function addPlaylist()
    {
        $data = [
            'playlist_id' => $this->playlist_id,
            'playlist_video_id' => $this->postArray['video_id'],
            'library' => $this->library,
        ];
        $res = $this->db->insert(Db_TABLE_PLAYLIST_VIDEOS, $data);

        return 0;
    }

    public function RemovePlaylistVideo()
    {
        $sql = 'delete FROM '.Db_TABLE_PLAYLIST_VIDEOS.' WHERE playlist_id = '.$this->postArray['playlistid'].' and playlist_video_id = '.$this->postArray['videoId'].'';
        $results = $this->query($sql);

        $this->myHeader(__URL_HOME__.'/video.php?playlist_id='.$this->postArray['playlistid'].'');

    }

    public function deletePlaylist()
    {
        utmdump([__METHOD__,$this->playlist_id]);

        $sql = 'delete d,v from '.Db_TABLE_PLAYLIST_DATA.'  d join '.Db_TABLE_PLAYLIST_VIDEOS.' v on d.id = v.playlist_id where d.id = '.$this->playlist_id.'';
        $results = $this->query($sql);
        $this->myHeader('playlist.php');

        return 0;
    }

    public function savePlaylist()
    {
        utmdump([__METHOD__,$this->postArray]);
        if (isset($this->postArray['playlist_name'])) {
            $playlist_name = $this->postArray['playlist_name'];
            if ('' != $playlist_name) {
                $update[] = " name = '".$playlist_name."' ";
            }
        }

        if (isset($this->postArray['playlist_genre'])) {
            $playlist_genre = $this->postArray['playlist_genre'];
            if ('' != $playlist_genre) {
                $update[] = " genre = '".$playlist_genre."' ";
            }
        }

        if (isset($update)) {
            $update_str = implode(', ', $update);
            $sql = 'UPDATE '.Db_TABLE_PLAYLIST_DATA.' SET '.$update_str.' WHERE id = '.$this->playlist_id.'';
            $results = $this->query($sql);
        }

        if (isset($this->postArray['prune_playlist'])) {
            $video_ids = $this->postArray['prune_playlist'];
            foreach ($video_ids as $_ => $id) {
                $video_id_array[] = $id;
            }
            $video_ids_str = implode(', ', $video_id_array);
            $sql = 'delete from '.Db_TABLE_PLAYLIST_VIDEOS.' where id in ('.$video_ids_str.')';
            $results = $this->query($sql);
        }

        $form_url = __URL_HOME__.'/playlist.php?playlist_id='.$this->playlist_id.'';
        $this->myHeader($form_url);
    }


}
