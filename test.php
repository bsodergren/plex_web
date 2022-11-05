<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");



define('TITLE', "Test Page");

include __LAYOUT_HEADER__;
?>
    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>
<?php
$json='{ "menu":[

{"name" : "Title 1","link" : "#","sub" : [
    {"name" : "Title A","link" : "#","sub" : null},
    {"name" : "Title b","link" : "#","sub" : null},
    {"name" : "Title c","link" : "#","sub" : null},
    {"name" : "Title d","link" : "#","sub" : null}]},

{"name" : "Title 2","link" : "#","sub" : null}]}'; // [ {name : 'Enclosure1',link : '#',sub : null}]},{name : 'Title',link : '#',sub : [ {name : 'Enclosure1',link : '#',sub : null}]},{name : 'Title',link : '#',sub : [ {name : 'Enclosure1',link : '#',sub : null}]},{name : 'Title',link : '#',sub : [ {name : 'Enclosure1',link : '#',sub : null}]} ] }";
echo printCode(json_decode($json,true),true);

$json=array("menu" => array(
        array("text"=>"text",
                            "link"=>"www.html",
                            "sub"=>NULL),
));
$array= json_encode($json);

print_r2($array);
 ?>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>