<?php

namespace Plex\Modules\VideoCard;

use Plex\Template\Render;
use Plex\Template\HTML\Elements;
use Plex\Modules\Chapter\Chapter;
use Plex\Modules\VideoCard\Traits\VideoRow;

/**
 * plex web viewer.
 */

/**
 * plex web viewer.
 */
class VideoCard
{
    use VideoRow;

    public $showVideoDetails = false;
    private $template_base = 'VideoCard';
    public object $Chapters;

    public function __construct() {}

    public function __call($method, $args)
    {
        $key = $args[0];

        return $this->info($key);
    }

    // $this->fileRowfs($params, ucfirst($key), display_size($value),$duration, $class);

    public function VideoInfo($fileInfoArray, $total_files)
    {
        global $db;

        $this->fileInfoArray = $fileInfoArray;
        $this->params = [];
        $table_body_html = [];
        $row_id = $fileInfoArray['id'];
        $this->Chapters = new Chapter(['id' => $row_id]);
        // $row_filename = $row_id.":".$row['filename'];
        $row_filename = $fileInfoArray['filename'];
        $row_fullpath = $fileInfoArray['fullpath'];
        $row_video_key = $fileInfoArray['video_key'];
        $this->params['video_key'] = $row_video_key;

        if (isset($fileInfoArray['rownum'])) {
            $result_number = $fileInfoArray['rownum'];
        }

        $this->params['VIDEO_TITLE'] = $row_filename;
        if ($fileInfoArray['title']) {
            $this->params['VIDEO_TITLE'] = $fileInfoArray['title'];
        }
        $this->params['ROW_ID'] = '';
        if (!\defined('NONAVBAR')) {
            $this->params['VERTICAL_TEXT'] = Render::html(
                $this->template_base.'/vertical',
                ['ROW_ID' => '&nbsp;&nbsp;&nbsp;'.$result_number.' of '.$total_files]
            );
        }

        // $this->params['DELETE_ID']          = 'delete_'.$row_id;
        $this->params['FILE_NAME_ID'] = $row_id.'_filename';
        $this->params['FULL_PATH'] = $row_fullpath;
        $this->params['FILE_ID'] = $row_id;
        $this->params['WRAPPER_CLASS'] = 'm-3';
        $this->params['RATING_WIDTH'] = 365;

        if (\defined('NONAVBAR')) {
            $this->params['WRAPPER_CLASS'] = 'm-0';
            $this->params['RATING_WIDTH'] = 175;
            $this->params['DELETE_BUTTONS'] = Render::html(
                $this->template_base.'/deletebuttons',
                ['DELETE_ID' => Elements::add_hidden('id', $row_id, 'id="DorRvideoId"')]);
        }

        $fileArray = [
            'rating',
            'title',
            'artist',
            'genre',
            'studio',
            'substudio',
            'keyword',
            'Chapters',
            'library',
            'fullpath',
            'filesize',
            'format',
            'added',
        ];
        $x = 0;
        foreach ($fileArray as $field) {
            $this->AltClass = (0 == $x % 2) ? 'text-bg-primary' : 'text-bg-secondary';

            if (\array_key_exists($field, $this->fileInfoArray)) {
                if (null === $this->fileInfoArray[$field]) {
                    $this->fileInfoArray[$field] = '';
                }
                $method = ucfirst($field);
                $this->{$method}($field);
                ++$x;
            }
        }

        // dd($this->params['HIDDEN_STUDIO']);
        $table_body_html['VIDEO'] = Render::html($this->template_base.'/Video', $this->params);
        $table_body_html['VIDEO_KEY'] = $row_video_key;

        return $table_body_html;
    }
}