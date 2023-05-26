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
if (__HTML_POPUP__ == true) {
    $params['ONLOAD'] = " onLoad=\"popup('/plex_web/logs.php', 'logs',1000,1000)\"";
}
echo process_template('header/header', $params);

if (!defined('NONAVBAR')) {
    require __LAYOUT_NAVBAR__;
}
