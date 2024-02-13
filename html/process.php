<?php
/**
 * plex web viewer
 */

use Plex\Core\PlexSql;
use Plex\Template\Render;
use Plex\Core\ProcessForms;
use Plex\Template\Template;
use Plex\Template\Layout\Header;

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
$t     = new Template();
if (array_key_exists('action', $_REQUEST)) {
    if ('refresh' == $_REQUEST['action']) {
        define('TITLE', 'Home');

        Header::Display();
    }
}
logger('_REQUEST', $_REQUEST);

$forms = new ProcessForms($_REQUEST);
echo $forms->redirect;
