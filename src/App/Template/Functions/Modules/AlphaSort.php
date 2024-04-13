<?php

namespace Plex\Template\Functions\Modules;

use Plex\Core\Request;
use Plex\Core\Utilities\PlexArray;
use Plex\Modules\Display\Display;
use Plex\Template\Render;

/**
 * plex web viewer.
 */
class AlphaSort extends Render
{
    private static $ShowAlpha = false;
    private $templateDir = 'elements/AlphaSort';

    private static $alpha_sort_map = [
        'm.studio', 'm.substudio', 'm.artist', 'm.title', 'v.filename', 'm.genre', 'genre',
    ];

    public function display_alphaSort($offset = 0, $len = 13)
    {
        $html = '';
        $sort = '';
        $current = '';
        $request_uri = '';

        $url_array = Request::$url_array;
        $sep = '&';
        if ('' != $url_array['query_string']) {
            parse_str($url_array['query_string'], $query_parts);

            $current = 'studio';

            if (isset($url_array['direction'])) {
                $query_parts['direction'] = $url_array['direction'];
            }
            if (!isset($query_parts['sort'])) {
                $query_parts['sort'] = $url_array['sortDefault'];
            }

            $sort = $query_parts['sort'];

            if (!PlexArray::matcharray(self::$alpha_sort_map, $sort)) {
                return '';
            }
            // unset($query_parts['sort']);
            if (isset($query_parts['alpha'])) {
                $current = $query_parts['alpha'];
                unset($query_parts['alpha']);
            }
            $request_uri = http_build_query($query_parts);
        }

        if ('' == $sort) {
            $sort = $url_array['current'];
        }

        $request_string = $request_uri; // .'sort='.$sort;

        $i = 0;

        $chars = range('A', 'Z');
        $charrange = array_merge(['#'], $chars, ['None', 'All']);

        $range = \array_slice($charrange, $offset, $len);
        $max = \count($range);

        foreach ($range as $char) {
            $bg = 'btn-primary ';
            $pill = '';
            if (0 == $i) {
                $pill = ' rounded-start-pill';
            }
            ++$i;
            if ($i == $max) {
                $pill = ' rounded-end-pill';
            }

            if ($current == $char) {
                $bg = ' btn-secondary ';
            }
            $class = 'btn btn-sm '.$bg.$pill;
            $url = $url_array['url'].'?alpha='.urlencode($char).$sep;
            $html .=
            Display::directory_navlinks($url, $char, $request_string, $class, 'role="button" aria-pressed="true"  ');
        }

        return $html;
    }

    public function display_AlphaRow()
    {
        $alpha_sort = $this->display_alphaSort(0, 30);

        //   $alpha_end   = $this->display_alphaSort(15, 20);
        return Render::html($this->templateDir.'/row', [
            'ALPHA_BLOCK_START' => $alpha_sort,
            // 'ALPHA_BLOCK_END' => $alpha_end
        ]);
    }

    public function displayAlphaBlock()
    {
        if (OptionIsTrue(ALPHA_SORT)) {
            return Render::html($this->templateDir.'/block', ['ALPHA_BLOCK' => $this->display_AlphaRow()]);
        }

        return '';
    }
}
