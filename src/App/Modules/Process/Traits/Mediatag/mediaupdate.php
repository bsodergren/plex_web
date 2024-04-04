<?php

namespace Plex\Modules\Process\Traits\Mediatag;

trait mediaupdate
{
    public $mediaupdate = '/home/bjorn/scripts/Mediatag/bin/mediaupdate';

    public function mediaUpdate()
    {
        $cmd = [$this->mediaupdate, '-q', '--path', $this->path, '-U', '-f', $this->fileList[0]];
        $this->runCmd($cmd, 'ProcessOutput');
    }
}
