<?php

namespace Plex\Template\Traits;

use KubAT\PhpSimple\HtmlDomParser;
use Plex\Template\Functions\Functions;
use Plex\Template\HTML\Elements;

trait Callbacks
{
    public const FUNCTION_CALLBACK = '|{{function=([a-zA-Z_]+)\|?(.*)?}}|i';
    public const STYLESHEET_CALLBACK = '|{{(stylesheet)=([a-zA-Z-_/\.]+)\|?([a-zA-Z=$,.\?\{\}]+)?}}|i';
    public const JAVASCRIPT_CALLBACK = '|{{(javascript)=([a-zA-Z-_/\.]+)\|?([a-zA-Z=$,.\?\{\}]+)?}}|i';
    public const TEMPLATE_CALLBACK = '|{{(template)=([a-zA-Z-_/\.]+)\|?(.*)?}}|i';
    public const VARIABLE_CALLBACK = '|{\$([a-zA-Z_-]+)}|';
    public const LANG_CALLBACK = '|{L ([a-zA-Z_]+)}|';
    public const JS_VAR_CALLBACK = '|!!([a-zA-Z_-]+)!!|';
    public const IF_CALLBACK = '|{if="([^"]+)"}(.*?){\/if}|misu';
    public const CSS_VAR_CALLBACK = '|\$([a-zA-Z_-]+)\$|';

    public const EXPLODE_CALLBACK = '|{replace="?([^"]+)"?}|mis';

    public function callback_explode_callback($matches)
    {
        $data = str_getcsv($matches[1], ',', "'");

        return str_replace($data[1], $data[2], $data[0]).$data[2];
    }

    public function callback_if_statement($matches)
    {
        $compare = $matches[1];
        $array = explode('=', $compare);
        $return = '';
        if ($array[0] == $array[1]) {
            $return = $matches[2];
        }

        return $return;
    }

    private function parse_variable($matches)
    {
        $key = $matches[1];

        if (\defined($key)) {
            return \constant($key);
        }
        if (\is_array($this->replacement_array)) {
            if (\array_key_exists($key, $this->replacement_array)) {
                return $this->replacement_array[$key];
                //  unset($this->replacement_array[$key]);
            }
        }

        return $key;
    }

    public function callback_text_variable($matches)
    {
        $key = $matches[1];
        $text = $this->parse_variable($matches);
        if($text == $key){
            dump([$this->template_file,$text]);
            return  $text;
        }
        return $text;

      
        
    }


    public function callback_parse_variable($matches)
    {
        $key = $matches[1];
        $text = $this->parse_variable($matches);
        if($text == $key){
            return '';
        }

        return $text;
    }

    public function callback_parse_include($matches)
    {
        $method = $matches[1];

        if (str_contains($matches[3], 'render')) {
            // $parts = explode(",",$matches[3]);
            // $vars = explode("=",$parts[1]);
        }

        return Elements::$method($matches[2]);
    }

    public function parse_urllink($text)
    {
        $dom = HtmlDomParser::str_get_html($text);

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
        $text = $matches[3];
        $font = '';
        $class = $matches[2];
        if (str_contains($matches[2], ',')) {
            $arr = explode(',', $matches[2]);
            $class = $arr[0];
            $font = 'fs-'.$arr[1];
        }

        $style = 'class="badge text-bg-'.$class.' '.$font.'"';

        return '<span '.$style.'>'.$text.'</span>';
    }

    private function callback_color($matches)
    {
        $text = $matches[3];
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
