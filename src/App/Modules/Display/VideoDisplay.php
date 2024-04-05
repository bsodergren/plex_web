<?php
namespace Plex\Modules\Display;


class VideoDisplay
{

    private $class;

    public function __construct($display = 'List')
    {
        $this->class = 'Plex\\Modules\\Display\\Layout\\'.$display.'Display';
    }


    public function init($template_base = 'filelist')
    {
        return new $this->class($template_base);
    }

}
