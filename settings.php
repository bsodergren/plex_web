<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");
/* 
$url_array = array(
	"url" => $_SERVER['PHP_SELF'],
	"rq_key" => "genre",
	"rq_value" => $_REQUEST["genre"],
	"direction" => $_SESSION['direction'],
	"sort_types" => array(
		"Studio" => "studio",
		"artist" => "artist",
		"filename" => "filename",
		"title" => "title",	
		"Duration" => "Duration")
);
*/



//$form = new Formr\Formr('bulma');
$form = new Formr\Formr();

if ($form->submitted())
{


    // get our form values and assign them to a variable
	foreach ($_POST as $key => $value) {

		if ($key == 'submit') {
			continue;
		}

		if(str_contains($key,"setting_"))
		{
			$pcs = explode('_', $key);
			$field = $pcs[1];
			$new_settiings[$field] = $value;
			continue;
		}

		$data = Array (	'value' => $value	);
		$db->where ('name', $key);
		$db->update(Db_TABLE_SETTINGS, $data);
	}

	
	if ($new_settiings['name'] != '' ) {
	
		$id = $db->insert(Db_TABLE_SETTINGS, $new_settiings);
	}


    // show a success message if no errors
    if($form->ok()) {
		$form->redirect('/plex_web/settings.php');

		}
}

define('TITLE', "Test Page");
include __LAYOUT_HEADER__;
?>
    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>

<?php


$form->open('MyForm');

 //$form->messages(); 
# $form->create_form('Name, Email, Comments|textarea');
foreach(__SETTINGS__ as $name => $value_type)
{

	$pcs = explode(';', $value_type);

	$type = $pcs[0];
	$value = $pcs[1];

	if ($type == "bool") {
		$checked = '';
		$notchecked = '';

		if ($value == 1) {
			$checked = true;
		} else {
			$notchecked = true;
		}
		$form->label($name, $name);
		$form->radio_inline($name, "Yes", 1, '', '', '', $checked);
		$form->radio_inline($name, "No", 0, '', '', '', $notchecked);
		echo "<br>";
	}

	if($type == "text")
	{
		
		$form->text($name,$name,$value);
		echo "<br>";

	}


}

echo "<br>";

$form->text("setting_name","New Setting");

echo "<br>";
$form->text("setting_value","Default Value");
echo "<br>";

$options = [
    'bool' => 'bool',
    'text' => 'text'   
];

// notice that we added a 'selected' value in the 7th parameter

echo $form->dropdown('setting_type','Setting Type','','','','','',$options);

echo "<br>";

$form->submit_button();
$form->close();

 ?>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>