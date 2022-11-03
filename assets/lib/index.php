<?php

DEFINE("__APPLICATION_HOME__",str_replace( "/".basename($_SERVER['PHP_SELF']), "", $_SERVER['PHP_SELF']));
DEFINE("__APPLICATION_FULLPATH__", $_SERVER['DOCUMENT_ROOT'] . __APPLICATION_HOME__ );
DEFINE("__PHP_ASSETS_DIR__", __APPLICATION_FULLPATH__."/assets");
require_once(__PHP_ASSETS_DIR__."/header.inc.php");




if (isset($_SESSION['auth'])) {

    header("Location: ".__URL_HOME__."/home");
    exit();
}
else {

    header("Location: ".__URL_HOME__."/login");
    exit();
}
