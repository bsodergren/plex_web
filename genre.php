<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");

define('TITLE', "Home");

include __LAYOUT_HEADER__;
if  (isset($_REQUEST['substudio']))
{
	$studio_key="substudio";
	$studio_query=$studio_key;
	$studio_text=$_REQUEST['substudio'];
	
} else {
	$studio_key="studio";
	$studio_query="".$studio_key;
	$studio_text=$_REQUEST['studio'];

}
$request_key=$studio_key.'='.$studio_text;

if (isset($_REQUEST['submit']) ) 
{
	echo saveData($_REQUEST, "genre.php?".$request_key."&genre=".$_REQUEST['genre'].$lib_req ); 	
	
} 
	elseif (isset($_REQUEST['genre']) ) 
{
	$genre = $_REQUEST['genre'];


	$studio = str_replace("-"," ",$studio_text);
	$studio = str_replace("_","/",$studio);
	$sql_studio= $studio_query." = '".$studio."'";
		
	
	


	if ($genre == "NULL" ) {
		//$sql_genre = " and " ." genre IS NULL ";
		$sql_genre = "";
	} else {
		$sql_genre= " and " ." genre LIKE '".$genre."' ";
	}
	
	$order_sort = "  title ASC";
	if (isset($_REQUEST['sort']) )
	{
		$order_sort = $_REQUEST['sort']." ASC";	
	}
	
	$where =  $lib_where.$sql_studio . $sql_genre;
	
	$sql=query_builder("select",$where,false,$order_sort);
	
	$results = $db->query($sql);
	 $total_results=count($results);
?>
      
<main role="main" class="container">
<?php echo $total_results; ?> number of files<br>
<a href="studio.php?<?php echo $request_key.$lib_req; ?>">back</a>
<br>
<br>
<?php echo " <a href='genre.php?".$request_key."&genre=".$_REQUEST["genre"].$lib_req."&sort=studio'>Studio</a> - ";
echo " <a href='genre.php?".$request_key."&genre=".$_REQUEST["genre"].$lib_req."&sort=title'>Title</a> - ";
echo " <a href='genre.php?".$request_key."&genre=".$_REQUEST["genre"].$lib_req."&sort=artist'>artist</a>";
    

	if (isset($_REQUEST['genre']) ) 
{
	echo "<table class=blueTable> 
 <form action=genre.php method=post id=\"myform\">
 <input type='hidden' value='".$_REQUEST[$studio_key]."' name='".$studio_key."'>
 <input type='hidden' value='".$_REQUEST['genre']."' name='genre'>";
	echo $lib_hidden;

	echo display_filelist($results);
	
	echo "</table>
	</form>";
}
 
 ?>
 </main>
<?php

}

include __LAYOUT_FOOTER__;  ?>