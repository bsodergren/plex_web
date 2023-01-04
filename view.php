<?php

require_once("_config.inc.php");

define('TITLE', "Home");


$dir=$_GET['dir'];
$__file=$_GET['file'];

include __LAYOUT_HEADER__;
?>
      
<main role="main" class="container">
<br>
<br>
<a href="<? echo getReferer()."?dir=".$dir;?>">back</a>


<?php


	$run_cmd = "echo -n $__file | md5sum | awk '{print $1}'";
	$results = shell_exec($run_cmd);
	$video_key="x".$results;
	$video_key = trim($video_key);
	
//	$sql=query_builder("select", "video_key = '" . $video_key . "'");
$cols = Array ("id","filename","thumbnail",
"title","artist",
"genre","studio","substudio",
"duration","favorite","fullpath","library" );
	$db->where ("video_key", $video_key);
	$result = $db->get (Db_TABLE_FILEDB, null, $cols);
	
	?>
<form action="process.php" method="post" id="formId">
<button type='submit' name="submit" onclick="hideSubmit('save')">Save</button>
<button type='submit' name="submit" onclick="hideSubmit('delete')">Delete</button>
<input type='hidden' id="redirect" value="<? echo getReferer()."?dir=".$dir;?>">
<input type=hidden id="hiddenSubmit" name=submit value="">

	<?php 
	// <button type="submit" name="submit" value="submit">Send</button>		
		$array=array();
		echo process_template("main_form",$array);
		


		echo display_filelist($result);
		
		echo "</form>";
?>
	
 
 
 </main>


<?php

include __LAYOUT_FOOTER__;  ?>