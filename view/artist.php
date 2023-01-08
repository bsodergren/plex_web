<?php

require_once("../_config.inc.php");

define('TITLE', "Home");

include __LAYOUT_HEADER__;

	
		
	$sql = "select  artist from ".Db_TABLE_FILEDB." WHERE library = '".$in_directory."' GROUP by artist ORDER BY `artist` ASC;";
	$result = $db->query($sql);

	
?>
    
    <main role="main" class="container">

    <?php
	
	echo "<ul> \n";
	foreach($result as $k => $v )
	{
		
		if($v["studio"] != "" ){
		$cnt = $v["cnt"];
			
			$query='select count(substudio) as cnt, substudio from '.Db_TABLE_FILEDB.' WHERE studio like "'.$v['studio'].'" GROUP by substudio ORDER BY `substudio` ASC';
			$alt_result = $db->query($query);
			
			$studio = str_replace(" ","-",$v['studio']);
			$studio = str_replace("/","_",$studio);
			
			echo "<li><a href='studio.php?viewstudio=".$studio."'>".$v["studio"]."</a> (".$cnt.")<br>";

			if(count($alt_result) > 1) {
				echo "<ul>";
			
				foreach($alt_result as $k_a => $v_a )
				{
					if($v_a["substudio"] != "" ){
						$cntv_a = $v_a["cnt"];
						$substudio = str_replace(" ","-",$v_a['substudio']);
						$substudio = str_replace("/","_",$substudio);
						echo "<li><a href='studio.php?viewstudio=".$substudio."'>".$v_a["substudio"]."</a>(".$cntv_a.") <br>";
					}
					
				}
				echo "</ul>";
			}
			
		}
	}
	echo "</ul><li><a href='studio.php?viewstudio=NULL'>Studio not Set</a><br>";

 ?>
 </ul>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>