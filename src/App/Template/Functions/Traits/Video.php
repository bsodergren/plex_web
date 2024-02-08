<?php
/**
 * plex web viewer
 */

namespace Plex\Template\Functions\Traits;

use Plex\Template\HTML\Elements;
use Plex\Template\Render;

trait Video
{
    public function UseEditable($matches) {}

    public function videoPlayer($matches)
    {
        if (\defined('NONAVBAR')) {
            if (NONAVBAR == true) {
                return '';
            }
        }
            $var        = $this->parseVars($matches);

            if (\is_array($var['query'])) {
                $req = '?'.http_build_query($var['query']);
            }

            $window     = basename($var['href'], '.php').'_popup';
            $url        = __URL_HOME__.'/'.$var['href'].$req;

            $javascript = " onclick=\"popup('".$url."', '".$window."')\"";
            $text       = str_replace('_', ' ', $var['text']);

            $button= Elements::addButton($text, 'button', 'btn btn-primary', '', $javascript);
            return $button;
        
    }

    public function videoRating($matches)
    {
        $hidden = null;
        if (true == \defined('SHOW_RATING')) {
            if (SHOW_RATING == true) {
                $var = $this->parseVars($matches);
                $params = [
                    'ROW_ID' => $var['id'], 
                'STAR_RATING' => $var['rating']
                ];

                if(array_key_exists("close",$var)){
                    $params['RATING_HIDDEN'] = Elements::add_hidden('close','false','id="close_window"');
                }

                return Render::html('elements/Rating/rating', $params);
            }
        }
    }

    public function ratingInclude($matches)
    {
        if (true == \defined('SHOW_RATING')) {
            if (SHOW_RATING == true) {
                return Render::html('elements/Rating/header', []);
            }
        }
    }

    public function Thumbnail($matches)
    {
        $var    = $this->parseVars($matches);
        $row_id = $var['id'];
        if (__SHOW_THUMBNAILS__ == true) {
            $thumbnail         = $this->fileThumbnail($row_id);
            $row_preview_image = $this->filePreview($row_id);
        }

        return Render::html(
            'VideoCard/thumbnail',
            [
                'PREVIEW'   => $row_preview_image,
                'THUMBNAIL' => $thumbnail,
                'FILE_ID'   => $row_id,
                'NEXT_ID'   => $next_id,
            ]
        );
    }

    public function fileThumbnail($row_id, $extra = '')
    {
        global $db;
        $query  = 'SELECT thumbnail FROM metatags_video_file WHERE id = '.$row_id;
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
        $query  = 'SELECT preview FROM metatags_video_file WHERE id = '.$row_id;
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
