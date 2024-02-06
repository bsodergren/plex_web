<?php

require_once '../_config.inc.php';

define('TITLE', 'Home');

 \Plex\Template\Layout\Header::Display();

$sql             = 'select  artist from '.Db_TABLE_VIDEO_FILE." WHERE library = '".$in_directory."' and artist is not null GROUP by artist ORDER BY `artist` ASC;";
$result          = $db->query($sql);

?>

    <main role="main" class="container">

    <?php

    $new_names[] = '';
foreach ($result as $k => $v) {
    $artist    = [$v['artist']];

    if (str_contains($v['artist'], ',')) {
        $artist = explode(',', $v['artist']);
    }

    $new_names = array_merge($new_names, $artist);
}

$new_names       = array_map('trim', $new_names);
$new_names       = array_map('strtolower', $new_names);

$new_names       = array_map('ucwords', $new_names);
$new_names       = array_unique($new_names);
sort($new_names);
print_r2($new_names);

?>
 </ul>
 </main>
 <?php  \Plex\Template\Layout\Footer::Display(); ?>