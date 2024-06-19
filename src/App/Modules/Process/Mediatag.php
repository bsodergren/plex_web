<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Process;

use Nette\Utils\Callback;
use Plex\Modules\Process\Traits\Mediatag\mediadb;
use Plex\Modules\Process\Traits\Mediatag\mediadownload;
use Plex\Modules\Process\Traits\Mediatag\mediaupdate;
use Plex\Modules\Process\Traits\Mediatag\playlist;
use Symfony\Component\Process\Process;
use UTMTemplate\Template;

class Mediatag
{
    use mediadb;
    use mediadownload;
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
        $process     = new Process($command);
        //  echo $process->getCommandLine();
        // dd($process->getCommandLine());
        if (true === $this->test) {
            return null;
        }
        $process->setTimeout(60000);
        $process->start();
        $process->wait($callbackCmd);
    }

    public function downloadOutput($type, $buffer)
    {
        $buffer = str_replace("\n", '', $buffer);

        utminfo($buffer);
        $this->p->setProgressBarHeader($buffer);

        // switch ($buffer) {
        // }
    }

    public function progressBar($type, $buffer)
    {
        // download]

        $buffer = str_replace("\n", '', $buffer);
        $buffer = str_replace("\r", '', $buffer);

        utminfo($buffer);

        switch ($buffer) {
            case str_contains($buffer, '[download]'):
                if (str_contains($buffer, 'Destination')) {
                    preg_match('/Destination:\s+(.*.mp4)/', $buffer, $destArray);
                    $this->p->setProgressBarHeader($destArray[1]);
                }
                $outputText = htmlspecialchars($buffer);
                preg_match('/([0-9.]+)\%.*ETA ([0-9:]+)/', $outputText, $output_array);
                $this->p->setProgressBarProgress($output_array[1] * 100 / 100);

                //                echo Template::put($output_array[0]);
                break;
            default:
                $this->p->setProgressBarHeader($buffer);

                break;
        }
    }

    public function ProcessOutput($type, $buffer)
    {
        $buffer = str_replace('\n\n', '\n', $buffer);
        utminfo($buffer);
        echo Template::put($buffer);
    }

    public function ProcessProgressBar($type, $buffer)
    {
        //  utminfo($buffer);
        $timeout = $buffer / 60;
        //  echo Template::ProgressBar($timeout, 'UpdateBar');
    }
}
