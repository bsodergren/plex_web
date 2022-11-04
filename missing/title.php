<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );
require_once("../_config.inc.php");

define('__NULL_FIELD', "title");

define('TITLE', "Home");

include __LAYOUT_HEADER__;


echo doRequest('save','saveData',0,__THIS_PAGE__);

echo doRequest('delete','deleteEntry',0,__THIS_PAGE__);


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
	
		$sql = "SELECT id,filename,title,artist,genre,studio,substudio,studio_b from ".Db_TABLE_FILEDB."   WHERE `".__NULL_FIELD."` IS NULL and  studio = \"".$studio."\" ORDER BY `studio`,`filename` ASC";

	//display_log($sql);
	$results = $db->query($sql);

	echo "<table class=blueTable> 
 <form action=".__THIS_PAGE__." method=post id=\"myform\">";
	echo showFileRow($results);
	echo "</table>
	</form>";
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