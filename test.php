<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");


	$sql = "SELECT id,name from ".Db_TABLE_STUDIO." limit 10 ";


//display_log($sql);
$result = $db->query($sql);

define('TITLE', "Test Page");

include __LAYOUT_HEADER__;
?>
    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>
<?php

$full_names_array=array();

	echo "<ul>";
	
	foreach($result as $id => $artist)
	{
		$job_id=$artist["id"];
		$name=$artist["name"];
		echo "<li>$job_id $name </li>";
	}
	
	echo "</ul>";

	


 ?>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>