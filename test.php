<?php
require_once("_config.inc.php");
define('TITLE', "Test Page");
//define('BREADCRUMB', ['home' => "home.php"]);
include __LAYOUT_HEADER__;
?>
<main role="main" class="container">
<?php

$searchDir = __PLEX_LIBRARY__.'/'.$in_directory;

$sql = query_builder('concat(fullpath,filename) as file');
logger('all files', $sql);

$results = $db->query($sql);

foreach($results as $k => $file ){
    $dbFileArray[] = trim($file['file']);
}


$res = command_search( $searchDir);
foreach($res[1] as $k => $file ){
    $fileArray[] = trim($file);
}

sort($dbFileArray);
//asort($fileArray);
$nAssArr = [];

$cAssArr = [];
$deletedFiles = [];
$changedFiles = [];
$newFiles = [];

$NewFileArray = array_diff($fileArray,$dbFileArray);
sort($NewFileArray);
$ChangedFileArray = array_diff($dbFileArray,$fileArray);
sort($ChangedFileArray);

foreach($NewFileArray as $k => $v){
    $key = basename($v,".mp4");
    $nAssArr[$key] = $v;
}
foreach($ChangedFileArray as $k => $v){
    $key = basename($v,".mp4");
    $cAssArr[$key] = $v;
}
if (count($cAssArr) > 0) {
    $deletedFiles = array_diff_key($cAssArr, $nAssArr);

    foreach ($cAssArr as $key => $value) {
        if (!array_key_exists($key, $deletedFiles)) {
            $changedFiles[$key] = $nAssArr[$key];
        }
    }
}

$newFiles = array_diff_key($nAssArr,$changedFiles);

dump($deletedFiles);
dump($changedFiles);
dump($newFiles);


foreach($changedFiles as $key => $filename)
{

    $fc = new FileDb($filename);
    if ($fc->exists()) {
        $fc->updateTag(["fullpath"=> $fc->filepath]);
    }
    
    unset($fc);
}

foreach($newFiles as $key => $filename)
{

    $fc = new FileDb($filename);
    if(!$fc->exists()){

        $id =  $fc->addVideo();
         output( $fc->filename ." ". $id );
       //  $fc->deleteVideo($id);
         
    }
    unset($fc);
}
//$filedb = new Filedb($filename);
//echo $filedb->getVideoInfo();



//dump($filedb->videoInfo);

?>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>