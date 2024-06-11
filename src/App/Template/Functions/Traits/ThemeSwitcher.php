<?php
/**
 *  Plexweb
 */

namespace Plex\Template\Functions\Traits;

use Plex\Core\RoboLoader;
use Plex\Modules\Display\Display;
use Plex\Template\Render;
use UTMTemplate\Template;

trait ThemeSwitcher
{
    public $ThemeSwitcherDir = 'elements/Themes';

    public function theme_dropdown()
    {
        $theme_options = Render::html($this->ThemeSwitcherDir.'/option', ['THEME_NAME' => 'Default', 'THEME_OPTION' => 'none']);
        foreach (Display::$CSS_THEMES as $theme) {
            $theme_options .= Render::html($this->ThemeSwitcherDir.'/option', ['THEME_NAME' => ucfirst($theme).' Theme', 'THEME_OPTION' => $theme.'-theme']);
        }

        return Render::html($this->ThemeSwitcherDir.'/select', ['THEME_OPTIONS' => $theme_options]);
    }

    public function themeSwitcher($matches)
    {
        $css_dir = Template::$ASSETS_PATH.'/css/themes/';
        $files   = RoboLoader::get_filelist($css_dir, 'bootstrap.min.css', 0);
$css_html = '';
        foreach ($files as $stylesheet) {
            $dirArray = explode('/', $stylesheet);
            array_pop($dirArray);
            $theme                 = end($dirArray);
            Display::$CSS_THEMES[] = $theme;
            $stylesheet            = str_replace(Template::$ASSETS_PATH, Template::$ASSETS_URL, $stylesheet);

            $css_html .= Render::html($this->ThemeSwitcherDir.'/stylesheet', ['CSS_NAME' => $theme, 'CSS_URL' => $stylesheet]);
        }

        return $css_html;
    }
}
