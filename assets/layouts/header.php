<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="description" content="<?php echo APP_DESCRIPTION;  ?>">
		<meta name="author" content="<?php echo APP_OWNER;  ?>">
		<base href="<?php echo __URL_HOME__;  ?>/" />
		
		<title><?php echo TITLE . ' | ' . APP_NAME; ?></title>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="<?php echo __LAYOUT_URL__;?>js/app.js?<?php echo substr(md5(rand()), 0, 7);?>"></script>


		
		<link rel="stylesheet" href="<?php echo __LAYOUT_URL__;?>css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo __LAYOUT_URL__;?>css/all.min.css">
		<link rel="stylesheet" href="<?php echo __LAYOUT_URL__;?>css/app.css?<?php echo substr(md5(rand()), 0, 7);?>">
		<link rel="stylesheet" href="<?php echo __LAYOUT_URL__;?>css/custom.css?<?php echo substr(md5(rand()), 0, 7);?>"> 


 
	</head>
	<?php
	$onLoad="";
	if (__HTML_POPUP__ == true ) {
		$onLoad="onLoad=\"popup('/plex_web/logs.php', 'logs',1000,1000)\"";
	}
?>
<body <?php echo $onLoad;?>>
<?php  include __LAYOUT_NAVBAR__; ?>
