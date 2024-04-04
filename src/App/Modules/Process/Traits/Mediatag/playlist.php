<?php

namespace Plex\Modules\Process\Traits\Mediatag;

trait playlist
{
    public $playlistCmd = '/home/bjorn/scripts/Mediatag/bin/playlist';

    public function playlistClean($playlistName)
    {
        $cmd = [$this->playlistCmd, '-c', '--path', $this->path, $playlistName];
        $this->runCmd($cmd, 'ProcessOutput');
    }

    public function playlistDownload($playlistName)
    {
        $cmd = [$this->playlistCmd,  '--path', $this->path, $playlistName];
        $this->runCmd($cmd, 'ProcessOutput');
    }
}
