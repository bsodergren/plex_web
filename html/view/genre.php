<?php

require_once '../_config.inc.php';
use Plex\Modules\Display\Layout;

Layout::Header();

$sql            = PlexSql::query_builder(Db_TABLE_VIDEO_FILE, 'count(genre) as cnt, genre',
    "library = '".$in_directory."'",
    'genre', 'genre asc');
$result         = $db->query($sql);

?>

    <main role="main" class="container">

    <?php

    echo "<ul> \n";
$allgenre_array = [];
foreach ($result as $k => $v) {
    if ('' != $v['genre']) {
        $genre_array = explode(',', $v['genre']);
        foreach ($genre_array as $x => $g) {
            if (!in_array($g, $allgenre_array)) {
                $allgenre_array[] = $g;
            }
        }
    }
}

foreach ($allgenre_array as $x => $g) {
    $sql   = "SELECT count(*) as cnt from metatags_filedb WHERE library = '".$in_directory."' AND genre LIKE '%".$g."%'";

    $rar   = $db->rawQueryOne($sql);
    $cnt   = '';
    if (isset($rar['cnt'])) {
        $cnt = $rar['cnt'];
    }
    $genre = str_replace(' ', '-', $g);
    $genre = str_replace('/', '_', $genre);

    echo "<li><a href='".__THIS_PAGE_.'?genre='.$genre."'>".$g.'</a> ('.$cnt.')<br>';
}

?>
 </ul>
 </main>
 <?php Layout::Footer(); ?>
