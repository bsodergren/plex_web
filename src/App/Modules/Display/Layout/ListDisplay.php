<?php

namespace Plex\Modules\Display\Layout;

use Plex\Template\Render;
use Plex\Modules\VideoCard\VideoCard;
use Plex\Modules\Display\VideoDisplay;

class ListDisplay extends VideoDisplay
{
    public $showVideoDetails = false;
    private $template_base   = '';

    public function __construct($template_base = 'filelist')
    {

        $this->template_base = $template_base;
    }

    public function display($results, $page_array = [])
    {
       
        $total_files     = '';

        if (isset($page_array['total_files'])) {
            $total_files = $page_array['total_files'];
        }

        if (isset($page_array['redirect_string'])) {
            $redirect_string = $page_array['redirect_string'];
        }

        $videoinfo       = new VideoCard();
        foreach ($results as $id => $row) {
            $row_id       = $row['id'];
            $row['next']  = 0;
            if (\array_key_exists($id + 1, $results)) {
                $row['next'] = $results[$id + 1]['id'];
            }

            $table_body[] = $videoinfo->VideoInfo($row, $total_files);
        }
        foreach ($table_body as $key => $value) {
            $videohtml['BODY'] .= $value['VIDEO'];

            $videohtml['HIDDEN_STUDIO'] = $value['HIDDEN_STUDIO'];

            $videohtml['VIDEO_KEY']     = $value['VIDEO_KEY'];
        }

       return  Render::html($this->template_base.'/page', $videohtml);
//        return $videohtml; // .$javascript_html;
    } // end display_filelist()
}
