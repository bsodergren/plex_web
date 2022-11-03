<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");

define('TITLE', "New");

if (isset($_REQUEST['submit']) ) 
{
	echo saveData($_REQUEST, "home.php" ); 	
	exit;	
}

if (isset($_REQUEST['delete']) ) 
{

	
	echo deleteEntry($_REQUEST, "new.php" ); 	
	exit;
	
}

include __LAYOUT_HEADER__;
$days=1;

if (isset($_REQUEST['days']) ) 
{
	$days=$_REQUEST['days'];
}

	$sql = "SELECT id,filename,title,artist,genre,studio,studio_a,studio_b from ".Db_TABLE_FILEDB."  where  library = '".$in_directory."' and `added` >= (CURRENT_TIMESTAMP - INTERVAL $days day);";


//display_log($sql);
$result = $db->query($sql);

	
?>
    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>
<?php





	foreach($result as $id => $row)
	{
		$row_key=$row['id'];
		$row_filename=$row['filename'];
		
		echo "<form action=new.php method=post id=\"myform\">
	<table class=blueTable>";
## <input type='hidden' value='".$_REQUEST['viewstudio']."' name='viewstudio'>";


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
		
	echo "
	</table>
</form>
<p>
";
	}


 ?>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>