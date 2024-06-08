<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Display;

use Plex\Modules\Database\PlexSql;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;
use UTMTemplate\UtmDevice;

class Layout
{
    public static function Header($body = false)
    {
        $params = [];
        if (OptionIsTrue(GRID_VIEW)) {
            $params['SCRIPTS'] .= Render::html('base/header/header_grid', ['__LAYOUT_URL__' => __LAYOUT_URL__]);
        }
        Render::echo('base/header/header', $params);

        if (OptionIsTrue(NAVBAR)) {
            $crumbs = (new Functions())->createBreadcrumbs();
            \define('BREADCRUMB', $crumbs);
            self::Navbar($params);
        }
        if (true === $body) {
            Render::echo('base/push', []);
        }
    }

    public static function Navbar($params)
    {
        $db            = PlexSql::$DB;
        $library_links = '';
        $sql           = PlexSql::query_builder(Db_TABLE_VIDEO_METADATA, 'DISTINCT(Library) as Library ');
        foreach ($db->query($sql) as $k => $v) {
            $library_links .= Display::navbar_left_links('home.php?library='.$v['Library'], $v['Library']);
        }
        $library_links .= Display::navbar_left_links('home.php?library=All', 'All');
        $params['CURRENT_DEVICE'] = UtmDevice::$DEVICE;
        $params['Device']         = ucfirst(strtolower(UtmDevice::$DEVICE));

        $params['NAV_BAR_LEFT_LINKS'] = Render::html('base/navbar/library_menu',
            ['LIBRARY_SELECT_LINKS' => $library_links]);
        Render::echo('base/navbar/main', $params);
    }

    public static function Footer()
    {
        global $pageObj;
        $params    = [];
        $page_html = '';
        $navbar    = '';
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
