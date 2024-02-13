<?php
use Plex\Core\FileListing;
use Plex\Template\Display\Display;

use Plex\Template\Display\VideoDisplay;
use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
require_once '_config.inc.php';

$form = new Formr\Formr('bootstrap4');

if ($form->submitted()) {
    echo proccess_settings('home.php');
}

define('TITLE', 'Test Page');

Header::Display();
?>

<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>

<?php

$form->open('MyForm');

// $form->messages();
// $form->create_form('Name, Email, Comments|textarea');
foreach (__SETTINGS__ as $name => $value_type) {
    $pcs   = explode(';', $value_type);

    $type  = $pcs[0];
    $value = $pcs[1];

    if ('bool' == $type) {
        $checked    = '';
        $notchecked = '';

        if (1 == $value) {
            $checked = 'checked';
        } else {
            $notchecked = 'checked';
        }
        ?>


<div class="form-check form-switch">
<input type="hidden" name="<?php echo $name; ?>" value="0">
 <label class="form-check-label" for="flexSwitchCheckDefault"><?php echo $name; ?></label>
  <input class="form-check-input" name="<?php echo $name; ?>" value="1"  type="checkbox" role="switch" id="flexSwitchCheckDefault" <?php echo $checked; ?> />




	</div>
<?php

    }

    if ('text' == $type) {
        ?>
<div class="form-group">
	<div class="row mb-3">
		<label for="<?php echo $name; ?>" class="col-sm-3 col-form-label "><?php echo $name; ?></label>
		<div class="col-sm-7">
			<input type="text" class="form-control border border-info"
			name="<?php echo $name; ?>"
			id="<?php echo $name; ?>" placeholder="<?php echo $value; ?>" value="<?php echo $value; ?>">
		</div>
	</div>
</div>
<?php
    }

    if ('array' == $type) {
        ?>
<div class="form-group">
	<div class="row mb-3">
		<label for="<?php echo $name; ?>" class="col-sm-3 col-form-label "><?php echo $name; ?></label>
		<div class="col-sm-7">
			<input type="text" class="form-control border border-info"
			name="<?php echo $name; ?>"
			id="<?php echo $name; ?>" placeholder='<?php echo $value; ?>' value='<?php echo $value; ?>'>
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
  <option value="array">Array</option>

</select>
</div>
</div>
<?php

$form->submit_button();
$form->close();

?>
 </main>
 <?php Footer::Display();
 ?>