<?php

use Plex\Template\Render;

define('TITLE', 'Home');
require_once '_config.inc.php';

// configuration
$url = __URL_HOME__.'/editor.php';
$file = '/home/bjorn/scripts/Mediatag/config/data/map/Words.txt';

// check if form has been submitted
if (isset($_POST['text'])) {
    foreach ($_POST['text'] as $key => $value) {
        $value = preg_replace("/[\r ]/", '', $value);
        $text .= trim($value)."\n";
    }
    dump($text);
    // save the text contents
    file_put_contents($file, $text);

    // redirect to form again
    header(sprintf('Location: %s', $url));
    printf('<a href="%s">Moved</a>.', htmlspecialchars($url));
    exit;
}

// read the textfile
$text = file_get_contents($file);

$textArray = explode(\PHP_EOL, $text);
$textPcs = array_chunk($textArray, 25);

foreach ($textPcs as $id => $wordArray) {
    $javascript .= Render::javascript('editor/form', ['id' => $id]);
    $stylesheet .= Render::stylesheet('editor/form', ['id' => $id]);
    $textBlocks .= Render::html('editor/textblock',
        ['WordWrapPart' => 'text[]',
            'id' => $id,
            'TextList' => implode(\PHP_EOL, $wordArray),
        ]);
    //  break;
}
$html = Render::html('editor/main', ['TextBlocks' => $textBlocks,
    'Javascript' => $javascript,
    'Stylesheet' => $stylesheet,
    'FormAction' => $url]);
// dump($html);
// $html = Render::html('editor/main', [ 'WordMap' => $text]);

Render::Display($html);
