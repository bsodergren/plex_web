<?php
/**
 *  Plexweb
 */

namespace Plex\Template\Functions\Traits;

use Plex\Modules\Video\Markers\Markers as vMarkers;

trait Markers
{
    public object $Markers;

    public function showMarkers($matches)
    {
        $var = $this->parseVars($matches);
        utmdump($var);
        $this->Markers = new vMarkers($var);
        $Markers       = $this->Markers->displayMarkers();

        return $Markers;
    }
}
