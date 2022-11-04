<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php"));

require_once("_config.inc.php");

define('TITLE', "New");

if (isset($_REQUEST['submit'])) {
	echo saveData($_REQUEST, "new.php");
	exit;
}

if (isset($_REQUEST['delete'])) {
	echo deleteEntry($_REQUEST, "new.php");
	exit;
}

include __LAYOUT_HEADER__;
$days = 1;

if (isset($_REQUEST['days'])) {
	$days = $_REQUEST['days'];
}
$sql = query_builder("select", "library = '".$in_directory."' and `added` >= (CURRENT_TIMESTAMP - INTERVAL $days day);");
$result = $db->query($sql);


?>

<main role="main" class="container">
	<a href="home.php">back</a>
	<br>
	<br>
	<?php



		echo "<form action=new.php method=post id=\"myform\">
	<table class=blueTable>";
	echo display_filelist($result);


		echo "
	</table>
</form>
<p>
";
	


	?>
</main>
<?php include __LAYOUT_FOOTER__;  ?>