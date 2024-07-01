<?php

namespace Plex\Template;

use UTMTemplate\Render;
use UTMTemplate\HTML\ProgressBar;
use UTMTemplate\Filesystem\Fileloader;

class Elements extends \UTMTemplate\HTML\Elements
{


    public static function pagestart()
    {
        echo '<main class="container-fluid container-sidenav my-3 py-3">';
    }
    public static function pageend()
    {
        echo '</main>';
    }

    public static function javaRefresh($url, $timeout_sec = 0, $text = '')
    {
        global $_REQUEST;

        if ($timeout_sec > 0) {
            $timeout = $timeout_sec / 100;
            $width = '300';
            $p = new ProgressBar();

            if ($text != '') {
                $textLength = imagefontwidth("14") * strlen($text);
                if($textLength > $width){
                    $width = $textLength;
                }
            }

            $p->setStyle(['width' => $width . 'px', 'rounded' => true]);
            $p->render();

            for ($i = 0; $i < ($size = 100); ++$i) {
                $p->setProgressBarProgress($i * 100 / $size, $text);
                usleep(1000000 * $timeout);
            }
            $p->setProgressBarProgress(100, $text);
        }

        echo Render::return(
            self::$ElementsDir . '/javascript',
            ['javascript' => "window.location.href = '" . $url . "';"]
        );
    }
}
