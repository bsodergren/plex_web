<?php
require_once("../_config.inc.php");

define('__NULL_FIELD', "studio");
define('TITLE', "Home");

include __LAYOUT_HEADER__;
if (isset($_REQUEST['submit']) ) 
{	
	echo saveData($_REQUEST, __THIS_PAGE__ ); 
	
} else {

?>
    
<main role="main" class="container">
<a href="<?php echo __THIS_PAGE__; ?>">back</a>
<br>
<br>
<?php	

	$sql=query_builder("select","'".__NULL_FIELD."' IS NULL or studio = \"Misc\" ",false,"`filename` ASC");

	//display_log($sql);
	$results = $db->query($sql);

	echo "<table class=blueTable> 
 <form action=".__THIS_PAGE__." method=post id=\"myform\">";
	echo display_filelist($results);
	echo "</table>
	</form>";
 
 ?>
 </main>
<?php

}

include __LAYOUT_FOOTER__;  ?>