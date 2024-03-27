<?php
namespace Plex\Template\Layout;
/**
 * plex web viewer
 */

use Plex\Template\Render;


class Footer
{
    public static function Display()
    {
        global $pageObj,$url_array;
        $sort_html = '';
        $page_html = '';
        $navbar    = '';
        $js_html   = '';
            if (OptionIsTrue(BOTTOM_NAV)) {
               

                if (OptionIsTrue(SHOW_PAGES) && isset($pageObj)) {
                    $page_html = $pageObj->toHtml();
                }

                $footer_nav = ['FOOTER_NAV' => $page_html];
                $navbar     = Render::html('base/footer/navbar', $footer_nav);
            }
        
        Render::echo('base/footer/main', ['FOOT_NAVBAR' => $navbar]);
    }
}
