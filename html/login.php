<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';

define('TITLE', 'Login');
require __LAYOUT_HEADER__;

$body = Template::GetHTML('auth/login', [
    'CSRF_TOKEN'       => insert_csrf_token(),
    'SESSION_STATUS'   => $_SESSION['STATUS']['loginstatus'],
    'STATUS_NOUSER'    => $_SESSION['ERRORS']['nouser'],
    'STATUS_WRONGPASS' => $_SESSION['ERRORS']['wrongpassword'],
]);
template::echo('base/page', ['BODY' => $body]);

require __LAYOUT_FOOTER__;
