<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Process\Traits;

use Plex\Modules\Database\PlaylistDB;
use Plex\Modules\Database\VideoDb;
use Plex\Modules\Display\VideoDisplay;

/**
 * plex web viewer.
 */

/**
 * plex web viewer.
 */
trait Playlist
{
    public function addAllPlaylist()
    {
        $url = $this->createPlaylist();
        utmdump([__METHOD__, $url]);
        echo $this->myHeader($url);
    }

    private function addPlaylistData()
    {
        $hide      = 0;
        $search_id = null;

        $name      = 'User Playlist';
        $studio    = [];
        $pl_search = null;

        if (\array_key_exists('substudio', $this->postArray)) {
            $name     = '';
            $studio[] = $this->postArray['substudio'];
        }
        if (\array_key_exists('studio', $this->postArray)) {
            $name     = '';
            $studio[] = $this->postArray['studio'];
        }

        if (\array_key_exists('playlist_name', $this->postArray)) {
            if ('' != $this->postArray['playlist_name']) {
                $name = $this->postArray['playlist_name'];
            }
        }

        if (\array_key_exists('AddToPlaylist', $this->postArray)) {
            return $this->postArray['PlaylistID'];
        }

        utmdump([__METHOD__, $this->postArray]);

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
            $playlist_id = PlaylistDB::createPlaylist(
                $name.implode(' ', $studio),
                'MMF',
                $search_id,
                $hide);
        }
        utmdump([__METHOD__, $playlist_id]);

        return $playlist_id;
    }

    public function deletePlaylist()
    {
        utmdump([__METHOD__, $this->postArray]);
        $playlist_id = $this->postArray['playlist_id'];
        PlaylistDB::deletePlaylist($playlist_id);
        $this->myHeader(__URL_HOME__.'/playlist.php');
    }

    public function createPlaylist()
    {
        utmdump([__METHOD__, $this->postArray]);
        $playlist_id = $this->addPlaylistData();

        if (!\array_key_exists('playlist', $this->postArray)) {
            return $playlist_id;
        }

        if (!\is_array($this->postArray['playlist'])) {
            $this->postArray['playlist'] = explode(',', $this->postArray['playlist']);
        }

        PlaylistDB::addVideo($playlist_id, $this->postArray['playlist']);

        if (\array_key_exists('PlayAll', $this->postArray)
          //  \array_key_exists('AddToPlaylist', $this->postArray) ||
            || \array_key_exists('refresh', $this->postArray)
        ) {
            return __URL_HOME__.'/video.php?playlist_id='.$playlist_id.'';
        }

        if (\array_key_exists('VideoPlayer', $this->postArray)) {
            if ('video' == $this->postArray['VideoPlayer']) {
                if (\array_key_exists('currentPl', $this->postArray)) {
                    if ('' != $this->postArray['currentPl']) {
                        $playlist_id = $this->postArray['currentPl'];
                    }
                }

                return __URL_HOME__.'/video.php?id='.$id.'&playlist_id='.$playlist_id.'';
            }
            if ('grid' == $this->postArray['VideoPlayer']) {
                $videoInfo              = (new VideoDb())->getVideoDetails($this->postArray['Video_ID']);
                $videoInfo[0]['rownum'] = $this->postArray['currentId'];

                $grid               = (new VideoDisplay('Grid'))->init();
                $grid->totalRecords = $this->postArray['total'];
                $html               = $grid->videoCell($videoInfo[0]);
                utmdump($videoInfo[0]);

                return $html;
            }
        }

        return __URL_HOME__.'/playlist.php?playlist_id='.$playlist_id.'';
    }

    public function savePlaylist()
    {
        utmdump([__METHOD__, $this->postArray]);
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
            PlaylistDB::updatePlaylist($this->playlist_id, $update);
        }

        if (isset($this->postArray['prune_playlist'])) {
            $video_ids = $this->postArray['prune_playlist'];
            PlaylistDB::removeVideo($this->playlist_id, $video_ids);
        }

        $form_url = __URL_HOME__.'/playlist.php?playlist_id='.$this->playlist_id.'';
        $this->myHeader($form_url);
    }
}
