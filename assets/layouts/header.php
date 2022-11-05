<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="description" content="<?php echo APP_DESCRIPTION;  ?>">
		<meta name="author" content="<?php echo APP_OWNER;  ?>">
		<base href="<?php echo __URL_HOME__;  ?>/" />
		
		
		<!--     <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->


		<title><?php echo TITLE . ' | ' . APP_NAME; ?></title>
		
		<link rel="icon" type="image/png" href="<?php echo __LAYOUT_URL__;?>/images/favicon.png">

	
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js"></script>
		
		<!-- Custom styles --> 		

		
			<link rel="stylesheet" href="<?php echo __URL_HOME__;?>/assets/lib/vendor/bootstrap-4.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo __URL_HOME__;?>/assets/lib/vendor/fontawesome-5.12.0/css/all.min.css">
		<link rel="stylesheet" href="<?php echo __LAYOUT_URL__;?>css/app.css?<?php echo substr(md5(rand()), 0, 7);?>">
		<link rel="stylesheet" href="<?php echo __LAYOUT_URL__;?>css/custom.css?<?php echo substr(md5(rand()), 0, 7);?>"> 
		<script src="<?php echo __LAYOUT_URL__;?>js/app.js?<?php echo substr(md5(rand()), 0, 7);?>"></script>



	</head>
	<?php
	$onLoad="";
	if (__HTML_POPUP__ == true ) {
		$onLoad="onLoad=\"popup('/plex_web/logs.php', 'logs',1000,1000)\"";
	}
?>
<body <?php echo $onLoad;?>>
<?php  include __LAYOUT_NAVBAR__; ?>
