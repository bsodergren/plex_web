<?php
require_once("_config.inc.php");
define('TITLE', "Test Page");
//define('BREADCRUMB', ['home' => "home.php"]);
include __LAYOUT_HEADER__;

$body = "hello ##yellow##there hwhat you## doing <br>" . PHP_EOL;
$body .= "hello there !!primary!!hwhat you!! doing";
template::echo("base/page",['BODY' => $body]);



include __LAYOUT_FOOTER__;  ?>