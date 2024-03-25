<?php
namespace Plex\Modules\Video\Playlist;

use UTMTemplatec\Render;
use Plex\Modules\Video\Player;

class PlyrList extends Playlist
{
    
    public function getPlyrList()
    {
        return Render::return($this->videoTemplate.'/container/playlist', ['PLAY_LIST' => $this->plyr_item]);
    }
   

}