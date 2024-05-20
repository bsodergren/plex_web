<?php
/**
 *  Plexweb
 */

use Plex\Modules\Display\Display;
use Plex\Modules\Display\VideoDisplay;
use Plex\Modules\Playlist\Playlist;
use Plex\Modules\Process\Forms;
use Plex\Template\Render;

/**
 * plex web viewer.
 */

require_once '_config.inc.php';

$Playlist = (new VideoDisplay('Playlist'))->init();

// exit;

$form = new Formr\Formr('bootstrap', 'hush');
if ($form->submitted()) {
    if (isset($_REQUEST['action'])) {
        // $data = $form->validate('playlist_name,action');
        $p = new Forms($_REQUEST);
        $p->process();

        echo $p->redirect;
    }
}

// if (isset($_REQUEST['add'])) {

//     $table_body_html = $form->open('MyForm');
//     $table_body_html .= $form->text('playlist_name','Your name','John Wick');
//     $table_body_html .= $form->hidden('action','addPlaylistData');
//     // $table_body_html .= $form->hidden('action','addPlaylistData');
//     $table_body_html .= $form->submit_button();

//     $table_body_html .=  $form->close();
//     goto render;
// }
// $playlist_id = null;
// $playlist_links = '';

if (isset($_REQUEST['playlist_id'])) {
    $playlist_id           = $_REQUEST['playlist_id'];
    $Playlist->playlist_id = $playlist_id;
}
if (null === $playlist_id) {
    $results = (new Playlist())->showAllPlaylists();
} else {
    $results = (new Playlist())->getPlaylist($playlist_id);
}
Display::$CrubURL['AddPlaylist'] = 'playlist.php?add=1';
$Playlist->Display($results);

// Render::Display($table_body_html, 'pages/Playlist/body');
