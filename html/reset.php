<?php

use Plex\Template\Render;

require_once '_config.inc.php';

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

Render::Display($body);
