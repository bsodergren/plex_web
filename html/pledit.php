<?php

use Plex\Modules\Process\Mediatag;
use Plex\Template\Render;

require_once '_config.inc.php';

// configuration
$url = __URL_HOME__.'/pledit.php';
$file = '/home/bjorn/plex/XXX/Playlists/plexplaylist.txt';
 $pl = new Mediatag('Playlists');


if (!file_exists($file)) {
    touch($file);
}
// check if form has been submitted
if (isset($_POST['text'])) {
    $text = $_POST['text'];

    // save the text contents
    file_put_contents($file, $text);

//    $pl->playlistClean($file);
    if ('Download' == $_POST['submit']) {
         $url = $url.'?download=true';
    //     utmdump($popurl);

    //     echo '<script>';
    //     echo "window.open('$popurl','ff');".\PHP_EOL;
    //     echo "window.location.href = '$url';".\PHP_EOL;
    //     echo '</script>';
    //     exit;
    //    sleep(2);

    }
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
$cols = 60;
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

if (isset($_REQUEST['download'])) {
    if (file_exists($file)) {
      //  $pl->playlistDownload($file);
        $pl->mediadownload();
        echo "fadsfsda";
    }
    exit;
}

