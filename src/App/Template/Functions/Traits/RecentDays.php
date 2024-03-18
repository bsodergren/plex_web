<?php

namespace Plex\Template\Functions\Traits;

use Plex\Core\Request;
use Plex\Template\Render;
use UTMTemplate\HTML\Elements;

trait RecentDays
{

    private static $RecentDaysDir = 'elements/RecentDays';

    public function displayDayList()
    {

        return Render::return(self::$RecentDaysDir.'/select',
        ['Options' => Elements::SelectOptions([1,2,4,6,8], $_SESSION['days'])]);
       
    }
}
