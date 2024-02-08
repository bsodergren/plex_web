<?php

namespace Plex\Template\HTML;

use Plex\Template\Template;

class Elements
{
    public static function stylesheet($stylesheet)
    {
        $stylesheet = 'css/'.$stylesheet;
        $file = __LAYOUT_PATH__.'/'.$stylesheet;

        if (false == file_exists($file)) {
            return '';
        }

        return Template::getHtml('elements/html/link', ['CSS_URL' => __LAYOUT_URL__.$stylesheet]);
    }

    public static function javascript($javafile)
    {
        $javafile = 'js/'.$javafile;
        $file = __LAYOUT_PATH__.'/'.$javafile;

        if (false == file_exists($file)) {
            return '';
        }

        return Template::getHtml('elements/html/script', ['SCRIPT_URL' => __LAYOUT_URL__.$javafile]);
    }

    public static function addButton($text, $type = 'button', $class = 'btn button', $extra = '', $javascript = '')
    {
        return Template::getHtml('elements/html/button', [
            'TEXT' => $text,
            'TYPE' => $type,
            'CLASS' => $class,
            'EXTRA' => $extra,
            'JAVASCRIPT' => $javascript,
        ]);
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
}
