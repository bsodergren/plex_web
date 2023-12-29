<?php
/**
 * plex web viewer
 */

namespace Plex\Template;

use Plex\Database\PlexSql;

/**
 * plex web viewer.
 */

/**
 * plex web viewer.
 */
class Render
{
    public $_SERVER;
    public $_SESSION;
    public $_REQUEST;
    public $navigation_link_array;
    public static $CSS_THEMES = [];

    public function __construct($navigation_link_array)
    {
        global $_SESSION;
        global $_REQUEST;
        global $_SERVER;

        $this->_SESSION              = $_SESSION;
        $this->navigation_link_array = $navigation_link_array;
        $this->_REQUEST              = $_REQUEST;
        $this->_SERVER               = $_SERVER;
    }

    public static function display_sort_options($url_array)
    {
        $html        = '';
        $request_uri = '';
        $sep         = '?';
        $current     = '';

        if ('' != $url_array['query_string']) {
            parse_str($url_array['query_string'], $query_parts);

            $current     = 'studio';

            if (isset($url_array['direction'])) {
                $query_parts['direction'] = $url_array['direction'];
            }

            if (isset($query_parts['sort'])) {
                $current = $query_parts['sort'];
                unset($query_parts['sort']);
            }

            $request_uri = '?'.http_build_query($query_parts);
            $sep         = '&';
        }

        foreach ($url_array['sort_types'] as $key => $value) {
            $bg             = '';

            if ($current == $value) {
                $bg = ' active';
            }
            $class          = 'btn btn-primary btn-m'.$bg;
            $request_string = $request_uri.$sep.'sort='.$value;

            $html .= self::display_directory_navlinks($url_array['url'], $key, $request_string, $class, 'role="button" aria-pressed="true"')."\n";
        }

        return $html;
    } // end display_sort_options()

    public static function display_directory_navlinks($url, $text, $request_uri = '', $class = '', $additional = '')
    {
        global $_SESSION;
        global $_REQUEST;

        $request_string = '';

        if ('' != $request_uri) {
            $request_string = $request_uri;
        }
        if ('' != $class) {
            $class = ' class="'.$class.'"';
        }

        // $link_url = $url . "?" . $request_key ."&genre=".$_REQUEST["genre"]."&". ;
        $html           = "<a href='".$url.$request_string."' ".$class.' '.$additional.'>'.$text.'</a>';

        return $html;
    } // end display_directory_navlinks()

    public static function display_navbar_left_links($url, $text, $js = '')
    {
        $style = '';
        global $_SESSION;

        if ($text == $_SESSION['library']) {
            $style = ' style="background:#778899"';
        }

        $array = [
            'MENULINK_URL'  => $url,
            'MENULINK_JS'   => $style,
            'MENULINK_TEXT' => $text,
        ];

        return Template::GetHTML('base/navbar/library_links', $array);
    } // end display_navbar_left_links()

    public static function display_navbar_links()
    {
        $html          = '';
        $dropdown_html = '';
        global $navigation_link_array,$login_link_array;
        global $_REQUEST;

        if (!isset($_SESSION['auth'])
        || 'verified' != $_SESSION['auth']) {
            $navigation_link_array = $login_link_array;
        }

        foreach ($navigation_link_array as $name => $link_array) {
            if ('dropdown' == $name) {
                $dropdown_html = '';

                foreach ($link_array as $dropdown_name => $dropdown_array) {
                    $dropdown_link_html = '';

                    foreach ($dropdown_array as $d_name => $d_values) {
                        $array = [
                            'DROPDOWN_URL_TEXT' => $d_name,
                            'DROPDOWN_URL'      => $d_values,
                        ];
                        $dropdown_link_html .= Template::GetHTML('base/navbar/menu_dropdown_link', $array);
                    }

                    $array              = [
                        'DROPDOWN_TEXT'  => $dropdown_name,
                        'DROPDOWN_LINKS' => $dropdown_link_html,
                    ];

                    $dropdown_html .= Template::GetHTML('base/navbar/menu_dropdown', $array);
                }
            } else {
                if (true == $link_array['studio']) {
                    if ($_REQUEST['studio']) {
                        $link_array['url'] = $link_array['url'].'?studio='.$_REQUEST['studio'];
                    }
                    if ($_REQUEST['substudio']) {
                        $link_array['url'] = $link_array['url'].'?substudio='.$_REQUEST['substudio'];
                    }
                }

                $array    = [
                    'MENULINK_URL'  => $link_array['url'],
                    'MENULINK_JS'   => $link_array['js'],
                    'MENULINK_TEXT' => $link_array['text'],
                ];

                $url_text = Template::GetHTML('base/navbar/menu_link', $array);

                if (true == $link_array['secure'] && 'bjorn' != $_SERVER['REMOTE_USER']) {
                    $html = $html.$url_text."\n";
                } else {
                    $html = $html.$url_text."\n";
                }
            } // end if
        } // end foreach

        return $html.$dropdown_html;
    } // end display_navbar_links()

    public static function display_theme_dropdown()
    {
        $theme_options = Template::GetHTML('base/navbar/theme/option', ['THEME_NAME' => 'Default', 'THEME_OPTION' => 'none']);
        foreach (self::$CSS_THEMES as $theme) {
            $theme_options .= Template::GetHTML('base/navbar/theme/option', ['THEME_NAME' => ucfirst($theme).' Theme', 'THEME_OPTION' => $theme.'-theme']);
        }

        return Template::GetHTML('base/navbar/theme/select', ['THEME_OPTIONS' => $theme_options]);
    }

    public static function display_breadcrumbs()
    {
        $crumbs_html = '';
        foreach (BREADCRUMB as $text => $url) {
            if ('' == $text) {
                continue;
            }

            $class           = 'breadcrumb-item';
            $link            = '<a href="'.$url.'">'.$text.'</a>';

            if ('' == $url) {
                $class .= ' active" aria-current="page';
                $link = $text;
            }

            $params['CLASS'] = $class;
            $params['LINK']  = $link;
            $crumbs_html .= Template::GetHTML('base/navbar/crumb', $params);
        }

        if (\defined('USE_FILTER')) {
            $genre_box_html  = self::display_filter('genre');
            $artist_box_html = self::display_filter('artist');
            $studio_box_html = self::display_filter('studio');
            foreach ($_REQUEST as $name => $value) {
                if ('' != $value) {
                    $hidden .= self::add_hidden($name, $value);
                }
            }
        }

        return Template::GetHTML('base/navbar/breadcrumb', ['CRUMB_LINKS' => $crumbs_html,
            'GENREFILTERBOX'                                              => $genre_box_html,
            'ARTISTFILTERBOX'                                             => $artist_box_html,
            'STUDIOFILTERBOX'                                             => $studio_box_html,
            'HIDDEN'                                                      => $hidden]);
    }

    public static function display_filter($tag)
    {
        $selected          = '';
        $clear             = $tag;
        foreach ($_REQUEST as $name => $value) {
            if ($name == $tag) {
                if ('' != $value) {
                    $selected = $value;
                    $clear    = 'Clear '.$tag;

                    continue;
                }
            }
        }

        $genreArray        = PlexSql::getFilterList($tag);
        $params['NAME']    = $tag;
        $params['OPTIONS'] = self::display_SelectOptions($genreArray, $selected, $clear);

        return Template::GetHTML('base/navbar/select/select_box', $params);
    }

    public static function display_SelectOptions($array, $selected = '', $blank = null)
    {
        $html           = '';
        $default_option = '';
        $default        = '';
        $checked        = '';
        foreach ($array as $val) {
            $checked = '';
            if ($val == $selected) {
                $checked = ' selected';
            }
            $html .= '<option class="filter-option" value="'.$val.'" '.$checked.'>'.$val.'</option>'."\n";
        }
        if (null !== $blank) {
            if ('' == $checked) {
                $default = ' selected';
            }
            $default_option = '<option class="filter-option" value=""  '.$default.'>'.$blank.'</option>'."\n";
        }

        return $default_option.$html;
    }
    public static function add_hidden($name, $value, $attributes = '')
    {
        $html = '';
        $html .= '<input '.$attributes.' type="hidden" name="'.$name.'"  value="'.$value.'">';

        return $html."\n";
    }

    public static function draw_checkbox($name, $value, $text = '')
    {
        global $pub_keywords;

        $checked       = '';
        $current_value = $value;

        if (1 == $current_value) {
            $checked = 'checked';
        }

        $html          = '<input type="hidden" name="'.$name.'" value="0">';
        $html .= '<input class="form-check-input" type="checkbox" name="'.$name.'" value=1 '.$checked.'>'.$text;

        return $html;
    }

}
