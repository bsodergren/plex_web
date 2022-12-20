<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");

define('TITLE', "Home");
define('PAGENATION', true);



	if (isset($_REQUEST['substudio'])) {
		#	if  (!isset($_REQUEST['allfiles']))
#	{
		$substudio = str_replace("-", " ", $_REQUEST['substudio']);
		//$substudio = str_replace("_","/",$substudio);
		$uri["substudio"] = array($_REQUEST['substudio'], $substudio);
		$studio_key = "substudio";
		#	}
#	$studio_key="substudio";
	}
	if (isset($_REQUEST['studio'])) {
		$studio = str_replace("-", " ", $_REQUEST['studio']);
		//$studio = str_replace("_","/",$studio);
		$uri["studio"] = array($_REQUEST['studio'], $studio);
		if (!isset($studio_key)) {
			$studio_key = "studio";
		}
	}

	if (isset($_REQUEST['genre'])) {
		$genre = str_replace("-", " ", $_REQUEST['genre']);
		$genre = str_replace("_", "/", $genre);
		$uri["genre"] = array($_REQUEST['genre'], $genre);

	}

	if (isset($_SESSION['sort']) )
{
	$uri["sort"] = $_SESSION['sort'];
}

if (isset($_SESSION['direction']) )
{
	$uri["direction"] = $_SESSION['direction'];
}

if (isset($_SESSION['library']) )
{
	$uri["library"] = $_SESSION['library'];
}

if (isset($_GET['pageno']) )
{
	$uri["pageno"] = $_GET['pageno'];
}




if (!isset($_REQUEST['allfiles'])) {
	list($sql_studio, $order_sort) = uri_SQLQuery($uri);

	$where = str_replace("studio = 'null'", "studio IS NULL", $sql_studio);
} else {
	list($sql_studio, $order_sort) = uri_SQLQuery($uri);
	$studio_key='';
	$where = $sql_studio;
	$uri["allfiles"] = $_GET['allfiles'];

}

		$db->where ($where);

	$db->withTotalCount()->get(Db_TABLE_FILEDB);
	$total_results=$db->totalCount;
	$total_pages = ceil($db->totalCount / $no_of_records_per_page);

	$sql=query_builder("select",$where,false,$order_sort,$no_of_records_per_page,$offset);
logger("all files", $sql);
	$results = $db->query($sql);
	$request_key = uri_String($uri);

$redirect_string="files.php".$request_key;
	//$total_results=count($results);
	$url_array = array(
		"url" => $_SERVER['PHP_SELF'],
		"rq_string" => $request_key,
		"sort_types" => array(
			"Studio" => "studio",
			"Artist" => "artist",
			"Filename" => "filename",
			"Title" => "title",	
			"Duration" => "duration",
			"Newest" => "added"	)
);


include __LAYOUT_HEADER__;
?>
      
<main role="main" class="container">
<?php echo $total_results; ?> number of files<br>
<a href="genre.php<?php echo $request_key; ?>">back</a>
<br>
<br>


<?php
	echo display_sort_options($url_array);
?>
<p>
<div class="nav-item">
<?php
if(defined('PAGENATION') and PAGENATION == true) {
		display_pagenationPages($_SERVER['PHP_SELF'],$request_key,$pageno,$total_pages);
	}
?>
</div>
</p>

<form action="process.php" method="post" id="formId">
<button type='submit' name="submit" onclick="hideSubmit('save')">Save</button>
<button type='submit' name="submit" onclick="hideSubmit('delete')">Delete</button>
<input type='hidden' id="redirect" value="<? echo $redirect_string;?>">
<input type=hidden id="hiddenSubmit" name=submit value="">

	<?php 
	// <button type="submit" name="submit" value="submit">Send</button>	
	if($studio_key)	{
		$studio_key_value= $_REQUEST[$studio_key];
	} else {
		$studio_key_value='';
	}
		$array=array(
			"VALUE_STUDIO" =>$studio_key_value,
			"NAME_STUDIO" => $studio_key,
			"VALUE_GENRE" => (isset($_REQUEST['genre'])) ? $_REQUEST['genre'] : "null",
			"NAME_GENRE" => "genre");
		echo process_template("main_form",$array);
		


		echo display_filelist($results,'filedelete');
		
		echo "</form>";
	
 
 ?>
 </main>


<?php

include __LAYOUT_FOOTER__;  ?>