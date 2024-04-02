<?php

namespace Plex\Template\Functions\Traits;

use Plex\Core\Request;
use Plex\Modules\Display\Display;
use Plex\Template\Render;

trait Breadcrumbs
{
    
    public function createBreadcrumbs()
    {
        $tag_types = Request::$tag_types;
        $request_string = [];
        $parts = [];

        $request_tag = [];
        $crumbs['Home'] = 'home.php';

        $url = 'list.php';
        if (__THIS_FILE__ == 'search.php') {
            $url = 'search.php';
        
        }

        // if (isset(self::$CrubURL['grid'])) {
        //     $url = 'files.php';
        // }

        if (isset(Display::$CrubURL['list'])) {
            $url = 'gridview.php';
        }

        $crumbs[$in_directory] = '';
        parse_str($_SERVER['QUERY_STRING'], $query_parts);
        if (\count($query_parts) > 0) {
            if (__THIS_FILE__ == 'search.php') {

            if (!array_key_exists('view', $query_parts) )
            {
               $query_parts['view'] = 'List';
           }}

            foreach ($query_parts as $key => $value) {
                if (\in_array($key, $tag_types)) {
                    if ('' != $value) {
                        $request_tag[$key] = $value;
                    }
                } else {
                    if ('alpha' == $key) {
                        continue;
                    }
                    if (__THIS_FILE__ == 'search.php') {

                        $request_tag = [];
                        if ('view' == $key) {
                            if($value == 'List'){
                                $value = 'Grid';
                            } else {
                                $value = 'List';
                            }

                            $request_string['view'] = $value;
                            continue;
                         
                              
                        
                        }
                    }
                    $request_string[$key] = $value;
                }
            }


            if (\array_key_exists('genre', $request_tag)) {
                $url = 'genre.php';
            }
            if (\array_key_exists('studio', $request_tag)) {
                // $url = 'studio.php';
                $studio_key = $request_tag['studio'];
                //  unset($request_tag['studio']);
            }

            $sep = '?';
            if (\count($request_string) > 0) {
                $re_string = $sep.http_build_query($request_string);
                $sep = '&';
            }

            $crumb_url = $url.$re_string;

            if (\count($request_tag) > 0) {
                $crumbs[$_SESSION['library']] = $crumb_url.$sep.http_build_query(['studio' => $studio_key]);
                foreach ($request_tag as $key => $value) {
                    $parts[$key] = $value;
                    $crumbs[$value] = $crumb_url.$sep.http_build_query($parts);
                    $last = $value;
                }

                if ('genre' == $key) {
                    $crumbs[$last] = '';
                } else {
                    $crumbs[$last] = 'genre.php?'.http_build_query($parts);
                }
            }
        }

        $req = '';
        if (__THIS_FILE__ == 'genre.php' || __THIS_FILE__ == 'studio.php') {
            $req = '&'.http_build_query($parts);
        }

        if (__THIS_FILE__ == 'search.php') {
            $crumbs['List'] = $crumb_url;
        }

        $crumbs['All'] = $url.'?allfiles=1'.$req;

        if (isset(Display::$CrubURL['grid'])) {
            $crumbs['Grid'] = Display::$CrubURL['grid'].$re_string.$sep.http_build_query($parts);
            unset($crumbs['All']);
        }

        // $crumbs['List'] = "";
        if (isset(Display::$CrubURL['list'])) {
            $crumbs['List'] = Display::$CrubURL['list'].$re_string.$sep.http_build_query($parts);
            unset($crumbs['All']);
        }

        return $crumbs;
    }

    public function breadcrumbs()
    {
        $crumbs_html = '';
        foreach (BREADCRUMB as $text => $url) {
            if ('' == $text) {
                continue;
            }

            $class = 'breadcrumb-item';
            $link = '<a href="'.$url.'">'.$text.'</a>';

            if ('' == $url) {
                $class .= ' active" aria-current="page';
                $link = $text;
            }

            $params['CLASS'] = $class;
            $params['LINK'] = $link;
            $crumbs_html .= Render::html(self::$BreadcrumbsDir.'/crumb', $params);
        }

        return Render::html(self::$BreadcrumbsDir.'/breadcrumb', ['CRUMB_LINKS' => $crumbs_html]);
    }
}
