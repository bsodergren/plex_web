<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("../_config.inc.php");

define('TITLE', "Home");

include __LAYOUT_HEADER__;

if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}

$no_of_records_per_page = 10;
$offset = ($pageno-1) * $no_of_records_per_page; 

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


	$sql=query_builder("select",$where,false,$order_sort,$no_of_records_per_page,$offset);
	logger("qyefasd",$sql);
	$results = $db->query($sql);
	 $total_results=count($results);
	 
?>
      
<main role="main" class="container">
<a href="view/files.php?pageno=1<?php echo $request_key;?>">First</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="<?php if($pageno <= 1){ echo '#'; } else { echo "view/files.php?pageno=".($pageno - 1).$request_key; } ?>">Prev</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "view/files.php?pageno=".($pageno + 1).$request_key; } ?>">Next</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="view/files.php?pageno=<?php echo $total_pages.$request_key; ?>">Last</a>
<p>
    
<?php echo $total_results; ?> number of files<br>
<a href="view/genre.php">back</a>
<br>
<br>
<?php

    echo display_directory_navlinks('view/files.php','Studio',
	[ "pageno"=>$pageno, "genre" => $_REQUEST["genre"],"sort" => "Studio",	"direction"=>$_SESSION['direction'] ]);
		echo " | ";

	echo display_directory_navlinks('view/files.php','Artist',
	[ "pageno"=>$pageno,"genre" => $_REQUEST["genre"],"sort" => "artist",	"direction"=>$_SESSION['direction'] ]);
	echo " | ";
	echo display_directory_navlinks('view/files.php','filename',
	[ "pageno"=>$pageno,"genre" => $_REQUEST["genre"],"sort" => "filename",	"direction"=>$_SESSION['direction'] ]);
	echo " | ";
	echo display_directory_navlinks('view/files.php','Title',
	[ "pageno"=>$pageno,"genre" => $_REQUEST["genre"],"sort" => "title",	"direction"=>$_SESSION['direction'] ]);
	echo " | ";
	echo display_directory_navlinks('view/files.php','Duration',
	[ "pageno"=>$pageno,"genre" => $_REQUEST["genre"],"sort" => "duration",	"direction"=>$_SESSION['direction'] ]);
	
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