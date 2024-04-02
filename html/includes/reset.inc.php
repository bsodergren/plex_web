<?php

require_once '../_config.inc.php';

check_logged_out();

if (isset($_POST['resetsubmit'])) {
    /*
    * -------------------------------------------------------------------------------
    *   Securing against Header Injection
    * -------------------------------------------------------------------------------
    */

    foreach ($_POST as $key => $value) {
        $_POST[$key] = _cleaninjections(trim($value));
    }

    /*
    * -------------------------------------------------------------------------------
    *   Verifying CSRF token
    * -------------------------------------------------------------------------------
    */

    if (!verify_csrf_token()) {
        $_SESSION['STATUS']['resetsubmit'] = 'Request could not be validated';
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit;
    }

    $selector       = $_POST['selector'];
    $validator      = $_POST['validator'];
    $password       = $_POST['newpassword'];
    $passwordRepeat = $_POST['confirmpassword'];

    if (empty($selector) || empty($validator)) {
        $_SESSION['STATUS']['resentsend'] = 'invalid token, please use new reset email';
        header('Location: '.__URL_HOME__);
        exit;
    }
    if (empty($password) || empty($passwordRepeat)) {
        $_SESSION['ERRORS']['passworderror'] = 'passwords cannot be empty';
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit;
    }
    if ($password != $passwordRepeat) {
        $_SESSION['ERRORS']['passworderror'] = 'passwords donot match';
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit;
    }

    $sql            = "SELECT * FROM auth_tokens WHERE auth_type='password_reset' AND selector=? AND expires_at >= NOW() LIMIT 1";
    $stmt           = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit;
    }

    mysqli_stmt_bind_param($stmt, 's', $selector);
    mysqli_stmt_execute($stmt);
    $results        = mysqli_stmt_get_result($stmt);

    if (!($row = mysqli_fetch_assoc($results))) {
        $_SESSION['STATUS']['resentsend'] = 'non-existent or expired token, please use new reset email';
        header('Location: '.__URL_HOME__);
        exit;
    }

    $tokenBin       = hex2bin($validator);
    $tokenCheck     = password_verify($tokenBin, $row['token']);

    if (false === $tokenCheck) {
        $_SESSION['STATUS']['resentsend'] = 'invalid token, please use new reset email';
        header('Location: '.__URL_HOME__);
        exit;
    }
    if (true === $tokenCheck) {
        $tokenEmail                        = $row['user_email'];

        $sql                               = 'SELECT * FROM users WHERE email=?;';
        $stmt                              = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit;
        }

        mysqli_stmt_bind_param($stmt, 's', $tokenEmail);
        mysqli_stmt_execute($stmt);
        $results                           = mysqli_stmt_get_result($stmt);

        if (!$row = mysqli_fetch_assoc($results)) {
            $_SESSION['STATUS']['resentsend'] = 'invalid token, please use new reset email';
            header('Location: '.__URL_HOME__);
            exit;
        }

        $sql                               = 'UPDATE users SET password=? WHERE email=?;';
        $stmt                              = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit;
        }

        $newPwdHash                        = password_hash($password, \PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, 'ss', $newPwdHash, $tokenEmail);
        mysqli_stmt_execute($stmt);

        $sql                               = "DELETE FROM auth_tokens WHERE user_email=? AND auth_type='password_reset';";
        $stmt                              = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit;
        }

        mysqli_stmt_bind_param($stmt, 's', $tokenEmail);
        mysqli_stmt_execute($stmt);

        $_SESSION['STATUS']['loginstatus'] = 'password updated, please log in';
        header('Location: '.__URL_HOME__);
    }
} else {
    header('Location: '.__URL_HOME__);
    exit;
}
