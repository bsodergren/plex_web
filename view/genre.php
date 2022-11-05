<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

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
	foreach($result as $k => $v )
	{
		
		if($v["genre"] != "" )
		{
			$cnt = $v["cnt"];			
		
			$genre = str_replace(" ","-",$v['genre']);
			$genre = str_replace("/","_",$genre);
			
			echo "<li><a href='view/files.php?genre=".$genre."'>".$v["genre"]."</a> (".$cnt.")<br>";

		}
	}

 ?>
 </ul>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>