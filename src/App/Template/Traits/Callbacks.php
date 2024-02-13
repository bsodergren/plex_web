<?php
/**
 * plex web viewer
 */

namespace Plex\Template\Traits;

use Nette\Utils\FileSystem;

use Plex\Template\HTML\Elements;
use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
use KubAT\PhpSimple\HtmlDomParser;
use Plex\Template\Functions\Functions;

trait Callbacks
{

    public const FUNCTION_CALLBACK = '|{{function=([a-zA-Z_]+)\|?(.*)?}}|i';
    public const STYLESHEET_CALLBACK = '|{{(stylesheet)=([a-zA-Z-_/\.]+)\|?(.*)?}}|i';
    public const JAVASCRIPT_CALLBACK = '|{{(javascript)=([a-zA-Z-_/\.]+)\|?(.*)?}}|i';
    public const VARIABLE_CALLBACK = '|{\$([a-zA-Z_-]+)}|';
    public const JS_VAR_CALLBACK = '|!!([a-zA-Z_-]+)!!|';
    
    

    public function callback_parse_variable($matches)
    {
        $key = $matches[1];

        if(defined($key)){
            return constant($key);
        }

        if(array_key_exists($key,$this->replacement_array)){
            $value = $this->replacement_array[$key];
          //  unset($this->replacement_array[$key]);
            return $value;
        }

        return '';
    }
    
    public function callback_parse_include($matches)
    {
        $method = $matches[1];
           return Elements::$method($matches[2]);
    }
    public function parse_urllink($text)
    {
        $dom   = HtmlDomParser::str_get_html($text);

        if (false === $dom) {
            return $text;
        }

        $elems = $dom->find('a');

        if (0 == \count($elems)) {
            return $text;
        }

        foreach ($elems as $a) {
            $a->setAttribute('data-bs-placement', 'top');
            $a->setAttribute('data-bs-toggle', 'tooltip');
            $a->setAttribute('title', $a->href);
        }

        return $dom;
    }

    public function callback_parse_function($matches)
    {
        $helper = new Functions();
        $method = $matches[1];

        // $value = Helper::$method();
        // if(method_exists($helper,$method)){
        return $helper->{$method}($matches);
        // }
    }

    private function callback_badge($matches)
    {
        $text  = $matches[3];
        $font  = '';
        $class = $matches[2];
        if (str_contains($matches[2], ',')) {
            $arr   = explode(',', $matches[2]);
            $class = $arr[0];
            $font  = 'fs-'.$arr[1];
        }

        $style = 'class="badge text-bg-'.$class.' '.$font.'"';

        return '<span '.$style.'>'.$text.'</span>';
    }

    private function callback_color($matches)
    {
        $text  = $matches[3];
        $style = 'style="';
        if (str_contains($matches[2], ',')) {
            $colors = explode(',', $matches[2]);
            $style .= 'color: '.$colors[0].'; background:'.$colors[1].';';
        } else {
            $style .= 'color: '.$matches[2].';';
        }
        $style .= '"';

        return '<span '.$style.'>'.$text.'</span>';
    }
}
