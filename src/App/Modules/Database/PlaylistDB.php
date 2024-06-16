<?php

namespace Plex\Modules\Database;

class PlaylistDB extends VideoDb
{
    public static function getPlaylistVideos($playlist_id)
    {
        $fieldArray = array_merge(self::$VideoMetaFields, self::$VideoFileFields, self::$PlayListFields);

        $sql = 'SELECT ';
        $sql .= implode(',', $fieldArray);
        $sql .= ' FROM '.Db_TABLE_PLAYLIST_VIDEOS.' p ';
        $sql .= ' ,  '.Db_TABLE_VIDEO_FILE.' v  ';
        $sql .= ' INNER JOIN '.Db_TABLE_VIDEO_METADATA.'  m on v.video_key=m.video_key '; // .PlexSql::getLibrary();
        $sql .= ' LEFT JOIN '.Db_TABLE_VIDEO_CUSTOM.'  c on m.video_key=c.video_key ';
        $sql .= ' WHERE  ( p.playlist_id = '.$playlist_id.' and p.playlist_video_id = v.id)';
        utminfo( $sql);

        return PlexSql::$DB->query($sql);
    }

    public static function createPlaylist($name, $genre = 'mmf,mff', $searchId = null, $hide = 0)
    {
        $data = [
            'name' => $name,
            'genre' => $genre,
            'library' => $_SESSION['library'],
            'search_id' => $searchId,
            'hide' => $hide,
        ];

        $plid = PlexSql::$DB->insert(Db_TABLE_PLAYLIST_DATA, $data);
        // utminfo(  PlexSql::$DB->getLastQuery(), $plid);

        return $plid;
    }

    public static function deletePlaylist($playlist_id)
    {
        $sql = 'delete d,v from '.Db_TABLE_PLAYLIST_DATA.'  d
        join '.Db_TABLE_PLAYLIST_VIDEOS.' v on d.id = v.playlist_id where d.id = '.$playlist_id.'';
        $results = PlexSql::$DB->query($sql);

        // utminfo( $results, $playlist_id);

        return 0;
    }

    public static function updatePlaylist($playlist_id, $update)
    {
        $update_str = implode(', ', $update);
        $sql = 'UPDATE '.Db_TABLE_PLAYLIST_DATA.' SET '.$update_str.' WHERE id = '.$playlist_id.'';
        $results = PlexSql::$DB->query($sql);
    }

    public static function addVideo($playlist_id, $video_id)
    {
        $existingIds = self::getExistingVidsFromPl($playlist_id);

        if (\is_array($video_id)) {
            $video_ids = $video_id;
        } else {
            $video_ids[] = $video_id;
        }

        foreach ($video_ids as $_ => $id) {
            if (\array_key_exists($id, $existingIds)) {
                continue;
            }
            $data = [
                'playlist_id' => $playlist_id,
                'playlist_video_id' => $id,
                'library' =>  $_SESSION['library'],
            ];
            // utminfo( $data);
            $ids[] = PlexSql::$DB->insert(Db_TABLE_PLAYLIST_VIDEOS, $data);
        }
    }

    public static function removeVideo($playlist_id, $video_id)
    {
        $sql = 'delete FROM '.Db_TABLE_PLAYLIST_VIDEOS.' WHERE playlist_id = '.$playlist_id.' and playlist_video_id = '.$video_id.'';
        $results = PlexSql::$DB->query($sql);
    }

    public static function getExistingVidsFromPl($playlist_id)
    {
        $existingIds = [];
        if ('' != $playlist_id) {
            PlexSql::$DB->where('playlist_id', $playlist_id);
            $pl_search = PlexSql::$DB->get(Db_TABLE_PLAYLIST_VIDEOS, null, ['playlist_video_id']);
            if (PlexSql::$DB->count > 0) {
                foreach ($pl_search as $k => $row) {
                    $existingIds[$row['playlist_video_id']] = true;
                }
            }
        }

        return $existingIds;
    }
}
