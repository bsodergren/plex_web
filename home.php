<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php"));

require_once("_config.inc.php");

define('TITLE', "Home");

include __LAYOUT_HEADER__;

$sql = query_builder("studio","library = '" . $in_directory ."' ",'studio','studio ASC');
			logger("studios", $sql);

$result = $db->query($sql);

$sql = query_builder("DISTINCT(library) as library ");
$result2 = $db->query($sql);

?>

<main role="main" class="container">

	<?php

	echo "<p>";
	echo '<ul id="menu" class="list"> '."\n";
	
	$json_array["menu"]=array();
	$index=0;
	$sidx=0;
	foreach ($result as $k => $v)
	{

		if ($v["studio"] != "") 
		{
			$sql = query_builder("count(studio) as cnt",$lib_where . ' studio like "' . $v['studio'].'" and substudio is null','studio','studio ASC');
			
			$rar = $db->rawQueryOne($sql);
			$cnt='';
			if(isset($rar['cnt'])) $cnt = " (".$rar['cnt'].") ";
			
			$sql = query_builder("count(substudio) as cnt, substudio",$lib_where . ' studio like "' . $v['studio'].'"','substudio','substudio ASC ');

			logger("Sub studios", $sql);
			$alt_result = $db->query($sql);
			
			$link = (count($alt_result) > 1) ? "&substudio=null":"";
	
			$studio = str_replace(" ", "-", $v['studio']);
			$studio = str_replace("/", "_", $studio);
			
			$json_array["menu"][$index]["name"] = $v["studio"] . $cnt;
			$json_array["menu"][$index]["link"] = "genre.php?studio=" . $studio . $link;
			

			if (count($alt_result) > 1) 
			{
				$json_array["menu"][$index]["sub"] = array();
				$sidx=0;
				#echo "<ul>";
				foreach ($alt_result as $k_a => $v_a) 
				{
					if ($v_a["substudio"] != NULL )
					{
						$json_array["menu"][$index]["sub"][$sidx]=array();

						$cntv_a = " (".$v_a["cnt"].")";
						$substudio = str_replace(" ", "-", $v_a['substudio']);
						$substudio = str_replace("/", "_", $substudio);
						
						$json_array["menu"][$index]["sub"][$sidx]["name"] = $v_a["substudio"]. $cntv_a ;
						$json_array["menu"][$index]["sub"][$sidx]["link"] = "genre.php?substudio=" . $substudio;
						$json_array["menu"][$index]["sub"][$sidx]["sub"] = null;
					
						$sidx++;

						//echo "<li><a href='genre.php?substudio=" . $substudio . "'>" . $v_a["substudio"] . "</a>" . $cntv_a . "<br>";
					}
				}
			} else {
				$json_array["menu"][$index]["sub"] = null;
			}				
				
		#echo "</ul>";
		
			$index++;			
		}
	}
	

	?>
	</ul>
</main>
<?php include __LAYOUT_FOOTER__;  ?>