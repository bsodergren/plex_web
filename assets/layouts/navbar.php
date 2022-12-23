<nav class="navbar navbar-expand-lg sticky-top  navbar-primary bg-dark ">
    <div class="container-fluid">

        <a class="navbar-brand text-light" href="home.php">
        <img src="<?php echo __LAYOUT_URL__; ?>/images/logonotext.png" alt="" width="50" height="50" class="mr-3"><?php echo APP_NAME; ?></a>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php
                $sql = query_builder('DISTINCT(library) as library ');
                $result2 = $db->query($sql);
                foreach ($result2 as $k => $v) {
                    echo display_navbar_left_links('home.php?library='.$v['library'], $v['library']);
                }
                ?>
            </ul>

            <ul class="navbar-nav">
                <?php echo display_navbar_links(); ?>
            </ul>
        </div>
    </div>
</nav>