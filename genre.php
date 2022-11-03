<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");

define('TITLE', "Home");

include __LAYOUT_HEADER__;

if (isset($_REQUEST['submit']) ) 
{
	echo saveData($_REQUEST, "studio.php?viewstudio=".$_REQUEST['viewstudio'] ); 	
	
} elseif (isset($_REQUEST['genre']) ) 
{
	$genre = $_REQUEST['genre'];

	$studio = str_replace("-"," ",$_REQUEST['viewstudio']);
	$studio = str_replace("_","/",$studio);
	
	
	if ($studio == "NULL" ) {
		$sql_studio= " studio IS NULL ";
	} else {
		
		$sql_studio= " if( (studio = '".$studio."' or studio_a = '".$studio."') and IFNULL(studio_a,1) = 1 ,studio, studio_a ) = '".$studio."'";
	}

	if ($genre == "NULL" ) {
		$sql_genre = " genre IS NULL ";
	} else {
		$sql_genre= " genre LIKE '".$genre."' ";
	}
	
	$order_sort = " ORDER BY title ASC";
	if (isset($_REQUEST['sort']) )
	{
		$order_sort = " ORDER BY ".$_REQUEST['sort']." ASC";	
	}
	
	$sql = "SELECT id,filename,title,artist,genre,studio,studio_a,studio_b,favorite,thumbnail from ".Db_TABLE_FILEDB."  where ". $sql_studio ." and " . $sql_genre . $order_sort;
	
	logger("SQL Query", $sql);
	$results = $db->query($sql);
	 $total_results=count($results);
?>
      
<main role="main" class="container">
<?php echo $total_results; ?> number of files<br>
<a href="studio.php?viewstudio=<?php echo $_REQUEST['viewstudio'] ?>">back</a>
<br>
<br>
<?php echo " <a href='genre.php?viewstudio=".$_REQUEST['viewstudio']."&genre=".$_REQUEST["genre"]."&sort=studio'>Studio</a> - ";
echo " <a href='genre.php?viewstudio=".$_REQUEST['viewstudio']."&genre=".$_REQUEST["genre"]."&sort=title'>Title</a> - ";
echo " <a href='genre.php?viewstudio=".$_REQUEST['viewstudio']."&genre=".$_REQUEST["genre"]."&sort=artist'>artist</a>";
    

	if (isset($_REQUEST['genre']) ) 
{
	echo "<table class=blueTable> 
 <form action=genre.php method=post id=\"myform\">
 <input type='hidden' value='".$_REQUEST['viewstudio']."' name='viewstudio'>";

	foreach($results as $id => $row)
	{
		$row_key=$row['id'];
		$row_filename=$row['filename'];
$array = array("FILE_NAME" => $row_filename);
			echo process_template("metadata_row_header",$array);
$value_array =array();
		foreach($row as $key => $value )
		{
			if ($key == "id" ) {
				continue;
			}
			if ($key == "filename" ) {
				continue;
			}
			if ($key == "thumbnail" ) {
				echo "<th rowspan=6><img src='".$value."'></th>";
				continue;
			}
			
			if ($key == "favorite" )
			{
				$yeschecked = "";
				$nochecked = " checked";


				if ($value == 1) {
					$yeschecked = " checked";
					$nochecked = "";

				}
					 $array = array(
				"FIELD_KEY" => $key,
				"FIELD_NAME" =>$row_key."_".$key,
				"PLACEHOLDER" =>  $placeholder,
				"YESVALUE" => $yeschecked,
				"NOVALUE" => $nochecked);

			$html =  process_template("metadata_favorite_row",$array);
			echo $html;
				
				continue;
			}
			$placeholder = "placeholder=\"".$value."\"";

			if ($value == "" ){
				$placeholder = "";
				switch ($key) {
					case 'artist':
						$value_array = missingArtist($key, $row);
					break;
					case 'title':
						$value_array = missingTitle($key, $row);
					break;

				}
			}
			
			if( isset($value_array[$key][0]) && $value_array[$key][0] != "" )
			{
				$value = " value=\"".$value_array[$key][0]."\"";
			} else {
				$value = "" ;
			}
			
			 $array = array(
				"FIELD_KEY" => $key,
				"FIELD_NAME" =>$row_key."_".$key,
				"PLACEHOLDER" =>  $placeholder,
				"VALUE" => $value);

			$html =  process_template("metadata_row",$array);
				unset($value_array);
		unset($value);
		echo $html;
		}
		
		
	}
	echo "</table>
	</form>";
}
 
 ?>
 </main>
<?php

}

include __LAYOUT_FOOTER__;  ?>