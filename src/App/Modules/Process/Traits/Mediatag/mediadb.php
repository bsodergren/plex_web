<?php

namespace Plex\Modules\Process\Traits\Mediatag;

trait mediadb
{
    public $mediadb = '/home/bjorn/scripts/Mediatag/bin/mediadb';

    public function mediaDb()
    {
        $cmd = [$this->mediadb, '-q', '--path', $this->path];
        $this->runCmd($cmd, 'ProcessOutput');
    }

    public function updateDbInfo()
    {
        $cmd = [$this->mediadb, '-q', '--path', $this->path, '-tDi'];
        $this->runCmd($cmd, 'ProcessOutput');
    }

    public function updateDbTags()
    {
        $cmd = [$this->mediadb, '-q', '--path', $this->path, '-u'];
        $this->runCmd($cmd, 'ProcessOutput');
    }
}
