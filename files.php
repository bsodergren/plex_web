<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");

define('TITLE', "Home");

include __LAYOUT_HEADER__;

	$null='';	
		$null_req='';	
	if  (isset($_REQUEST['substudio']) && $_REQUEST['substudio'] != "null")
{
	$studio_key="substudio";
	$studio_query=$studio_key;
	$studio_text=$_REQUEST['substudio'];
	
} else {
	
	if  (isset($_REQUEST['substudio']) && $_REQUEST['substudio'] == "null")
		{
			$null=' and substudio is null ';
			$null_req="&substudio=NULL";
		}
	$studio_key="studio";
	$studio_query="".$studio_key;
	$studio_text=$_REQUEST['studio'];

}

$request_key=$studio_key.'='.$studio_text.$null_req;

$redirect_string="files.php?".$request_key."&genre=".$_REQUEST['genre'] ;

process_form($redirect_string);

if (isset($_REQUEST['genre']) ) 
{
	$genre = $_REQUEST['genre'];


	$studio = str_replace("-"," ",$studio_text);
	$studio = str_replace("_","/",$studio);
	$sql_studio= $studio_query." = '".$studio."' ".$null;
		
	
	


	if ($genre == "NULL" ) {
		//$sql_genre = " and " ." genre IS NULL ";
		$sql_genre = "";
	} else {
		$sql_genre= " and " ." genre LIKE '".$genre."' ";
	}
	
	$order_sort = "  title ".$_SESSION['direction'];
	if (isset($_REQUEST['sort']) )
	{
		$order_sort = $_REQUEST['sort']." ".$_SESSION['direction'];	
	}
	
	$where =  $lib_where.$sql_studio . $sql_genre;
	
	$sql=query_builder("select",$where,false,$order_sort);
	logger("qyefasd",$sql);
	$results = $db->query($sql);
	 $total_results=count($results);
?>
      
<main role="main" class="container">
<?php echo $total_results; ?> number of files<br>
<a href="genre.php?<?php echo $request_key; ?>">back</a>
<br>
<br>
<?php

    
	echo display_directory_navlinks('files.php','Artist',
	[ $studio_key => $studio_text,"genre" => $_REQUEST["genre"],"sort" => "artist",	"direction"=>$_SESSION['direction'] ]);
	echo " | ";
	echo display_directory_navlinks('files.php','filename',
	[ $studio_key => $studio_text,"genre" => $_REQUEST["genre"],"sort" => "filename",	"direction"=>$_SESSION['direction'] ]);
	echo " | ";
	echo display_directory_navlinks('files.php','Title',
	[ $studio_key => $studio_text,"genre" => $_REQUEST["genre"],"sort" => "title",	"direction"=>$_SESSION['direction'] ]);
	echo " | ";
	echo display_directory_navlinks('files.php','Duration',
	[ $studio_key => $studio_text,"genre" => $_REQUEST["genre"],"sort" => "duration",	"direction"=>$_SESSION['direction'] ]);
	
	if (isset($_REQUEST['genre']) ) 
	{
		echo '<form action="files.php" method="post" id="myform">'."\n";
		
		$array=array(
			"VALUE_STUDIO" => $_REQUEST[$studio_key],
			"NAME_STUDIO" => $studio_key,
			"VALUE_GENRE" => $_REQUEST['genre'],
			"NAME_GENRE" => "genre");
		echo process_template("main_form",$array);
		


		echo display_filelist($results,'filedelete');
		
		echo "</form>";
	}
 
 ?>
 </main>
<?php

}

include __LAYOUT_FOOTER__;  ?>