<!-- navbar file -->
<nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm p-2">
	<div class="navcontainer">
		<a class="navbar-brand" href="home.php">
		<img src="<?php echo __LAYOUT_URL__;?>/images/logonotext.png" alt="" width="50" height="50" class="mr-3">
		<?php echo APP_NAME; ?></a>
		
		<div class="collapse navbar-collapse" id="navbarSupportedContent">

			<!-- Left Side Of Navbar -->
			<ul class="navbar-nav mr-auto">
			<?php
				$sql = query_builder("DISTINCT(library) as library ");
				$result2 = $db->query($sql);
				foreach ($result2 as $k => $v)
				{
					echo display_navbar_left_links("home.php?library=" . $v["library"] ,$v["library"]);
		
			}
			?>
			</ul>

		<!-- Right Side Of Navbar -->
		
			<ul class="navbar-nav ml-auto">
				<?php echo display_navbar_links(); ?>
			</ul>
			
		</div>
<div class="nav-item">
<?php
	if(defined('PAGENATION') and PAGENATION == true) {
		display_pagenation($_SERVER['PHP_SELF'],$request_key,$pageno,$total_pages);
	}
        ?>
		</div>
	</div>
</nav>
<!-- navbar file -->
