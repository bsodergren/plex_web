<?php

namespace Plex\Template\Layout;

use Plex\Template\Functions\Functions;
use Plex\Template\Render;
use Plex\Template\Template;

/**
 * plex web viewer.
 */
class Header
{
    public static function Display()
    {
        if (APP_AUTHENTICATION == true) {
            if (isset($_SESSION['auth'])) {
                $_SESSION['expire'] = ALLOWED_INACTIVITY_TIME;
            }
            generate_csrf_token();
            check_remember_me();

            if (\array_key_exists(basename(__THIS_FILE__, '.php'), __AUTH_FUNCTION__)) {
                __AUTH_FUNCTION__[basename(__THIS_FILE__, '.php')]();
            } else {
                check_verified();
            }
        } else {
            $_SESSION['auth'] = 'verified';
        }
        $params['APP_DESCRIPTION'] = APP_DESCRIPTION;
        $params['APP_OWNER'] = APP_OWNER;
        $params['__URL_HOME__'] = __URL_HOME__;
        $params['TITLE'] = TITLE;
        $params['APP_NAME'] = APP_NAME;
        $params['__LAYOUT_URL__'] = __LAYOUT_URL__;

        if (\defined('GRID_VIEW')) {
            $params['SCRIPTS'] .= Render::html('base/header/header_grid', ['__LAYOUT_URL__' => __LAYOUT_URL__]);
        }

        Template::echo('base/header/header', $params);

        if (!\defined('NONAVBAR')) {
            $crumbs = (new Functions())->createBreadcrumbs();
            \define('BREADCRUMB', $crumbs);

            Navbar::Display($params);
        }
    }
}
