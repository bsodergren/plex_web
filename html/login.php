<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';

define('TITLE', 'Login');
 \Plex\Template\Layout\Header::Display();

$body = Render::html('auth/login', [
    'CSRF_TOKEN'       => insert_csrf_token(),
    'SESSION_STATUS'   => $_SESSION['STATUS']['loginstatus'],
    'STATUS_NOUSER'    => $_SESSION['ERRORS']['nouser'],
    'STATUS_WRONGPASS' => $_SESSION['ERRORS']['wrongpassword'],
]);
Template::echo('base/page', ['BODY' => $body]);

 \Plex\Template\Layout\Footer::Display();
