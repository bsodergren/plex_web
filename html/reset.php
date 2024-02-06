<?php

use Plex\Template\Render;
use Plex\Template\Template;

require_once '_config.inc.php';

define('TITLE', 'Login');
 \Plex\Template\Layout\Header::Display();

$selector  = $_GET['selector'];
$validator = $_GET['validator'];
if (isset($validator, $selector)) {
    $body = Render::html('auth/reset', [
        'CSRF_TOKEN'         => insert_csrf_token(),
        'SELECTOR'           => $selector,
        'VALIDATOR'          => $validator,
        'STATUS_RESETSUBMIT' => $_SESSION['STATUS']['resetsubmit'],
        'STATUS_PASSWDERROR' => $_SESSION['ERRORS']['passworderror'],
    ]);
} else {
    $body = Render::html('auth/reset_send', [
        'CSRF_TOKEN'         => insert_csrf_token(),
        'STATUS_RESETSUBMIT' => $_SESSION['STATUS']['resetsubmit'],
        'STATUS_PASSWDERROR' => $_SESSION['ERRORS']['passworderror'],
    ]);
}

Template::echo('base/page', ['BODY' => $body]);

 \Plex\Template\Layout\Footer::Display();

?>






