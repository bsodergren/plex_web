<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");

define('TITLE', "Home");

include __LAYOUT_HEADER__;


$studio = str_replace("-"," ",$_REQUEST['viewstudio']);
$studio = str_replace("_","/",$studio);

if ($studio == "NULL" ) {
	$sql = "SELECT DISTINCT(`".Db_TABLE_FILEDB."`.`genre`) as genre from ".Db_TABLE_FILEDB."  WHERE `studio` IS NULL ORDER BY `genre` ASC";
} else {
	$sql = "SELECT DISTINCT(`".Db_TABLE_FILEDB."`.`genre`) as genre from ".Db_TABLE_FILEDB."  where studio like '".$studio."' or studio_a like '".$studio."'";
}

//display_log($sql);
$result = $db->query($sql);

	
?>
    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>
<a href='genre.php?viewstudio=<?php echo $_REQUEST['viewstudio']; ?>&genre=NULL'>Genre not Set</a><br>

<?php


foreach($result as $k => $v )
{
if($v["genre"] != "" ){
	echo $studio." <a href='genre.php?viewstudio=".$_REQUEST['viewstudio']."&genre=".$v["genre"]."'>".$v["genre"]."</a><br>";
}
}

 ?>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>