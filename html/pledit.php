<?php

use Plex\Modules\Process\Traits\Mediatag;
use Plex\Template\Render;

require_once '_config.inc.php';

// configuration
$url = __URL_HOME__.'/pledit.php';
$file = '/home/bjorn/plex/XXX/Playlists/plexplaylist.txt';

// check if form has been submitted
if (isset($_POST['text'])) {
    $text = $_POST['text'];

    // save the text contents
    file_put_contents($file, $text);

    $pl = new Mediatag('Playlists');
    $pl->playlistClean($file);

    // redirect to form again
    header(sprintf('Location: %s', $url));
    printf('<a href="%s">Moved</a>.', htmlspecialchars($url));
    exit;
}

$rows = 10;
// read the textfile
$text = file_get_contents($file);
$array = explode("\n", $text);
$lines = count($array);
$cols = 0;
foreach ($array as $line) {
    $w = strlen($line);
    if ($w > $cols) {
        $cols = $w;
    }
}

$stylesheet .= Render::stylesheet('editor/form', ['id' => $id]);

$textBlocks .= Render::html('editor/textblock',
    ['WordWrapPart' => 'text',
        'id' => 'playlistText',
        'TextList' => $text,
        'Rows' => $lines,
        'Cols' => $cols + 10,
    ]);

//   break;

$html = Render::html('editor/main', ['TextBlocks' => $textBlocks,
    'Javascript' => $javascript,
    'Stylesheet' => $stylesheet,
    'FormAction' => $url]);
// $html = Render::html('editor/main', [ 'WordMap' => $text]);

Render::Display($html);
