<?php
namespace Plex\Template\Display;

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
