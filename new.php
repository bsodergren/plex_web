<?php
require_once("_config.inc.php");

define('TITLE', "New");



require __LAYOUT_HEADER__;

$lib_where = $lib_where . ' AND ';$days = 1;

if (isset($_REQUEST['days'])) {
	$days = $_REQUEST['days'];
}
$sql = query_builder("select", "library = '".$in_directory."' and `new` = 1;");
$result = $db->query($sql);


?>

<main role="main" class="container">
	<a href="home.php">back</a>
	<br>
	<br>
	<?php



		echo "<form action=new.php method=post id=\"myform\">
	<table class=blueTable>";
	echo display_filelist($result,'hide');


		echo "
	</table>
</form>
<p>
";
	


	?>
</main>
<?php include __LAYOUT_FOOTER__;  ?>