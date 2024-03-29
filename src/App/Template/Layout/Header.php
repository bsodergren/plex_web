<?php

namespace Plex\Template\Layout;

use Plex\Template\Render;
use UTM\Utilities\Option;
use Plex\Template\Functions\Functions;


/**
 * plex web viewer.
 */
class Header
{
    public static function Display()
    {
        
        // $params['APP_DESCRIPTION'] = APP_DESCRIPTION;
        // $params['APP_OWNER'] = APP_OWNER;
        // $params['__URL_HOME__'] = __URL_HOME__;
        // $params['TITLE'] = TITLE;
        // $params['APP_NAME'] = APP_NAME;
        // $params['__LAYOUT_URL__'] = __LAYOUT_URL__;

        if (OptionIsTrue(GRID_VIEW)
            ) {
            $params['SCRIPTS'] .= Render::html('base/header/header_grid', ['__LAYOUT_URL__' => __LAYOUT_URL__]);
        }

        Render::echo('base/header/header', $params);

        if (OptionIsTrue(NAVBAR)) {
            $crumbs = (new Functions())->createBreadcrumbs();
            \define('BREADCRUMB', $crumbs);

            Navbar::Display($params);
        }
    }
}
