<?php
namespace Plex\Modules\Video\Playlist;

use Plex\Modules\Video\Player;

class Canvas extends Playlist
{
    private $CavasTemplate = '/canvas';
    

    public function getCanvas()
    {
        $this->addSearchBox();

        return Render::html($this->videoTemplate.'/canvas/block', ['CANVAS_LIST' => $this->canvas_item,
            'PlaylistName' => $this->playlistName, 'Canvas_Form' => $this->canvas_form]);
    }

}