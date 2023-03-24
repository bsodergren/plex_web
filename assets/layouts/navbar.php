<nav class="navbar navbar-expand-lg fixed-top  navbar-primary bg-dark ">
    <div class="container-fluid">
        <a class="navbar-brand text-light" href="home.php">
        <img src="<?php echo __LAYOUT_URL__; ?>/images/logonotext.png" alt="" width="50" height="50" class="mr-3"><?php echo APP_NAME; ?></a>
        <div class="collapse navbar-collapse" id="navbarContent">
        <?php

                $library_links = '';
        $sql = query_builder('DISTINCT(library) as library ');
        foreach ($db->query($sql) as $k => $v) {
            $library_links .= display_navbar_left_links('home.php?library='.$v['library'], $v['library']);
        }
        echo   process_template('navbar/library_menu', ["LIBRARY_SELECT_LINKS" => $library_links]);
        ?>
            <ul class="navbar-nav">
                <?php echo display_navbar_links(); ?>
            </ul>
        </div>


    </div>

</nav>
<?php
if (defined('BREADCRUMB')) {
    echo display_breadcrumbs();
}?>
