<?php
/**
 * plex web viewer
 */

exit;
define('TITLE', 'Signup');
require_once '_config.inc.php';
check_logged_out();

 \Plex\Template\Layout\Header::Display();

$body = Render::html('auth/register', [
    'CSRF_TOKEN'        => insert_csrf_token(),
    'SESSION_STATUS'    => $_SESSION['STATUS']['signupstatus'],
    'STATUS_NOUSER'     => $_SESSION['ERRORS']['usernameerror'],
    'STATUS_WRONGPASS'  => $_SESSION['ERRORS']['imageerror'],
    'STATUS_EMAILERROR' => $_SESSION['ERRORS']['emailerror'],
    'STATUS_PASSERROR'  => $_SESSION['ERRORS']['passworderror'],
]);
Template::echo('base/page', ['BODY' => $body]);

 \Plex\Template\Layout\Footer::Display();
