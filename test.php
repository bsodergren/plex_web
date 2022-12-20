<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");
/* 
$url_array = array(
	"url" => $_SERVER['PHP_SELF'],
	"rq_key" => "genre",
	"rq_value" => $_REQUEST["genre"],
	"direction" => $_SESSION['direction'],
	"sort_types" => array(
		"Studio" => "studio",
		"artist" => "artist",
		"filename" => "filename",
		"title" => "title",	
		"Duration" => "Duration")
);
*/
define('TITLE', "Test Page");
$redirect_string = "files.php";
include __LAYOUT_HEADER__;
?>
    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>

<?php



logger("all files", $sql);


$results = $db->query($sql);

print_r2($results);

 ?>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>