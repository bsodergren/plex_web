<?php

use Plex\Modules\Process\Forms;
use UTMTemplate\Template;

require_once '_config.inc.php';

utmdump([__METHOD__, $_REQUEST]);

if (true == $_GET['q']) {
    $q = $_GET['q'];

    $db->where('tag_name', '%'.$q.'%', 'like');
    $tags = $db->get(Db_TABLE_TAGS);

    if ($db->count > 0) {
        foreach ($tags as $tag) {
            echo $tag['tag_name']."\n";
        }
    }

    exit;
}
// $t = new Template();
if (array_key_exists('action', $_REQUEST)) {
    if ('refresh' == $_REQUEST['action']) {
        Layout::Header();
    }
}

logger('_REQUEST', $_REQUEST);

$ProcessReq = new Forms($_REQUEST);
$ProcessReq->process();
echo $ProcessReq->redirect;
