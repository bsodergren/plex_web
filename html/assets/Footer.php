<?php

use Plex\Template\Render;
use Plex\Template\Template;
/**
 * plex web viewer
 */

$sort_html = '';
$page_html = '';
$navbar    = '';
$js_html   = '';
if (!defined('NONAVBAR')) {
    if (__BOTTOM_NAV__ == 1) {
        if (__SHOW_SORT__ == true && isset($pageObj)) {
            $sort_html = Template::return('base/footer/sort', ['SORT_HTML' => Render::display_sort_options($url_array)]);
        }

        if (__SHOW_PAGES__ == true && isset($pageObj)) {
            $page_html = $pageObj->toHtml();
        }

        $footer_nav = ['FOOTER_NAV' => $sort_html.$page_html];
        $navbar     = Template::return('base/footer/navbar', $footer_nav);
    }
}
Template::echo('base/footer/main', ['FOOT_NAVBAR' => $navbar]);
