<?php

namespace Plex\Template\HTML;

use Plex\Modules\Database\PlexSql;
use Plex\Template\Functions\Traits\TagCloud;
use Plex\Template\Render;

class Elements
{

    use TagCloud;
    
    public static $ElementsDir = 'elements/html';

    public static function template($template)
    {
        return Render::return($template, []);
    }

    public static function stylesheet($stylesheet)
    {
        $stylesheet = 'css/'.$stylesheet;
        $file = __LAYOUT_PATH__.'/'.$stylesheet;

        if (false == file_exists($file)) {
            return '';
        }

        return Render::return(self::$ElementsDir.'/link', ['CSS_URL' => __LAYOUT_URL__.$stylesheet]);
    }

    public static function javascript($javafile)
    {
        $javafile = 'js/'.$javafile;
        $file = __LAYOUT_PATH__.'/'.$javafile;

        if (false == file_exists($file)) {
            return '';
        }

        return Render::return(self::$ElementsDir.'/script', ['SCRIPT_URL' => __LAYOUT_URL__.$javafile]);
    }

    public static function addButton($text, $type = 'button', $class = 'btn button', $extra = '', $javascript = '')
    {
        return Render::return(self::$ElementsDir.'/button', [
            'TEXT' => $text,
            'TYPE' => $type,
            'CLASS' => $class,
            'EXTRA' => $extra,
            'JAVASCRIPT' => $javascript,
        ]);
    }

    public static function SelectOptions($array, $selected = '', $blank = null, $class = 'filter-option text-bg-primary')
    {
        $html = '';
        $default_option = '';
        $default = '';
        $checked = '';
        foreach ($array as $val) {
            $checked = '';

            if (\is_array($val)) {
                $text = $val['text'];
                $value = $val['value'];
            } else {
                $text = $val;
                $value = $val;
            }

            if ($text == $selected) {
                $checked = ' selected';
            }

            $html .= '<option class="'.$class.'" value="'.$value.'" '.$checked.'>'.$text.'</option>'."\n";
        }
        if (null !== $blank) {
            if ('' == $checked) {
                $default = ' selected';
            }
            $default_option = '<option class="'.$class.'" value=""  '.$default.'>'.$blank.'</option>'."\n";
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

        $checked = '';
        $current_value = $value;

        if (1 == $current_value) {
            $checked = 'checked';
        }

        $html = '<input type="hidden" name="'.$name.'" value="0">';
        $html .= '<input class="form-check-input" type="checkbox" name="'.$name.'" value=1 '.$checked.'>'.$text;

        return $html;
    }

    public static function javaRefresh($url, $timeout = 0)
    {
        global $_REQUEST;

        $html = '<script>'."\n";

        if ($timeout > 0) {
            $html .= 'setTimeout(function(){ ';
        }

        $html .= "window.location.href = '".$url."';";

        if ($timeout > 0) {
            $timeout *= 1000;
            $html .= '}, '.$timeout.');';
        }
        $html .= "\n".'</script>';
        logger('Looking for redirect', $html);

        echo $html;
    }

    public static function Comment($text)
    {
        return \PHP_EOL.'<!-- ---------- '.$text.' ----------- --->'.\PHP_EOL;
    }
}
