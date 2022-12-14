<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );
require_once("../_config.inc.php");

define('__NULL_FIELD', "genre");

define('TITLE', "Home");

include __LAYOUT_HEADER__;
if (isset($_REQUEST['submit']) ) 
{	
	echo saveData($_REQUEST, __THIS_PAGE__ ); 
	
} else {

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
	
		$sql = "SELECT id,filename,title,artist,genre,studio,substudio from ".Db_TABLE_FILEDB."   WHERE `".__NULL_FIELD."` IS NULL and  studio = \"".$studio."\" ORDER BY `studio`,`filename` ASC";

	//display_log($sql);
	$results = $db->query($sql);

	echo "<table class=blueTable> 
 <form action=".__THIS_PAGE__." method=post id=\"myform\">";
	foreach($results as $id => $row)
	{
		$row_key=$row['id'];
		$row_filename=$row['filename'];
		echo "<thead><tr id=RedHead><td colspan=2><input type=submit name=submit value=save id=\"submit\"> ".$row_filename."</td></tr></thead>";

		foreach($row as $key => $value )
		{
			if ($key == "id" ) {
				continue;
			}
			if ($key == "filename" ) {
				continue;
			}
			
			$default = "placeholder=\"".$value."\"";

			if ($value == "" ){
				$default = "";
			}
			
			
			 $array = array(
				"FIELD_KEY" => $key,
				"FIELD_NAME" =>$row_key."_".$key,
				"VALUE" =>  $default);

		$html =  process_template("metadata_row",$array);
			
		echo $html;
		}
		
		
	}
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

}

include __LAYOUT_FOOTER__;  ?>