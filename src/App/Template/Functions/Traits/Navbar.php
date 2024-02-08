<?php
namespace Plex\Template\Functions\Traits;

use Plex\Template\Render;
use Plex\Template\Display\Display;

trait Navbar
{

    
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

 


    public  function createBreadcrumbs()
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

        if (isset(Display::$CrubURL['list'])) {
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

        if (isset(Display::$CrubURL['grid'])) {
            $crumbs['Grid'] = Display::$CrubURL['grid'].$re_string.$sep.http_build_query($parts);
            unset($crumbs['All']);
        }

        // $crumbs['List'] = "";
        if (isset(Display::$CrubURL['list'])) {
            $crumbs['List'] = Display::$CrubURL['list'].$re_string.$sep.http_build_query($parts);
            unset($crumbs['All']);
        }

        // $crumbs['All'] = "";
        //        if (isset( self::$CrubURL['all'] )) {
        //      }
        // dd($crumbs);
        return $crumbs;
    }

    public  function breadcrumbs()
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
}