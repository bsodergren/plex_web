<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("../_config.inc.php");

define('TITLE', "Home");
define('PAGENATION', true);

$redirect_string="view/files.php?genre=".$_REQUEST['genre'] ;


if (isset($_REQUEST['genre']) ) 
{
	$genre = $_REQUEST['genre'];


	$genre = str_replace("-"," ",$genre);
	$genre = str_replace("_","/",$genre);


	if ($genre == "NULL" ) {
		//$sql_genre = " and " ." genre IS NULL ";
		$sql_genre = "";
	} else {
		$sql_genre= " genre LIKE '".$genre."' ";
	}
	
	$order_sort = "  title ".$_SESSION['direction'];
	if (isset($_SESSION['sort']) )
	{
		$order_sort = $_SESSION['sort']." ".$_SESSION['direction'];
		$request_key="&genre=".$_REQUEST['genre'];
	
	}
	
	$where =  $lib_where . $sql_genre;
	$db->where ("genre",$genre);
	$db->withTotalCount()->get(Db_TABLE_FILEDB);
	$total_pages = ceil($db->totalCount / $no_of_records_per_page);


	$sql=query_builder("select",$where,false,$order_sort,);
	$results = $db->query($sql);
	$total_results=count($results);

$url_array = array(
	"url" => $_SERVER['PHP_SELF'],
	"rq_key" => "genre",
	"rq_value" => $_REQUEST["genre"],
	"direction" => $_SESSION['direction'],
	"sort_types" => array(
		"Studio" => "studio",
		"Artist" => "artist",
		"Filename" => "filename",
		"Title" => "title",	
		"Duration" => "Duration")
);		

    include __LAYOUT_HEADER__;
?>

<main role="main" class="container">
<a href="view/genre.php">back</a>
<br>
<br>
<?php

	
	echo display_sort_options($url_array,$pageno);
	if (isset($_REQUEST['genre']) ) 
	{
		//echo '<form action="files.php" method="post" id="myform">'."\n";
		
	//	$array=array(
	//		"VALUE_STUDIO" => $_REQUEST[$studio_key],
	//		"NAME_STUDIO" => $studio_key,
	//		"VALUE_GENRE" => $_REQUEST['genre'],
	//		"NAME_GENRE" => "genre");
	//	echo process_template("main_form",$array);
		


		echo display_filelist($results);
		
	//	echo "</form>";
	}
 
 ?>
 </main>
<?php

}

include __LAYOUT_FOOTER__;  ?>