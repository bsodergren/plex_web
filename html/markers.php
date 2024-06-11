<?php

use Plex\Template\Render;
use UTMTemplate\HTML\Elements;

use Plex\Core\Request;
use Plex\Modules\Database\FileListing;
use Plex\Modules\Display\Display;
use Plex\Modules\Display\VideoDisplay;
/*
 * plex web viewer
 */

// define('__TAG_CAT_CLASS__', 'border border-2 border-dark  mx-2 d-flex');

require_once '_config.inc.php';


$html = Render::html('pages/Markers/main',
    [
        'BackButton' => Elements::url(__THIS_FILE__,"back"),
        // 'TAG_CLOUD_HTML' => Elements::keyword_cloud('genre'),
        //    'TAG_CLOUD_KEYWORD' => Elements::keyword_cloud('keyword'),
    ]);
Render::Display($html,'pages/Markers/body');
