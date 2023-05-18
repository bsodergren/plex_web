<?php
define('TITLE', 'Home');
define('NONAVBAR', true);
define("VIDEOINFO", true);

require_once '_config.inc.php';


$id = $_REQUEST['id'];
$cols = array("id","filename","video_key","thumbnail","title","artist","genre","studio","substudio","keyword","added","fullpath","duration");
$db->where("id", $id);
$result = $db->get(Db_TABLE_FILEDB,null,$cols);

require __LAYOUT_HEADER__;
?>

<main role="main">
    <?php

    
        echo display_filelist($result); 

  ?>
</main>
<?php require __LAYOUT_FOOTER__;
