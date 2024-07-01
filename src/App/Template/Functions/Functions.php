<?php
/**
 *  Plexweb
 */

namespace Plex\Template\Functions;

use Plex\Modules\Database\FileListing;
use Plex\Modules\Playlist\Playlist;
use Plex\Template\Functions\Modules\AlphaSort;
use Plex\Template\Functions\Modules\metaFilters;
use Plex\Template\Functions\Traits\Breadcrumbs;
use Plex\Template\Functions\Traits\Markers;
use Plex\Template\Functions\Traits\Navbar;
use Plex\Template\Functions\Traits\PageSort;
use Plex\Template\Functions\Traits\TagCloud;
use Plex\Template\Functions\Traits\ThemeSwitcher;
use Plex\Template\Functions\Traits\Video;
use Plex\Template\Render;
use UTMTemplate\Functions\Traits\Parser;
use UTMTemplate\HTML\Elements;

class Functions extends Render
{
    use Breadcrumbs;
    use Markers;
    use Navbar;
    use PageSort;
    use Parser;
    use TagCloud;
    use ThemeSwitcher;

    use Video;

    public static $ElementsDir    = 'elements';
    public static $PlaylistDir    = 'elements/Playlist';
    public static $MarkerDir      = 'elements/Markers';
    public static $ButtonDir      = 'elements/Buttons';
    public static $RatingsDir     = 'elements/Rating';
    public static $BreadcrumbsDir = 'elements/Breadcrumb';
    public $playlist_id;

    public function __construct()
    {
    }

    public function __call($name, $arguments)
    {
    }

    public function hiddenSearch()
    {
        utmdump('serach ID => '.FileListing::$searchId);
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

     //   return Render::html(self::$ButtonDir.'/Playlist/canvas', $params);
    }

    public function AlphaBlock($match)
    {
        return (new AlphaSort())->displayAlphaBlock();
    }

    public static function playlistCanvas(){
        if (OptionIsTrue(SHOW_PLAYLIST))
        {

            $playlists               = (new Playlist())->getPlaylistSelectOptions();
            // $params['CANVAS_BODY'] = Render::html('elements/Playlist/canvas_body', []);

            return Render::html(self::$ButtonDir.'/Playlist/sidenav',  ['SelectPlaylists' => $playlists]);
        }
    }
}
