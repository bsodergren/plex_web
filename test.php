<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");


$sql = "  SELECT 
SUBSTRING_INDEX(genre,',',1) AS firstname from metatags_filedb WHERE
substudio = 'not fap' ";


logger($sql);
$result = $db->query($sql);

define('TITLE', "Test Page");

include __LAYOUT_HEADER__;
?>
    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>
<?php
print_r2($result);

 ?>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>