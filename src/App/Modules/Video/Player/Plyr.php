<?php

namespace Plex\Modules\Video\Player;

use Plex\Modules\Video\Player;
use Plex\Modules\Video\Playlist\Playlist;

class Plyr extends Player
{
    public $templatePlayer = '/Video/Plyr';
    public $id;

    public function __construct($object)
    {
       // parent::__construct();
       $this->parent = $object;
       $this->id =         $this->videoId();
       $this->playlist = new Playlist();
        
    }
    public function ShowVideoPlayer()
    {
        $this->id =         $this->videoId();

        $this->VideoDetails();

        utmdump($this->videoId());

    }

    // 
}