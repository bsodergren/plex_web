<!-- navbar file -->
<nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm p-2">
	<div class="container">
		<a class="navbar-brand" href="home.php">
		<img src="<?php echo __LAYOUT_URL__;?>/images/logonotext.png" alt="" width="50" height="50" class="mr-3"><?php echo APP_NAME; ?></a>
		
		<div class="collapse navbar-collapse" id="navbarSupportedContent">

			<!-- Left Side Of Navbar -->
			<ul class="navbar-nav mr-auto">
			
			</ul>

		<!-- Right Side Of Navbar -->
		
			<ul class="navbar-nav ml-auto">
				<?php echo display_navbar_links(); ?>
			</ul>
		</div>
	</div>
</nav>
<!-- navbar file -->
