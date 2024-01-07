<?php
/**
 * plex web viewer
 */

$params['__LAYOUT_URL__']      = __LAYOUT_URL__;
$params['APP_NAME']            = APP_NAME;

$library_links                 = '';
if (isset($_SESSION['auth'])
    && 'verified' == $_SESSION['auth']) {
    $sql                          = query_builder(Db_TABLE_VIDEO_TAGS, 'DISTINCT(library) as library ');
    foreach ($db->query($sql) as $k => $v) {
        $library_links .= Render::display_navbar_left_links('home.php?library='.$v['library'], $v['library']);
    }
    $library_links .= Render::display_navbar_left_links('home.php?library=All', 'All');

    $library_links .= Render::display_theme_dropdown();

    $params['NAV_BAR_LEFT_LINKS'] = process_template('base/navbar/library_menu', ['LIBRARY_SELECT_LINKS' => $library_links]);
    // if (defined('BREADCRUMB')) {
    //     $params['BREADCRUMB'] = Render::display_breadcrumbs();

    //     // $params['BREADCRUMB']  .=
    // }
}
$params['NAV_BAR_RIGHT_LINKS'] = Render::display_navbar_links();

Template::echo('base/navbar/main', $params);
