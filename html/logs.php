<?php
use Plex\Core\Request;
use Plex\Modules\Database\FileListing;
use Plex\Modules\Display\Display;
use Plex\Modules\Display\VideoDisplay;
use Plex\Template\Render;
use Nette\Utils\FileSystem;
use UTMTemplate\HTML\Elements;


require_once '_config.inc.php';

function getFiles($directory) {
    // Try to open the directory
    if($dir = opendir($directory)) {
        // Create an array for all files found
        $tmp = Array();

        // Add the files
        while($file = readdir($dir)) {
            // Make sure the file exists
            if($file != "." && $file != ".." && $file[0] != '.') {
                // If it's a directiry, list all files within it
                if(is_dir($directory . "/" . $file)) {
                    $tmp2 = getFiles($directory . "/" . $file);
                    if(is_array($tmp2)) {
                        $tmp = array_merge($tmp, $tmp2);
                    }
                } else {
                    array_push($tmp, $directory . "/" . $file);
                }
            }
        }

        // Finish off the function
        closedir($dir);
        return $tmp;
    }
}
$err_array = getFiles(__ERROR_LOG_DIRECTORY__);
utminfo($err_array);

foreach($err_array as $file )
{
    $file_link = str_replace(APP_HTML_ROOT,APP_HOME,$file);
    $file_text = basename($file_link);
    if($file_text == 'default.log'){
        continue;
    }
    $dirName = basename(dirname($file_link,2));
    $paths[$dirName][] = $file_link;

}
utminfo($paths);
$groups = '';
foreach($paths as $cat => $files )
{
    $fileButtons = '';
    if($cat == 'logs'){
        continue;
    }
    foreach($files as $file) {
        $file_link = str_replace(APP_HTML_ROOT,APP_HOME,$file);
        $file_text = basename($file_link);
        if($file_text == 'default.log'){
            continue;
        }
        $fileButtons .=  Render::html('pages/Logs/link', ['url' => $file_link,'text' => $file_text]);
    }

    $groups .=  Render::html('pages/Logs/block', ['cat' => $cat,'fileButtons' => $fileButtons,
    'ACCORDIAN_ID'     => Display::RandomId('loggers_')]);
   // $groups .= Render::html('pages/Logs/group', ['block' => $block ]);
}


$html = Render::html('pages/Logs/body', ['TextBlocks' => $groups]);
// $html = Render::html('editor/main', [ 'WordMap' => $text]);

Render::Display($html);
