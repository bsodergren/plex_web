<?php
namespace Plex\Template\Layout;
/**
 * plex web viewer
 */

use Plex\Template\Render;
use Plex\Template\Display\Display;


class Footer
{
    public static function Display()
    {
        global $pageObj,$url_array;
        $sort_html = '';
        $page_html = '';
        $navbar    = '';
        $js_html   = '';
        if (!defined('__BOTTOM_NAV__')) {
            define('__BOTTOM_NAV__', true);
        }
        if (!defined('__SHOW_SORT__')) {
            define('__SHOW_SORT__', true);
        }
        if (!defined('__SHOW_PAGES__')) {
            define('__SHOW_PAGES__', true);
        }
        if (!defined('NONAVBAR')) {
            if (__BOTTOM_NAV__ == 1) {
               

                if (__SHOW_PAGES__ == true && isset($pageObj)) {
                    $page_html = $pageObj->toHtml();
                }

                $footer_nav = ['FOOTER_NAV' => $page_html];
                $navbar     = Render::html('base/footer/navbar', $footer_nav);
            }
        }
        Render::echo('base/footer/main', ['FOOT_NAVBAR' => $navbar]);
    }
}
