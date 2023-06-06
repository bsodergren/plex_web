<?php


$params['APP_DESCRIPTION'] = APP_DESCRIPTION;
$params['APP_OWNER'] = APP_OWNER;
$params['__URL_HOME__'] = __URL_HOME__;
$params['TITLE'] = TITLE;
$params['APP_NAME'] = APP_NAME;
$params['__LAYOUT_URL__'] = __LAYOUT_URL__;



$params['SCRIPTS'] = process_template('header/header_scripts', $params);

if (!defined('VIDEOINFO')) {

    $params['SCRIPTS'] .= process_template('header/header_filelist', ['__LAYOUT_URL__' => __LAYOUT_URL__]);
} else {
    $params['SCRIPTS'] .= process_template('header/header_videoinfo', ['__LAYOUT_URL__' => __LAYOUT_URL__]);
}

if (defined('GRID_VIEW')) {
    $params['SCRIPTS'] .= process_template('header/header_grid', ['__LAYOUT_URL__' => __LAYOUT_URL__]);

}

echo process_template('header/header', $params);

if (!defined('NONAVBAR')) {

    $crumbs['Home'] = "home.php";
    $crumbs[$in_directory] = "";

    if(key_exists('studio',$_REQUEST)) {
        $crumbs[$_REQUEST['studio']] = '';

    }
    if(key_exists('substudio',$_REQUEST)) {
        $crumbs[$_REQUEST['substudio']] = '';

    }
    if(key_exists('prev',$_REQUEST)) {
        $crumbs[$_REQUEST['prev']]= $studio_url;
    }

    if(isset($genre_url)) {
        $crumbs['Genre'] = $genre_url;
    }

//    $crumbs['Grid'] = "";
    if(isset($gridview_url)) {
        $crumbs['Grid'] = $gridview_url;
    }

    //$crumbs['List'] = "";
    if(isset($filelist_url)) {
        $crumbs['List'] = $filelist_url;
    }

    

    //$crumbs['All'] = "";
    if(isset($all_url)) {
        $crumbs['All'] = $all_url;
    }

    DEFINE('BREADCRUMB', $crumbs);



    require __LAYOUT_NAVBAR__;
}
