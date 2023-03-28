<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="description"
        content="<?php echo APP_DESCRIPTION; ?>">
    <meta name="author" content="<?php echo APP_OWNER; ?>">
    <base href="<?php echo __URL_HOME__; ?>/" />

    <title>
        <?php echo TITLE . ' | ' . APP_NAME; ?>
    </title>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">



    <script
        src="<?php echo __LAYOUT_URL__; ?>js/app.js?<?php echo substr(md5(rand()), 0, 7); ?>">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>


    <link rel="stylesheet"
        href="<?php echo __LAYOUT_URL__; ?>css/custom.css?<?php echo substr(md5(rand()), 0, 7); ?>">
    <link rel="stylesheet"
        href="<?php echo __LAYOUT_URL__; ?>css/tags-input.css?<?php echo substr(md5(rand()), 0, 7); ?>">
</head>
<?php
    $onLoad = '';
        if (__HTML_POPUP__ == true) {
            $onLoad = "onLoad=\"popup('/plex_web/logs.php', 'logs',1000,1000)\"";
        }
        ?>

<body <?php echo $onLoad; ?>>
    <?php
        require __LAYOUT_NAVBAR__;
        ?>