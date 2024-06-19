<?php
/**
 *  Plexweb
 */

namespace Plex\Template\Functions\Traits;

use Plex\Core\Request;
use Plex\Template\Render;

trait PageSort
{
    private static $PageSortDir = 'elements/PageSort';

    public static function sort_options()
    {
        global $pageObj;
        $url_array   = Request::$url_array;
        $html        = '';
        $request_uri = '';
        $sep         = '?';
        $current     = '';
        if ('' != $url_array['query_string']) {
            parse_str($url_array['query_string'], $query_parts);
            unset($query_parts['alpha']);
            $current = 'studio';

            if (isset($url_array['direction'])) {
                $query_parts['direction'] = $url_array['direction'];
            }

            if (isset($query_parts['sort'])) {
                $current = $query_parts['sort'];
                unset($query_parts['sort']);
            }

            $request_uri = '?'.http_build_query($query_parts);
            $sep         = '&';
        }
        $i   = 0;
        $max = \count($url_array['sort_types']);
        foreach ($url_array['sort_types'] as $key => $value) {
            $bg   = '';
            $pill = '';
            if (0 == $i) {
                //   $pill = ' rounded-start-pill';
            }
            ++$i;
            if ($i == $max) {
                //  $pill = ' rounded-end-pill';
            }

            if ($current == $value) {
                //    $bg = ' active';
            }
            $class          = 'nav-link text-light'.$bg; // .$pill;
            $request_string = $request_uri.$sep.'sort='.$value;

            $html .= Render::return(self::$PageSortDir.'/page_item',
                ['LI_CLASS'   => 'nav-item',
                    'A_CLASS' => $class,
                    'A_HREF'  => $url_array['url'].$request_string,
                    'A_TEXT'  => $key]);
        }

        return $html;
    } // end sort_options()

    public function displayPageSorter()
    {
        global $pageObj,$url_array;
        if (OptionIsTrue(SHOW_SORT) && isset($pageObj)) {
            return Render::return(self::$PageSortDir.'/sort',
                ['SORT_HTML' => self::sort_options()]);
        }
    }
}
