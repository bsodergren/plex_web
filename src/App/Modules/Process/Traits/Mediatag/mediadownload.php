<?php

namespace Plex\Modules\Process\Traits\Mediatag;

use Plex\Template\Render;

trait mediadownload
{
    public $mediadownload = '/home/bjorn/scripts/Mediatag/bin/mediadownload';

    public function mediadownload()
    {
        $cmd = [$this->mediadownload, '-q', '--path', '/home/bjorn/plex/XXX/Downloads'];
        $this->p = new Render();

        utmdump($cmd);

        $this->runCmd($cmd, 'downloadOutput');
    }


}
