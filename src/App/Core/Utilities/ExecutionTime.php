<?php
/**
 *  Plexweb
 */

namespace Plex\Core\Utilities;

/*
 * plex web viewer
 */

class ExecutionTime
{
    private $startTime;

    private $endTime;

    public function __toString()
    {
        return 'This process used '.$this->runTime($this->endTime, $this->startTime, 'utime')." ms for its computations\nIt spent ".$this->runTime($this->endTime, $this->startTime, 'stime')." ms in system calls\n";
    } // end __toString()

    public function start()
    {
        $this->startTime = getrusage();
    } // end start()

    public function end()
    {
        $this->endTime = getrusage();
    } // end end()

    private function runTime($ru, $rus, $index)
    {
        return ($ru["ru_{$index}.tv_sec"] * 1000 + (int) ($ru["ru_{$index}.tv_usec"] / 1000)) - ($rus["ru_{$index}.tv_sec"] * 1000 + (int) ($rus["ru_{$index}.tv_usec"] / 1000));
    } // end runTime()
} // end class
