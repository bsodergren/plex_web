<?php

require_once '../_config.inc.php';

check_logged_out();

if (isset($_POST['signupsubmit'])) {
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
        $_SESSION['STATUS']['signupstatus'] = 'Request could not be validated';
        header('Location: ../register.php');
        exit;
    }

    // filter POST data
    function input_filter($data)
    {
        $data = trim($data);
        $data = stripslashes($data);

        return htmlspecialchars($data);
    }

    $username                          = input_filter($_POST['username']);
    $email                             = input_filter($_POST['email']);
    $password                          = input_filter($_POST['password']);
    $passwordRepeat                    = input_filter($_POST['confirmpassword']);
    $headline                          = input_filter($_POST['headline']);
    $bio                               = input_filter($_POST['bio']);
    $full_name                         = input_filter($_POST['first_name']);
    $last_name                         = input_filter($_POST['last_name']);

    if (isset($_POST['gender'])) {
        $gender = input_filter($_POST['gender']);
    } else {
        $gender = null;
    }

    /*
    * -------------------------------------------------------------------------------
    *   Data Validation
    * -------------------------------------------------------------------------------
    */

    if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
        $_SESSION['ERRORS']['formerror'] = 'required fields cannot be empty, try again';
        header('Location: ../register.php');
        exit;
    }
    if (!preg_match('/^[a-zA-Z0-9]*$/', $username)) {
        $_SESSION['ERRORS']['usernameerror'] = 'invalid username';
        header('Location: ../register.php');
        exit;
    }
    if (!filter_var($email, \FILTER_VALIDATE_EMAIL)) {
        $_SESSION['ERRORS']['emailerror'] = 'invalid email';
        header('Location: ../register.php');
        exit;
    }
    if ($password !== $passwordRepeat) {
        $_SESSION['ERRORS']['passworderror'] = 'passwords donot match';
        header('Location: ../register.php');
        exit;
    }

    if (!availableUsername($conn, $username)) {
        $_SESSION['ERRORS']['usernameerror'] = 'username already taken';
        header('Location: ../register.php');
        exit;
    }
    if (!availableEmail($conn, $email)) {
        $_SESSION['ERRORS']['emailerror'] = 'email already taken';
        header('Location: ../register.php');
        exit;
    }

    /*
    * -------------------------------------------------------------------------------
    *   Image Upload
    * -------------------------------------------------------------------------------
    */

    $FileNameNew                       = '_defaultUser.png';

    /*
    * -------------------------------------------------------------------------------
    *   User Creation
    * -------------------------------------------------------------------------------
    */

    $sql                               = 'insert into users(username, email, password, first_name, last_name, gender,
                headline, bio, profile_image, created_at)
                values ( ?,?,?,?,?,?,?,?,?, NOW() )';
    $stmt                              = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
        header('Location: ../register.php');
        exit;
    }

    $hashedPwd                         = password_hash($password, \PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($stmt, 'sssssssss', $username, $email, $hashedPwd, $full_name, $last_name, $gender, $headline, $bio, $FileNameNew);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    /*
    * -------------------------------------------------------------------------------
    *   Sending Verification Email for Account Activation
    * -------------------------------------------------------------------------------
    */

    require 'sendverificationemail.inc.php';

    $_SESSION['STATUS']['loginstatus'] = 'Account Created, please Login';
    header('Location: ../login.php');
    exit;

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    header('Location: ../');
    exit;
}
