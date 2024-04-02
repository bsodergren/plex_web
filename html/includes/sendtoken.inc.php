<?php

require_once '../_config.inc.php';

check_logged_out();

$json_url  = '/home/bjorn/.config/passwords.json';
$json      = file_get_contents($json_url);
$json_data = json_decode($json, true);

define('GMAIL_USER', $json_data['username']);
define('GMAIL_PWD', $json_data['password']);
define('MAIL_USERNAME', GMAIL_USER);

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

if (isset($_POST['resentsend'])) {
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
        $_SESSION['STATUS']['resentsend'] = 'Request could not be validated';
        header('Location: '.__URL_HOME__);
        exit;
    }

    $selector                         = bin2hex(random_bytes(8));
    $token                            = random_bytes(32);
    // $url = "localhost/loginsystem/reset-password/?selector=" . $selector . "&validator=" . bin2hex($token);
    $url                              = __URL_HOME__.'/reset.php?selector='.$selector.'&validator='.bin2hex($token);

    $expires                          = 'DATE_ADD(NOW(), INTERVAL 1 HOUR)';

    $email                            = $_POST['email'];

    $sql                              = 'SELECT id FROM users WHERE email=?;';
    $stmt                             = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $_SESSION['ERRORS']['sqlerror'] = 'SQL ERROR';
        header('Location: '.__URL_HOME__);
        exit;
    }

    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (0 == mysqli_stmt_num_rows($stmt)) {
        $_SESSION['ERRORS']['emailerror'] = 'given email does not exist in our records';
        header('Location: '.__URL_HOME__);
        exit;
    }

    $sql                              = "DELETE FROM auth_tokens WHERE user_email=? AND auth_type='password_reset';";
    $stmt                             = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $_SESSION['ERRORS']['sqlerror'] = 'SQL ERROR';
        header('Location: '.__URL_HOME__);
        exit;
    }

    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);

    $sql                              = "INSERT INTO auth_tokens (user_email, auth_type, selector, token, expires_at)
            VALUES (?, 'password_reset', ?, ?, ".$expires.');';
    $stmt                             = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $_SESSION['ERRORS']['sqlerror'] = 'SQL ERROR';
        header('Location: '.__URL_HOME__);
        exit;
    }

    $hashedToken                      = password_hash($token, \PASSWORD_DEFAULT);
    mysqli_stmt_bind_param($stmt, 'sss', $email, $selector, $hashedToken);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $to                               = $email;
    $subject                          = 'Reset Your Password';

    /*
    * -------------------------------------------------------------------------------
    *   Using email template
    * -------------------------------------------------------------------------------
    */

    $mail_variables                   = [];

    $mail_variables['APP_NAME']       = APP_NAME;
    $mail_variables['email']          = $email;
    $mail_variables['url']            = $url;
    $message                          = file_get_contents('/home/bjorn/www/plex_web/html/includes/template_passwordresetemail.php');

    foreach ($mail_variables as $key => $value) {
        $message = str_replace('{{ '.$key.' }}', $value, $message);
    }

    $mail                             = new PHPMailer(true);

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
        // for public use
        $_SESSION['STATUS']['mailstatus'] = 'message could not be sent, try again later';

        // for development use
        // $_SESSION['STATUS']['mailstatus'] = 'message could not be sent. ERROR: ' . $mail->ErrorInfo;

        header('Location: '.__URL_HOME__);
        exit;
    }

    $_SESSION['STATUS']['resentsend'] = 'verification email sent';
    header('Location: '.__URL_HOME__);
    exit;
}

header('Location: '.__URL_HOME__);
exit;
