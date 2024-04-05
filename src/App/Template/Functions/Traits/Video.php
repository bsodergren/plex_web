<?php

namespace Plex\Template\Functions\Traits;

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Playlist\Playlist;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;
use UTM\Utilities\Option;
use UTMTemplate\HTML\Elements;

trait Video
{
    public function UseEditable($matches) {}

    public function videoButton($matches)
    {
        $var = $this->parseVars($matches);
        // if(array_key_exists('prev_id',$var))
        // {
        //     if($var['prev_id'] == ""){
        //         return null;
        //     }
        // }
        if (\array_key_exists('pl_id', $var)) {
            if ('null' == $var['pl_id']) {
                return null;
            }
        }

        return Render::html(Functions::$ButtonDir.'/'.$var['template'], $var);
    }

    public function videoPlayer($matches)
    {
        $var = $this->parseVars($matches);

        if (OptionIsTrue(NAVBAR)) {
            $page = strtolower(str_replace('_', '', $var['text']));
            if (__THIS_PAGE__ == $page) {
                return '';
            }
        }

        if (\is_array($var['query'])) {
            $req = '?'.http_build_query($var['query']);
        }

        $window = basename($var['href'], '.php').'_popup';
        $url = __URL_HOME__.'/'.$var['href'].$req;
        $class = 'btn btn-primary';
        $extra = ' style="--bs-bg-opacity: .5;"';
        $javascript = " onclick=\"popup('".$url."', '".$window."')\"";
        $text = str_replace('_', ' ', $var['text']);

        if (OptionIsFalse(SHOW_THUMBNAILS)) {
            $class .= ' position-absolute vertical-text text-nowrap';
            if ('Play Video' == $text) {
                $extra = ' style="left: -25px; top:40px"';
            }
            if ('Video Info' == $text) {
                $extra = ' style="left: -25px;  top:150px"';
            }
        }

        return Elements::addButton($text, 'button', $class, $extra, $javascript);
    }


    public function getPlaylistsfromId($id,$playlist_id){
        $results = Playlist::getVideoPlaylists($id);
        $disabled_id=[] ;

        foreach($results as $k => $row)
        {
            if($playlist_id === null){
                if (\array_key_exists('playlist_id', $row)) {
                    $playlist_id = $row['playlist_id'];
                }
            } else {
                $disabled_id[] = $row['playlist_id'];
            }
        }
        $disabled_id_list = implode(",",$disabled_id);

        return [$playlist_id,$disabled_id_list];
    }

    public function getVideoPlaylistJson($id,$playlist_id=null)
    {
        list($playlist_id,$disabled_id_list) = $this->getPlaylistsfromId($id,$playlist_id);
        $playlists = (new Playlist())->getPlaylistJsonOptions($playlist_id,$disabled_id_list);
        $this->playlist_id = $playlist_id;
        return $playlists;
    }

    public  function getVideoPlaylists($id,$playlist_id=null)
    {
        list($playlist_id,$disabled_id_list) = $this->getPlaylistsfromId($id,$playlist_id);
        $playlists = (new Playlist())->getPlaylistSelectOptions($playlist_id,$disabled_id_list);
        $this->playlist_id = $playlist_id;
        return $playlists;

    }



    public function videoAddToPlaylist($matches)
    {
        $var = $this->parseVars($matches);
        $id = $var['id'];
        $playlist_id = $var['pl_id'];

        $playlisthtml = $this->getVideoPlaylists($id,$playlist_id);
        return Render::html(Functions::$PlaylistDir.'/VideoPlayer/PlaylistForm', [
            'playlistId' =>  $this->playlist_id ,
            'SelectPlaylists' => $playlisthtml,
            'Videoid' => $var['id'],
        ]);

    }

    public function videoPlaylistBtn($matches)
    {
        $var = $this->parseVars($matches);
        $buttons = '';
        $results = Playlist::getVideoPlaylists($var['query']['id']);
        foreach ($results as $n => $val) {
            if (\is_array($var['query'])) {
                $req = '?'.http_build_query($var['query']);
            }

            $window = basename($var['href'], '.php').'_popup';
            $url = __URL_HOME__.'/'.$var['href'].$req.$val['playlist_id'];
            $class = 'btn btn-outline-primary';
            $extra = '';
            $javascript = " onclick=\"popup('".$url."', '".$window."')\"";
            $text = Playlist::getPlaylistName($val['playlist_id']);

            $buttons .= Elements::addButton($text, 'button', $class, $extra, $javascript);
        }

        return $buttons;
    }

    public function videoRating($matches)
    {
        $hidden = null;
            if (OptionIsTrue(SHOW_RATING)) {
                $var = $this->parseVars($matches);
                $params = [
                    'ROW_ID' => $var['id'],
                    'STAR_RATING' => $var['rating'],
                ];

                if (\array_key_exists('close', $var)) {
                    $params['RATING_HIDDEN'] = Elements::add_hidden('close', 'false', 'id="close_window"');
                }

                return Render::html(Functions::$RatingsDir.'/rating', $params);
            }

    }

    public function ratingInclude($matches)
    {
            if (OptionIsTrue(SHOW_RATING)) {
                return Render::html(Functions::$RatingsDir.'/header', []);
            }

    }

    public function Thumbnail($matches)
    {
        $next_id = '';
        $var = $this->parseVars($matches);
        $row_id = $var['id'];
        $params = ['FILE_ID' => $row_id,
            'PlaylistId' => $var['playlist_id'],
            'NEXT_ID' => $next_id,
            'width' => 60,
        ];
        if (OptionIsTrue(SHOW_THUMBNAILS)) {
            $thumbnail = $this->fileThumbnail($row_id);

            $row_preview_image = $this->filePreview($row_id);
            $params['width'] = 325;
            $params['Thumbnail_html'] = Render::html(
                'VideoCard/thumbnail',
                [
                    'PREVIEW' => $row_preview_image,
                    'THUMBNAIL' => $thumbnail,
                    'FILE_ID' => $row_id,
                    'NEXT_ID' => $next_id,
                ]
            );
        }

        return Render::html(
            'VideoCard/thumbnail_wrapper',
            $params);
    }

    public function fileThumbnail($row_id, $extra = '')
    {
        $db = PlexSql::$DB;
        if ('' == $row_id) {
            return null;
        }
        $query = 'SELECT thumbnail FROM '.Db_TABLE_VIDEO_FILE.' WHERE id = '.$row_id;
        $result = $db->query($query);
        if (OptionIsFalse(SHOW_THUMBNAILS)) {
            return null;
        }

        return __URL_HOME__.$result[0]['thumbnail'];
        //  return __URL_HOME__.'/images/thumbnail.php?id='.$row_id;
    }

    public function filePreview($row_id, $extra = '')
    {
        $db = PlexSql::$DB;
        if ('' == $row_id) {
            return null;
        }
        $query = 'SELECT preview FROM '.Db_TABLE_VIDEO_FILE.' WHERE id = '.$row_id;
        $result = $db->query($query);

        if (OptionIsFalse(SHOW_THUMBNAILS)) {
            return null;
        }
        if (null === $result[0]['preview']) {
            return null;
        }

        return __URL_HOME__.$result[0]['preview'];
        //  return __URL_HOME__.'/images/thumbnail.php?id='.$row_id;
    }
}
