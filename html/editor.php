<?php

use Plex\Template\Render;

require_once '_config.inc.php';

// configuration
$url  = __URL_HOME__.'/editor.php';
$file = '/home/bjorn/scripts/Mediatag/config/data/map/Words.txt';
// check if form has been submitted
if (isset($_POST['text'])) {
    $text =  '';

    $textArray = array_reverse($_POST['text']);
    foreach ($textArray as $key => $value) {
        $value = preg_replace("/[\r ]/", '', $value);
        $text .= trim($value)."\n";
    }

    // save the text contents
    file_put_contents($file, $text);

    // redirect to form again
    header(sprintf('Location: %s', $url));
    printf('<a href="%s">Moved</a>.', htmlspecialchars($url));
    exit;
}

$rows = 10;
$stylesheet =  '';
$textBlocks =  '';
$TextRow =  '';

// read the textfile
$text       = file_get_contents($file);
$textArray  = explode(\PHP_EOL, $text);
$textPcs    = array_chunk($textArray, $rows);
$textPcs    = array_reverse($textPcs);
$col        = 0;
$javascript = Render::javascript('pages/editor/form_func', []);
foreach ($textPcs as $id => $wordArray) {
    ++$col;
    $mouseover = '';
    $lineNo    = '<div id="line-numbers"><p></p></div>';
    if (0 == $id) {
        $javascript .= Render::javascript('pages/editor/form', ['id' => $id]);
        $mouseover = ' onmousemove="lineNumbers_'.$id.'()"';
        $lineNo    = '<div id="line-numbers-'.$id.'"><p>1</p></div>';
    }
    $stylesheet .= Render::stylesheet('pages/editor/form', ['id' => $id]);

    $textBlocks .= Render::html('pages/editor/textblock',
        ['WordWrapPart' => 'text[]',
            'MouseOver' => $mouseover,
            'LineNo'    => $lineNo,
            'Rows'      => $rows,
            'id'        => $id,
            'TextList'  => implode(\PHP_EOL, $wordArray),
        ]);

    if (5 == $col) {
        $TextRow .= Render::html('pages/editor/row',
            ['TextRow' => $textBlocks,
            ]);
        $textBlocks = '';
        $col        = 0;
    }

    //   break;
}
$TextRow .= Render::html('pages/editor/row',
    ['TextRow' => $textBlocks,
    ]);
$html = Render::html('pages/editor/main', ['TextBlocks' => $TextRow,
    'Javascript'                                  => $javascript,
    'Stylesheet'                                  => $stylesheet,
    'FormAction'                                  => $url]);
// $html = Render::html('editor/main', [ 'WordMap' => $text]);

Render::Display($html);
