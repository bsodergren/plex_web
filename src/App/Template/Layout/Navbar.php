<?php

namespace Plex\Template\Layout;

use Plex\Template\Render;

use Plex\Modules\Database\PlexSql;
use Plex\Template\Display\Display;

/**
 * plex web viewer.
 */
class Navbar
{
    public static function Display($params)
    {
        global $db;
        $params['__LAYOUT_URL__'] = __LAYOUT_URL__;
        $params['APP_NAME'] = APP_NAME;

        $library_links = '';
        if (isset($_SESSION['auth'])
            && 'verified' == $_SESSION['auth']) {
            $sql = PlexSql::query_builder(Db_TABLE_VIDEO_TAGS, 'DISTINCT(library) as library ');
            foreach ($db->query($sql) as $k => $v) {
                $library_links .= Display::navbar_left_links('home.php?library='.$v['library'], $v['library']);
            }
            $library_links .= Display::navbar_left_links('home.php?library=All', 'All');

            $params['NAV_BAR_LEFT_LINKS'] = Render::html('base/navbar/library_menu',
                ['LIBRARY_SELECT_LINKS' => $library_links]);
        }

        Render::echo('base/navbar/main', $params);
    }
}
