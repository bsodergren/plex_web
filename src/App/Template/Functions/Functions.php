<?php

namespace Plex\Template\Functions;

use Plex\Modules\Database\FileListing;
use Plex\Modules\Playlist\Playlist;
use Plex\Template\Functions\Modules\AlphaSort;
use Plex\Template\Functions\Modules\metaFilters;
use Plex\Template\Functions\Traits\Breadcrumbs;
use Plex\Template\Functions\Traits\Navbar;
use Plex\Template\Functions\Traits\PageSort;
use Plex\Template\Functions\Traits\RecentDays;
use Plex\Template\Functions\Traits\TagCloud;
use Plex\Template\Functions\Traits\ThemeSwitcher;
use Plex\Template\Functions\Traits\Video;
use Plex\Template\Render;
use UTMTemplate\Functions\Traits\Parser;
use UTMTemplate\HTML\Elements;
use UTMTemplate\Template;

class Functions extends Render
{
    use Breadcrumbs;
    use Navbar;
    use PageSort;
    use Parser;
    use RecentDays;
    use TagCloud;
    use ThemeSwitcher;

    use Video;

    public static $ElementsDir = 'elements';
    public static $PlaylistDir = 'elements/Playlist';
    public static $ChapterDir = 'elements/Chapters';
    public static $ButtonDir = 'elements/Buttons';
    public static $RatingsDir = 'elements/Rating';
    public static $BreadcrumbsDir = 'elements/Breadcrumb';

    public function __construct()
    {
    }

    public function __call($name, $arguments)
    {

    }

    public function hiddenSearch()
    {
        if (null === FileListing::$searchId) {
            return '';
        }

        return Elements::add_hidden('search_id', FileListing::$searchId, 'id="searchId"');
    }

    public function displayFilters()
    {
        return (new metaFilters())->displayFilters();
    }

    public function metaFilters($match)
    {
        $method = $match[2];

        return (new metaFilters())->{$method}();
    }

    public function playListButton()
    {
        $playlists = (new Playlist())->getPlaylistSelectOptions();
        $params['CANVAS_HEADER'] = Render::html(self::$ButtonDir.'/Playlist/canvas_header', []);
        $params['CANVAS_BODY'] = Render::html(self::$ButtonDir.'/Playlist/canvas_body', ['SelectPlaylists' => $playlists]);
        // $params['CANVAS_BODY'] = Render::html('elements/Playlist/canvas_body', []);

        return Render::html(self::$ButtonDir.'/Playlist/canvas', $params);
    }

    public function AlphaBlock($match)
    {
        return (new AlphaSort())->displayAlphaBlock();
    }

    public function pageHeader($matches)
    {
        Layout::Header();
    }

    public function pageFooter($matches)
    {
        Layout::Footer();
    }
}
