<?php
/**
 * Command like Metatag writer for video files.
 */

$params['__LAYOUT_URL__']       = __LAYOUT_URL__;
$params['APP_NAME']             = APP_NAME;

$library_links                  = '';
if (isset($_SESSION['auth']) &&
    $_SESSION['auth'] == 'verified') {

    $sql                            = query_builder('DISTINCT(library) as library ');
    foreach ($db->query($sql) as $k => $v) {
        $library_links .= Render::display_navbar_left_links('home.php?library='.$v['library'], $v['library']);
    }
    $params['NAV_BAR_LEFT_LINKS']   =  process_template('base/navbar/library_menu', ['LIBRARY_SELECT_LINKS' => $library_links]);
    if (defined('BREADCRUMB')) {
        $params['BREADCRUMB']  =   Render::display_breadcrumbs();
    }
}
    $params['NAV_BAR_RIGHT_LINKS']  = Render::display_navbar_links();



echo process_template('base/navbar/main', $params);
