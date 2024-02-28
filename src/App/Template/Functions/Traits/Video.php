<?php

namespace Plex\Template\Functions\Traits;

use Plex\Template\Functions\Functions;
use Plex\Template\HTML\Elements;
use Plex\Template\Render;

trait Video
{
    public function UseEditable($matches) {}

    public function videoButton($matches)
    {
        $var = $this->parseVars($matches);
        return Render::html(Functions::$ButtonDir.'/'.$var['template'], $var);
    }

    public function videoPlayer($matches)
    {
    
        $var = $this->parseVars($matches);


        if (\defined('NONAVBAR')) {
            $page = strtolower(str_replace('_', '', $var['text']));
            if (NONAVBAR == true &&
             __THIS_PAGE__ == $page) {
                 return '';
            }
        }

        if (\is_array($var['query'])) {
            $req = '?'.http_build_query($var['query']);
        }

        $window = basename($var['href'], '.php').'_popup';
        $url = __URL_HOME__.'/'.$var['href'].$req;
        $class = 'btn btn-primary';
        $extra = '';
        $javascript = " onclick=\"popup('".$url."', '".$window."')\"";
        $text = str_replace('_', ' ', $var['text']);
        if (__SHOW_THUMBNAILS__ == false) {
            $class = $class. ' position-absolute vertical-text text-nowrap';
            if($text == 'Play Video'){
                $extra = ' style="left: -25px; top:40px"';
            }
            if($text == 'Video Info'){
                $extra = ' style="left: -25px;  top:150px"';
            }
           
        }
        return Elements::addButton($text, 'button', $class, $extra, $javascript);
    }

    public function videoRating($matches)
    {
        $hidden = null;
        if (true == \defined('SHOW_RATING')) {
            if (SHOW_RATING == true) {
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
    }

    public function ratingInclude($matches)
    {
        if (true == \defined('SHOW_RATING')) {
            if (SHOW_RATING == true) {
                return Render::html(Functions::$RatingsDir.'/header', []);
            }
        }
    }

    public function Thumbnail($matches)
    {
        $var = $this->parseVars($matches);
        $row_id = $var['id'];
        $params = ['FILE_ID' => $row_id,
            'NEXT_ID' => $next_id,
            'width' => 60
            ];
        if (__SHOW_THUMBNAILS__ == true) {
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
        global $db;
        $query = 'SELECT thumbnail FROM metatags_video_file WHERE id = '.$row_id;
        $result = $db->query($query);
        if (\defined('NOTHUMBNAIL')) {
            return null;
        }

        return __URL_HOME__.$result[0]['thumbnail'];
        //  return __URL_HOME__.'/images/thumbnail.php?id='.$row_id;
    }

    public function filePreview($row_id, $extra = '')
    {
        global $db;
        $query = 'SELECT preview FROM metatags_video_file WHERE id = '.$row_id;
        $result = $db->query($query);

        if (\defined('NOTHUMBNAIL')) {
            return null;
        }
        if (null === $result[0]['preview']) {
            return null;
        }

        return __URL_HOME__.$result[0]['preview'];
        //  return __URL_HOME__.'/images/thumbnail.php?id='.$row_id;
    }
}
