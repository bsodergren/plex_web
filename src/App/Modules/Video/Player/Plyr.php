<?php

namespace Plex\Modules\Video\Player;

use Plex\Modules\Video\Player;
use Plex\Modules\Video\Playlist\Playlist;

class Plyr extends Player
{
    public $templatePlayer = 'pages/Video/Plyr';
    public $id;
    public $parent;

    public function __construct($object)
    {
       // parent::__construct();
      //  parent::$PlayerTemplate = $this->templatePlayer;
        $this->parent = $object;
        $this->id = $this->videoId();

        // $object->playlist_id = $this->playlist->playlist_id;
    }

    public function ShowVideoPlayer()
    {
        // $this->id = $this->videoId();

        $this->VideoDetails();

        utminfo([__METHOD__,$this->videoId()]);
    }
}
