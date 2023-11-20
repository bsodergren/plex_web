<?php

require_once '../_config.inc.php';

define('TITLE', 'Home');

include __LAYOUT_HEADER__;

$sql    = query_builder(Db_TABLE_VIDEO_FILE,'count(studio) as cnt, studio',
    "library = '".$in_directory."'",
    'studio', 'studio asc');

$result = $db->query($sql);

?>

    <main role="main" class="container">

    <?php

    echo "<ul> \n";
foreach ($result as $k => $v) {
    if ('' != $v['studio']) {
        $cnt        = $v['cnt'];
        $query      = query_builder(Db_TABLE_VIDEO_FILE,'count(substudio) as cnt, substudio',
            'studio like "'.$v['studio'].'"',
            'substudio', 'substudio asc');
        $alt_result = $db->query($query);

        $studio     = str_replace(' ', '-', $v['studio']);
        $studio     = str_replace('/', '_', $studio);

        echo "<li><a href='".__THIS_FILE__.'?studio='.$studio."'>".$v['studio'].'</a> ('.$cnt.')<br>';

        if (count($alt_result) > 1) {
            echo '<ul>';

            foreach ($alt_result as $k_a => $v_a) {
                if ('' != $v_a['substudio']) {
                    $cntv_a    = $v_a['cnt'];
                    $substudio = str_replace(' ', '-', $v_a['substudio']);
                    $substudio = str_replace('/', '_', $substudio);
                    echo "<li><a href='".__THIS_FILE__.'?studio='.$substudio."'>".$v_a['substudio'].'</a>('.$cntv_a.') <br>';
                }
            }
            echo '</ul>';
        }
    }
}
echo "</ul><li><a href='".__THIS_FILE__."?studio=NULL'>Studio not Set</a><br>";

?>
 </ul>
 </main>
 <?php include __LAYOUT_FOOTER__; ?>