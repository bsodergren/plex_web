<?php

namespace Plex\Modules\Video\Player;

use Plex\Modules\Video\Player;

class Plyr extends Player
{
    public $templatePlayer = '/Plyr';
    public $id;

    public function __construct()
    {
       // parent::__construct();
        
    }
    public function ShowVideoPlayer()
    {
        $this->id =         $this->videoId();

       // $this->VideoDetails();

        dump(get_class_vars(get_class($this)));

    }
    // 
}