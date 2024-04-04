<?php

namespace Plex\Modules\Process;

use Nette\Utils\Callback;
use UTMTemplate\Template;
use Symfony\Component\Process\Process;
use Plex\Modules\Process\Traits\Mediatag\mediadb;
use Plex\Modules\Process\Traits\Mediatag\playlist;
use Plex\Modules\Process\Traits\Mediatag\mediaupdate;

class Mediatag
{
    use mediadb;
    use mediaupdate;
    use playlist;

    public $fileList = [];
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

    public function runCmd($command, $callback)
    {
        $callbackCmd = Callback::check([$this, $callback]);
        $process = new Process($command);

        if (true === $this->test) {
            echo $process->getCommandLine();

            return null;
        }
        $process->setTimeout(60000);
        $process->start();
        $process->wait($callbackCmd);
    }

    public function ProcessOutput($type, $buffer)
    {
        $buffer = str_replace('\n\n', '\n', $buffer);
        utmdump($buffer);
        echo Template::put($buffer);
    }

    public function ProcessProgressBar($type, $buffer)
    {
        //  utmdump($buffer);
        $timeout = $buffer / 60;
        //  echo Template::ProgressBar($timeout, 'UpdateBar');
    }
}
