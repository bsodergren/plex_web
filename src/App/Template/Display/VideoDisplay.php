<?php
namespace Plex\Template\Display;

use Plex\Template\Functions\Traits\Video;

class VideoDisplay
{

    public function __construct($display = 'List')
    {
        $this->class = 'Plex\\Template\\Display\\Layout\\'.$display.'Display';
    }


    public function init($template_base = 'filelist')
    {
        return new $this->class($template_base);
    }

}
