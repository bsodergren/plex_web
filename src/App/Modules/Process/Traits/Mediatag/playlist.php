<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Process\Traits\Mediatag;

use Plex\Template\Render;

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

        $this->p = new Render();

        echo '<div style="width: 600px;">';

        $this->p->render();
        echo '</div>';

        $this->runCmd($cmd, 'progressBar');
        $this->p->setProgressBarProgress(100);
    }
}
