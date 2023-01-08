<?php

require_once("../_config.inc.php");

define('__NULL_FIELD', "title");

define('TITLE', "Home");

include __LAYOUT_HEADER__;

$order_sort = "  title " . $_SESSION['direction'];
if (isset($_SESSION['sort'])) {
	$order_sort = $_SESSION['sort'] . " " . $_SESSION['direction'];

}
if (isset($_GET['pageno'])) {
	$uri["pageno"] = $_GET['pageno'];
}



?>
    
<main role="main" class="container">
<a href="<?php echo __THIS_PAGE__; ?>">back</a>
<br>
<br>
<?php	

if(isset($_REQUEST['viewstudio']))
{
	$studio = str_replace("-"," ",$_REQUEST['viewstudio']);
	$studio = str_replace("_","/",$studio);
	

    $where = $lib_where  . __NULL_FIELD." IS NULL ";

	$sql = query_builder("select", $where, false, $order_sort, $no_of_records_per_page, $offset);
    logger("all genres", $sql);


	//display_log($sql);
	$results = $db->query($sql);
	echo display_filelist($results, '');

} else {
	
		$sql = "select count(studio) as cnt, studio from ".Db_TABLE_FILEDB." WHERE `".__NULL_FIELD."` IS NULL  GROUP by studio ORDER BY `studio` ASC;";
	$result = $db->query($sql);
	echo "<ul> \n";
	foreach($result as $k => $v )
	{
		
		if($v["studio"] != "" ){
			$cnt = $v["cnt"];
			$studio = str_replace(" ","-",$v['studio']);
			$studio = str_replace("/","_",$studio);
			
			echo "<li><a href='".__THIS_PAGE__."?viewstudio=".$studio."'>".$v["studio"]."</a> (".$cnt.")<br>";
		}
	}
	echo "</ul>";
}
 
 ?>
 </main>
<?php



include __LAYOUT_FOOTER__;  ?>