<?php

require_once '../_config.inc.php';

$json_url   = '/home/bjorn/.config/passwords.json';
$json       = file_get_contents($json_url);
$json_data  = json_decode($json, true);

define('GMAIL_USER', $json_data['username']);
define('GMAIL_PWD', $json_data['password']);
define('MAIL_USERNAME', GMAIL_USER);

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$header_url = __URL_HOME__.'/register.php';

if (isset($_POST['verifysubmit'])) {
    if (!verify_csrf_token()) {
        $_SESSION['STATUS']['verify'] = 'Request could not be validated';
        header('Location: ../');
        exit;
    }

    $header_url = __URL_HOME__.'/verify.php';
}

if (
    isset($_POST['signupsubmit'])
    || isset($_POST['verifysubmit'])) {
    foreach ($_POST as $key => $value) {
        $_POST[$key] = _cleaninjections(trim($value));
    }

    $selector                   = bin2hex(random_bytes(8));
    $token                      = random_bytes(32);
    $url                        = __URL_HOME__.'/includes/verify.inc.php?selector='.$selector.'&validator='.bin2hex($token);

    $expires                    = 'DATE_ADD(NOW(), INTERVAL 1 HOUR)';
    $email                      = $_SESSION['email'];

    $sql                        = "DELETE FROM auth_tokens WHERE user_email=? AND auth_type='account_verify';";
    $stmt                       = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $_SESSION['ERRORS']['sqlerror'] = 'SQL ERROR';
        header('Location: '.$header_url);
        exit;
    }
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);

    $sql                        = "INSERT INTO auth_tokens (user_email, auth_type, selector, token, expires_at)  VALUES (?, 'account_verify', ?, ?, ".$expires.');';
    $stmt                       = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $_SESSION['ERRORS']['sqlerror'] = 'SQL ERROR';
        header('Location: '.$header_url);
        exit;
    }
    $hashedToken                = password_hash($token, \PASSWORD_DEFAULT);
    mysqli_stmt_bind_param($stmt, 'sss', $email, $selector, $hashedToken);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $to                         = $email;
    $subject                    = 'Verify Your Account';

    /*
    * -------------------------------------------------------------------------------
    *   Using email template
    * -------------------------------------------------------------------------------
    */

    $mail_variables             = [];

    $mail_variables['APP_NAME'] = APP_NAME;
    $mail_variables['EMAIL']    = $email;
    $mail_variables['URL']      = $url;

    $message                    = Render::html('auth/email/verify', $mail_variables);

    $mail                       = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Port       = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth   = true;
        $mail->Host       = 'smtp.gmail.com';
        $mail->Username   = GMAIL_USER;
        $mail->Password   = GMAIL_PWD;

        $mail->setFrom(MAIL_USERNAME, APP_NAME);
        $mail->addAddress($to, APP_NAME);

        $mail->isHTML(true);
        $mail->Subject    = $subject;
        $mail->Body       = $message;

        $mail->send();
    } catch (Exception $e) {
    }

    /*
    * ------------------------------------------------------------
    *   Script Endpoint
    * ------------------------------------------------------------
    */
} else {
    header('Location: '.__URL_HOME__);
    exit;
}
