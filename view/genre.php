<?php

require_once("../_config.inc.php");

define('TITLE', "Home");

include __LAYOUT_HEADER__;

	
		$sql = query_builder("count(genre) as cnt, genre",
					"library = '".$in_directory."'",
					"genre","genre asc");
	$result = $db->query($sql);

	
?>
    
    <main role="main" class="container">

    <?php
	
	echo "<ul> \n";
	$allgenre_array=array();
	foreach($result as $k => $v )
	{
		
		if($v["genre"] != "" )
		{
			$genre_array = explode(',', $v["genre"]);
		    foreach ($genre_array as $x => $g) {
			    if (!in_array($g, $allgenre_array)) {
				    array_push($allgenre_array, $g);

			    }
		    }
		}
	}

    
    foreach ($allgenre_array as $x => $g) {

	    $sql = "SELECT count(*) as cnt from metatags_filedb WHERE library = '".$in_directory."' AND genre LIKE '%".$g."%'";

		$rar = $db->rawQueryOne($sql);
		$cnt = '';
		if (isset($rar['cnt'])) {
			$cnt = $rar['cnt'];
		}
			$genre = str_replace(" ","-",$g);
			$genre = str_replace("/","_",$genre);
			
			echo "<li><a href='view/files.php?genre=".$genre."'>".$g."</a> (".$cnt.")<br>";
	}



 ?>
 </ul>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>