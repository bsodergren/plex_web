<?php
namespace Plex\Template\Display;

use Plex\Core\PlexSql;
use Plex\Template\Render;
use Plex\Core\Utilities\Colors;

/**
 * plex web viewer
 */

/**
 * plex web viewer.
 */
class Display 
{
    public $_SERVER;
    public $_SESSION;
    public $_REQUEST;
    public $navigation_link_array;
    public static $CSS_THEMES = [];
    public static $CrubURL    = [];
    private $model_popup   = '';
    private $model_buttons = [];
    public static $Random;


    public function __construct($navigation_link_array = '')
    {
        global $_SESSION;
        global $_REQUEST;
        global $_SERVER;

        $this->_SESSION              = $_SESSION;
        $this->navigation_link_array = $navigation_link_array;
        $this->_REQUEST              = $_REQUEST;
        $this->_SERVER               = $_SERVER;
    }


    

    public static function sort_options($url_array)
    {
        $html        = '';
        $request_uri = '';
        $sep         = '?';
        $current     = '';

        if ('' != $url_array['query_string']) {
            parse_str($url_array['query_string'], $query_parts);
            unset($query_parts['alpha']);
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
        $i           = 0;
        $max         = count($url_array['sort_types']);
        foreach ($url_array['sort_types'] as $key => $value) {
            $bg             = '';
            $pill           = '';
            if (0 == $i) {
                $pill = ' rounded-start-pill';
            }
            ++$i;
            if ($i == $max) {
                $pill = ' rounded-end-pill';
            }

            if ($current == $value) {
                $bg = ' active';
            }
            $class          = 'nav-link text-light'.$bg;//.$pill;
            $request_string = $request_uri.$sep.'sort='.$value;
            $html .= self::directory_navlinks($url_array['url'], $key, $request_string, $class, 'role="button" aria-pressed="true"')."\n";
        }

        return $html;
    } // end sort_options()

    public static function directory_navlinks($url, $text, $request_uri = '', $class = '', $additional = '')
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
        $html           = "<li class='nav-item'><a href='".$url.$request_string."' ".$class.' '.$additional.'>'.$text.'</a></li>';

        return $html;
    } // end directory_navlinks()

    public static function navbar_left_links($url, $text, $js = '')
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

        return Render::html('base/navbar/library_links', $array);
    } // end navbar_left_links()

    public static function navbar_links()
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
            $is_active = '';
            if ('dropdown' == $name) {
                $dropdown_html = '';

                foreach ($link_array as $dropdown_name => $dropdown_array) {
                    $dropdown_link_html = '';

                    foreach ($dropdown_array as $d_name => $d_values) {
                        $array = [
                            'DROPDOWN_URL_TEXT' => $d_name,
                            'DROPDOWN_URL'      => $d_values,
                        ];
                        $dropdown_link_html .= Render::html('base/navbar/menu_dropdown_link', $array);
                    }

                    $array              = [
                        'DROPDOWN_TEXT'  => $dropdown_name,
                        'DROPDOWN_LINKS' => $dropdown_link_html,
                    ];

                    $dropdown_html .= Render::html('base/navbar/menu_dropdown', $array);
                }
            } else {
                if (true == $link_array['studio']) {
                    if ($_REQUEST['studio']) {
                        $url = $link_array['url'].'?studio='.$_REQUEST['studio'];
                    }
                    if ($_REQUEST['substudio']) {
                        $url = $link_array['url'].'?substudio='.$_REQUEST['substudio'];
                    }
                }

                if (__THIS_PAGE__ == basename($link_array['url'], '.php')) {
                    $is_active = ' active';
                }
                $array    = [
                    'MENULINK_URL'  => $link_array['url'],
                    'MENULINK_JS'   => $link_array['js'],
                    'MENULINK_TEXT' => $link_array['text'],
                    'ACTIVE'        => $is_active,
                ];

                $url_text = Render::html('base/navbar/menu_link', $array);

                if (true == $link_array['secure'] && 'bjorn' != $_SERVER['REMOTE_USER']) {
                    $html = $html.$url_text."\n";
                } else {
                    $html = $html.$url_text."\n";
                }
            } // end if
        } // end foreach

        return $html.$dropdown_html;
    } // end navbar_links()

    public static function theme_dropdown()
    {
        $theme_options = Render::html('base/navbar/theme/option', ['THEME_NAME' => 'Default', 'THEME_OPTION' => 'none']);
        foreach (self::$CSS_THEMES as $theme) {
            $theme_options .= Render::html('base/navbar/theme/option', ['THEME_NAME' => ucfirst($theme).' Theme', 'THEME_OPTION' => $theme.'-theme']);
        }

        return Render::html('base/navbar/theme/select', ['THEME_OPTIONS' => $theme_options]);
    }

    public static function breadcrumbs()
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
            $crumbs_html .= Render::html('base/navbar/crumb', $params);
        }

        

        return Render::html('base/navbar/breadcrumb', ['CRUMB_LINKS' => $crumbs_html]);
    }

    public static function filter($tag)
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
        $params['OPTIONS'] = self::SelectOptions($genreArray, $selected, $clear);

        return Render::html('base/navbar/select/select_box', $params);
    }

    public static function SelectOptions($array, $selected = '', $blank = null)
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
            $html .= '<option class="filter-option text-bg-primary" value="'.$val.'" '.$checked.'>'.$val.'</option>'."\n";
        }
        if (null !== $blank) {
            if ('' == $checked) {
                $default = ' selected';
            }
            $default_option = '<option class="filter-option text-bg-primary" value=""  '.$default.'>'.$blank.'</option>'."\n";
        }

        return $default_option.$html;
    }

 


    public static function createBreadcrumbs()
    {
        global $tag_types;
        $request_string        = [];
        $parts                 = [];

        $request_tag           = [];
        $crumbs['Home']        = 'home.php';
        $url                   = 'files.php';

        // if (isset(self::$CrubURL['grid'])) {
        //     $url = 'files.php';
        // }

        if (isset(self::$CrubURL['list'])) {
            $url = 'gridview.php';
        }

        $crumbs[$in_directory] = '';
        parse_str($_SERVER['QUERY_STRING'], $query_parts);

        if (count($query_parts) > 0) {
            foreach ($query_parts as $key => $value) {
                if (in_array($key, $tag_types)) {
                    if ('' != $value) {
                        $request_tag[$key] = $value;
                    }
                } else {
                    if ('alpha' == $key) {
                        continue;
                    }
                    // if ('allfiles' == $key) {
                    //     continue;
                    // }
                    $request_string[$key] = $value;
                }
            }

            // dump([$request_string,$request_tag]);

            if(array_key_exists("genre",$request_tag)) {
                $url = 'genre.php';
            }
            if(array_key_exists("studio",$request_tag)) {
                // $url = 'studio.php';
                $studio_key = $request_tag['studio'];
              //  unset($request_tag['studio']);
            }
           
            $sep       = '?';
            if (count($request_string) > 0) {
                $re_string = $sep.http_build_query($request_string);
                $sep       = '&';
            }

            $crumb_url = $url.$re_string;

            if (count($request_tag) > 0) {
                $crumbs[$_SESSION['library']] = $crumb_url.$sep.http_build_query(['studio' =>  $studio_key]);

                foreach ($request_tag as $key => $value) {
                    $parts[$key]    = $value;
                    $crumbs[$value] = $crumb_url.$sep.http_build_query($parts);
                    $last           = $value;
                    // dump($key);
                }
                
                if($key == "genre"){
                    $crumbs[$last]                =  '';                    
                } else {
                    $crumbs[$last]                =  'genre.php?'.http_build_query($parts);
                }

            }
        }
        //  dump($crumbs);
        $req                   = '';
        if (__THIS_FILE__ == 'genre.php') {
            $req = '&'.http_build_query($parts);
        }
        $crumbs['All']         = $url.'?allfiles=1'.$req;

        if (isset(self::$CrubURL['grid'])) {
            $crumbs['Grid'] = self::$CrubURL['grid'].$re_string.$sep.http_build_query($parts);
            unset($crumbs['All']);
        }

        // $crumbs['List'] = "";
        if (isset(self::$CrubURL['list'])) {
            $crumbs['List'] = self::$CrubURL['list'].$re_string.$sep.http_build_query($parts);
            unset($crumbs['All']);
        }

        // $crumbs['All'] = "";
        //        if (isset( self::$CrubURL['all'] )) {
        //      }
        // dd($crumbs);
        return $crumbs;
    }

    public static function echo($text, $var = '')
    {
        /*
        if (defined('__DISPLAY_POPUP__')) {
            global $model_display;
            $model_display->model($text, $var);
            return 0;
        }
    */

        $pre_style = 'style="border: 1px solid #ddd;border-left: 3px solid #f36d33;color: #666;page-break-inside: avoid;font-family: monospace;font-size: 15px;line-height: 1.6;margin-bottom: 1.6em;max-width: 100%;overflow: auto;padding: 1em 1.5em;display: block;word-wrap: break-word;"';
        $div_style = 'style="display: inline-block;width: 100%;border: 1px solid #000;text-align: left;font-size:1.5rem;"';
        $colors =new Colors();
        $is_array  = false;

        if (is_array($text)) {
            $var  = $text;
            $text = 'Array';
        }

        if (is_array($var)) {
            $var      = var_export($var, 1);
            $var      = $colors->getColoredHTML($var, 'green');
            $var      = "<pre {$pre_style}>".$var.'</pre>';
            $is_array = true;
        } else {
            $var = $colors->getColoredHTML($var, 'green');
        }

        $text      = $colors->getColoredHTML($text);

        echo "<div {$div_style}>".$text.' '.$var."</div><br>\n";
    }

    public function model($text, $var = '')
    {
        $pre_style             = 'style="border: 1px solid #ddd;border-left: 3px solid #f36d33;color: #666;page-break-inside: avoid;font-family: monospace;font-size: 15px;line-height: 1.6;margin-bottom: 1.6em;max-width: 100%;overflow: auto;padding: 1em 1.5em;display: block;word-wrap: break-word;"';

         

        $is_array              = false;

        if (is_array($text)) {
            $var  = $text;
            $text = 'Array';
        }

        if (is_array($var)) {
            $var      = var_export($var, 1);
            // $var=$colors->getColoredHTML($var, "green");
            $var      = "<pre {$pre_style}>".$var.'</pre>';
            $is_array = true;
        }

        // else {
        //    $var = $colors->getColoredHTML($var, "green");
        // }
        // $text=$colors->getColoredHTML($text);

        $random_id             = 'Model_'.substr(md5(rand()), 0, 7);
        $this->model_popup .= Render::html('popup_debug_model', ['MODEL_TITLE' => $text, 'MODEL_BODY' => $var, 'MODEL_ID' => $random_id]);

        $button_html           = Render::html('popup_debug_button', ['MODEL_TITLE' => $text, 'MODEL_ID' => $random_id]);
        $this->model_buttons[] = $button_html;
    }

    public function writeModelHtml()
    {
        if (defined('__DISPLAY_POPUP__')) {
            echo $this->model_popup;
            echo '      <div class="btn-group-vertical">';

            foreach ($this->model_buttons as $k => $html_button) {
                echo $html_button;
            }

            echo '</div>';
        }
    }
public static function RandomId($prefix='', $length = 10)
{
    return $prefix.substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

   public static function Random($length = 10) {
        self::$Random= self::RandomId('',$length);
    }
    public static function displayVideoLink($id, $text, $extra = '') {}
}
