<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");

$form = new Formr\Formr('bootstrap4');

if ($form->submitted())
{

	logger("settings post data", $_POST);
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
			$checked = "checked";
		} else {
			$notchecked = "checked";;
		}
?>

<div class="row">

	 <div class="form-group col">
	<label for="radiobtn" class="col-sm-3 control-label"><?php echo $name ?></label>		 
	<div class="col-sm-5 form-check-inline">		 
		<div class="radio radio-danger">		 
			<input type="radio"  name="<?php echo $name ?>" id="<?php echo $name ?>1" value="1" <?php echo $checked ?>>	 
			<label>	Yes	</label>
		</div>
		<div class="col-sm-5">		 

		<div class="radio radio-danger  ">		 
			<input type="radio"  name="<?php echo $name ?>" id="<?php echo $name ?>1" value="0"  <?php echo $notchecked ?>>		 
			<label>	No</label>
		</div></div>
	</div>
</div>
	</div>
<?php

	}

	if($type == "text")
	{
?>
<div class="form-group">
	<div class="row mb-3"> 
		<label for="<?php echo $name ?>" class="col-sm-3 col-form-label border  border-secondary"><?php echo $name ?></label>
		<div class="col-sm-7">
			<input type="text" class="form-control border border-info " id="<?php echo $name ?>" placeholder="<?php echo $value ?>" value="<?php echo $value ?>">
		</div>
	</div>
</div>
<?php 
	}

}

?>
<div class="row">
<label class="col-sm-3  text-center ">Add New Setting</label>
</div>
<div class="row">
  <div class="col-sm-3 ">
    <input type="text" name="setting_name" class="form-control" placeholder="New Setting" aria-label="New Setting">
  </div>
  <div class="col-sm-3">
    <input type="text" name="setting_value"class="form-control" placeholder="default value" aria-label="default value">
  </div>
<div class="col-sm-3 ">
  <select name="setting_type" class="form-select" size="2" aria-label="Setting Type">
  <option value="bool">Boolean</option>
  <option value="text">Text</option>
  
</select>
</div>
</div>
<?php 

$form->submit_button();
$form->close();

 ?>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>