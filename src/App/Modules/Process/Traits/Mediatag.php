<?php

namespace Plex\Modules\Process\Traits;

use Nette\Utils\Callback;
use Symfony\Component\Process\Process;
use UTMTemplate\Template;

class Mediatag
{
    public $fileList = [];
    public $mediaupdate = '/home/bjorn/scripts/Mediatag/bin/mediaupdate';
    public $mediadb = '/home/bjorn/scripts/Mediatag/bin/mediadb';
    public $playlistCmd = '/home/bjorn/scripts/Mediatag/bin/playlist';
    public $path;
    private $test = false;

    public function __construct($path = '')
    {
        if ('' == $path) {
            $this->path = __PLEX_LIBRARY__.\DIRECTORY_SEPARATOR.$_SESSION['library'];
        } else {
            $this->path = __PLEX_LIBRARY__.\DIRECTORY_SEPARATOR.$path;
        }
    }

    public function addFile($file)
    {
        $this->fileList[] = $file;
        if (\count($this->fileList) > 0) {
            $this->path = \dirname($this->fileList[0]);
        }
    }

    public function refreshFile()
    {
        $this->mediaUpdate();
        $this->mediaDb();
        $this->updateDbTags();
        $this->updateDbInfo();
    }

    public function playlistClean($playlistName)
    {
        $callback = Callback::check([$this, 'ProcessOutput']);
        $process = new Process([$this->playlistCmd, '-c', '--path', $this->path, $playlistName]);
        utmdump($process->getCommandLine());
        if (true === $this->test) {
            return null;
        }
        $process->setTimeout(60000);
        $process->start();
        $process->wait($callback);
    }

    public function mediaUpdate()
    {
        $callback = Callback::check([$this, 'ProcessOutput']);
        $process = new Process([$this->mediaupdate, '-q', '--path', $this->path, '-U', '-f', $this->fileList[0]]);
        // utmdump($process->getCommandLine());
        if (true === $this->test) {
            return null;
        }
        $process->setTimeout(60000);
        $process->start();
        $process->wait($callback);
    }

    public function mediaDb()
    {
        $callback = Callback::check([$this, 'ProcessOutput']);

        $process = new Process([$this->mediadb, '-q', '--path', $this->path]);
        $process->setTimeout(60000);
        // utmdump($process->getCommandLine());
        if (true === $this->test) {
            return null;
        }
        $process->start();
        $process->wait($callback);
    }

    public function updateDbInfo()
    {
        $callback = Callback::check([$this, 'ProcessOutput']);

        $process = new Process([$this->mediadb, '-q', '--path', $this->path, '-tDPi']);
        $process->setTimeout(60000);
        // utmdump($process->getCommandLine());
        if (true === $this->test) {
            return null;
        }
        $process->start();
        $process->wait($callback);
    }

    public function updateDbTags()
    {
        $callback = Callback::check([$this, 'ProcessOutput']);
        $process = new Process([$this->mediadb, '-q', '--path', $this->path, '-u']);
        $process->setTimeout(60000);
        // utmdump($process->getCommandLine());
        if (true === $this->test) {
            return null;
        }
        $process->start();
        $process->wait($callback);
    }

    public function ProcessOutput($type, $buffer)
    {
        $buffer = str_replace('\n\n', '\n', $buffer);
         utmdump($buffer);
        //echo Template::put($buffer);
    }

    public function ProcessProgressBar($type, $buffer)
    {
        //  utmdump($buffer);
        $timeout = $buffer / 60;
        //  echo Template::ProgressBar($timeout, 'UpdateBar');
    }
}
