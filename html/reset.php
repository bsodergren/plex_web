<?php

require_once '_config.inc.php';

define('TITLE', 'Login');
require __LAYOUT_HEADER__;

$selector  = $_GET['selector'];
$validator = $_GET['validator'];
if (isset($validator, $selector)) {
    $body = process_template('auth/reset', [
        'CSRF_TOKEN'         => insert_csrf_token(),
        'SELECTOR'           => $selector,
        'VALIDATOR'          => $validator,
        'STATUS_RESETSUBMIT' => $_SESSION['STATUS']['resetsubmit'],
        'STATUS_PASSWDERROR' => $_SESSION['ERRORS']['passworderror'],
    ]);
} else {
    $body = process_template('auth/reset_send', [
        'CSRF_TOKEN'         => insert_csrf_token(),
        'STATUS_RESETSUBMIT' => $_SESSION['STATUS']['resetsubmit'],
        'STATUS_PASSWDERROR' => $_SESSION['ERRORS']['passworderror'],
    ]);
}

template::echo('base/page', ['BODY' => $body]);

require __LAYOUT_FOOTER__;

?>






