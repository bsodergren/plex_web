<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");
define('TITLE', "Test Page");
include __LAYOUT_HEADER__;

?>
    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>





 </main>
 <?php include __LAYOUT_FOOTER__;  ?>