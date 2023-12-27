<?php
/**
 * plex web viewer
 */

function _cleaninjections($test)
{
    $find = [
        "/[\r\n]/",
        '/%0[A-B]/',
        '/%0[a-b]/',
        '/bcc\\:/i',
        '/Content\\-Type\\:/i',
        '/Mime\\-Version\\:/i',
        '/cc\\:/i',
        '/from\\:/i',
        '/to\\:/i',
        '/Content\\-Transfer\\-Encoding\\:/i',
    ];

    return preg_replace($find, '', $test);
}

function generate_csrf_token()
{
    global $_SESSION;

    if (!isset($_SESSION)) {
        session_start();
    }

    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
}

function insert_csrf_token()
{
    global $_SESSION;

    generate_csrf_token();

    return '<input type="hidden" name="token" value="'.$_SESSION['token'].'" />';
}

function verify_csrf_token()
{
    global $_SESSION,$_POST;
    generate_csrf_token();

    if (!empty($_POST['token'])) {
        if (hash_equals($_SESSION['token'], $_POST['token'])) {
            return true;
        }

        return false;
    }

    return false;
}
