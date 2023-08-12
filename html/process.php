<?php
/**
 * Command like Metatag writer for video files.
 */

require_once '_config.inc.php';
if (true == $_GET['q']) {
    $q    = $_GET['q'];

    $db->where('tag_name', '%'.$q.'%', 'like');
    $tags = $db->get('tags');

    if ($db->count > 0) {
        foreach ($tags as $tag) {
            echo $tag['tag_name']."\n";
        }
    }
    exit;
}
// print_r2($_REQUEST);
// exit;
process_form($_POST['redirect']);
