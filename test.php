<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");

define('TITLE', "New");


	$sql = "SELECT artist from ".Db_TABLE_FILEDB."  where library = '".$in_directory."' ";


//display_log($sql);
$result = $db->query($sql);

	
?>
    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>
<?php

$full_names_array=array();

	foreach($result as $id => $artist)
	{
		$artist_name=$artist["artist"];
		if($artist_name != null)
		{
			if(str_contains($artist_name, ",") == true ) 
			{
				$names_arr = explode(",",$artist_name);
				$names_list="";
				
				foreach( $names_arr as $str_name )
				{
					if (!in_array($str_name, $full_names_array))
					{
						$full_names_array[] = $str_name;
					}
				}
			} else {
				if (!in_array($artist_name, $full_names_array))
					{
						$full_names_array[] = $artist_name;
					}
			}
		}
	}
		asort($full_names_array);
	echo "<ul>";
	foreach($full_names_array as $id => $name)
	{

		echo "<li> $name </li>";
	}
				echo "</ul>";

	


 ?>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>