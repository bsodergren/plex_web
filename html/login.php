<?php
use Plex\Template\Render;

require_once '_config.inc.php';

$body = Render::html('auth/login', [
    'CSRF_TOKEN'       => insert_csrf_token(),
    'SESSION_STATUS'   => $_SESSION['STATUS']['loginstatus'],
    'STATUS_NOUSER'    => $_SESSION['ERRORS']['nouser'],
    'STATUS_WRONGPASS' => $_SESSION['ERRORS']['wrongpassword'],
]);
Render::Display($body);
