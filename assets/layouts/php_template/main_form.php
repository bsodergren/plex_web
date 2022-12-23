<?php
		if ($studio_key) {
			$studio_key_value = $_REQUEST[$studio_key];
		} else {
			$studio_key_value = '';
		}
	
	  $genre_value = (isset($_REQUEST['genre'])) ? $_REQUEST['genre'] : 'null';
	?>
	


<?php
  echo display_filelist($results, '', $page_array); 
?>

</form>
