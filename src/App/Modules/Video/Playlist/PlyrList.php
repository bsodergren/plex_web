<?php
namespace Plex\Modules\Video\Playlist;

use Plex\Modules\Video\Player;

class PlyrList extends Playlist
{
    
    
    public function getPlyrList()
    {
        return Render::html($this->videoTemplate.'/container/playlist', ['PLAY_LIST' => $this->plyr_item]);
    }

}