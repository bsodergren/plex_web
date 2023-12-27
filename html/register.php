<?php
/**
 * plex web viewer
 */

exit;
define('TITLE', 'Signup');
require_once '_config.inc.php';
check_logged_out();

require __LAYOUT_HEADER__;

$body = process_template('auth/register', [
    'CSRF_TOKEN'        => insert_csrf_token(),
    'SESSION_STATUS'    => $_SESSION['STATUS']['signupstatus'],
    'STATUS_NOUSER'     => $_SESSION['ERRORS']['usernameerror'],
    'STATUS_WRONGPASS'  => $_SESSION['ERRORS']['imageerror'],
    'STATUS_EMAILERROR' => $_SESSION['ERRORS']['emailerror'],
    'STATUS_PASSERROR'  => $_SESSION['ERRORS']['passworderror'],
]);
template::echo('base/page', ['BODY' => $body]);

require __LAYOUT_FOOTER__;
