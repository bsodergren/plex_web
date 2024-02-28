<?php
namespace Plex\Modules\Video\Playlist;

use Plex\Modules\Video\Player;

class Carousel extends Playlist
{
    public function getCarousel()
    {
        return Render::html($this->videoTemplate.'/carousel/block', ['CAROUSEL_INNER_HTML' => $this->carousel_item]);
    }

    public function getCarouselScript()
    {
        return Render::html($this->videoTemplate.'/carousel/js', ['PLAYLIST_ID' => $this->playlist_id]);
    }

    
    
}