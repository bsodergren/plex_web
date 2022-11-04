<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");

define('TITLE', "Home");

include __LAYOUT_HEADER__;

if (isset($_REQUEST['submit']) ) 
{
	echo saveData($_REQUEST, "studio.php?studio=".$_REQUEST['studio'] ); 	
	
} elseif (isset($_REQUEST['genre']) ) 
{
	$genre = $_REQUEST['genre'];

	$studio = str_replace("-"," ",$_REQUEST['studio']);
	$studio = str_replace("_","/",$studio);
	
	
	if ($studio == "NULL" ) {
		$sql_studio= " studio IS NULL ";
	} else {
		
		$sql_studio= " if( (studio = '".$studio."' or substudio = '".$studio."') and IFNULL(substudio,1) = 1 ,studio, substudio ) = '".$studio."'";
	}

	if ($genre == "NULL" ) {
		$sql_genre = " genre IS NULL ";
	} else {
		$sql_genre= " genre LIKE '".$genre."' ";
	}
	
	$order_sort = "  title ASC";
	if (isset($_REQUEST['sort']) )
	{
		$order_sort = $_REQUEST['sort']." ASC";	
	}
	
	$where =  $sql_studio ." and " . $sql_genre;
	
	$sql=query_builder("select",$where,false,$order_sort);
	logger("SQL Query", $sql);
	$results = $db->query($sql);
	 $total_results=count($results);
?>
      
<main role="main" class="container">
<?php echo $total_results; ?> number of files<br>
<a href="studio.php?studio=<?php echo $_REQUEST['studio'] ?>">back</a>
<br>
<br>
<?php echo " <a href='genre.php?studio=".$_REQUEST['studio']."&genre=".$_REQUEST["genre"]."&sort=studio'>Studio</a> - ";
echo " <a href='genre.php?studio=".$_REQUEST['studio']."&genre=".$_REQUEST["genre"]."&sort=title'>Title</a> - ";
echo " <a href='genre.php?studio=".$_REQUEST['studio']."&genre=".$_REQUEST["genre"]."&sort=artist'>artist</a>";
    

	if (isset($_REQUEST['genre']) ) 
{
	echo "<table class=blueTable> 
 <form action=genre.php method=post id=\"myform\">
 <input type='hidden' value='".$_REQUEST['studio']."' name='studio'>";

	echo display_filelist($results);
	
	echo "</table>
	</form>";
}
 
 ?>
 </main>
<?php

}

include __LAYOUT_FOOTER__;  ?>