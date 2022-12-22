


    <div class="container text-center">
      <div class="row">
        <div class="col">  
        <?php if (__DISPLAY__['sort'] == true) include __PHP_TEMPLATE__.'sort_buttons.php'; ?>
    </div>
    <div class="col-md-auto">
    <?php if (__DISPLAY__['page'] == true) include __PHP_TEMPLATE__.'paginate.php'; ?>
    </div>
  </div>
</div>

<?php
		if ($studio_key) {
			$studio_key_value = $_REQUEST[$studio_key];
		} else {
			$studio_key_value = '';
		}
	
	  $genre_value = (isset($_REQUEST['genre'])) ? $_REQUEST['genre'] : 'null';
	?>
	


<?php
  echo $hidden_fields . "\n";
  echo display_filelist($results, '', $page_array); 
?>

</form>
