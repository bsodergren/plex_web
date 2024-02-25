<?php

namespace Plex\Template\HTML;

use Plex\Template\Render;
use Plex\Template\HTML\Elements;

class Elements
{

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

        return Render::return('elements/html/link', ['CSS_URL' => __LAYOUT_URL__.$stylesheet]);
    }

    public static function javascript($javafile)
    {
        $javafile = 'js/'.$javafile;
        $file = __LAYOUT_PATH__.'/'.$javafile;

        if (false == file_exists($file)) {
            return '';
        }

        return Render::return('elements/html/script', ['SCRIPT_URL' => __LAYOUT_URL__.$javafile]);
    }

    public static function addButton($text, $type = 'button', $class = 'btn button', $extra = '', $javascript = '')
    {
        return Render::return('elements/html/button', [
            'TEXT' => $text,
            'TYPE' => $type,
            'CLASS' => $class,
            'EXTRA' => $extra,
            'JAVASCRIPT' => $javascript,
        ]);
    }

    public static function SelectOptions($array, $selected = '', $blank = null,$class="filter-option text-bg-primary")
    {
        $html = '';
        $default_option = '';
        $default = '';
        $checked = '';
        foreach ($array as $val) {
            $checked = '';

            if(is_array($val))
            {
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

    public static function keyword_cloud($field = 'keyword')
    {
        global $db;
        global $_SESSION;

        $where = PlexSql::getLibrary();
        $where = str_replace('AND', 'WHERE', $where);
        $where = str_replace('m.library', 'library', $where);

        $sql = 'SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX('.$field.", ',', n.digit+1), ',', -1) val FROM ".Db_TABLE_VIDEO_TAGS.' INNER JOIN (SELECT 0 digit UNION ALL SELECT
 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6) n
 ON LENGTH(REPLACE('.$field.", ',' , '')) <= LENGTH(".$field.')-n.digit '.$where.' ORDER BY `val` ASC';
        $list = $db->query($sql);
        $tag_links = '';
        if (0 == \count($list)) {
            return false;
        }

        if (\is_array($list)) {
            foreach ($list as $key => $keyword) {
                $list_array[] = $keyword['val'];
            }
        } else {
            $list_array = explode(',', $list);
        }

        foreach ($list_array as $k => $keyword) {
            $letter = substr($keyword, 0, 1);
            if (!isset($last_letter)) {
                $last_letter = $letter;
            }
            if ($letter != $last_letter) {
                $last_letter = $letter;
                // $link_array[] = '</div>    <div class="'.__TAG_CAT_CLASS__.' ">';
                // $index=0;
            }
            $keyword_array[$last_letter][] = $keyword;
            // if ($max <= $index) {
            //     $link_array[] = '</div>    <div class="">';
            //     $index=0;
            // }
            // $index++;
            // $link_array[] = Render::html(
            //     'cloud/tag',
            //     [
            //         'KEY'      => $field,
            //         'QUERY'    => urlencode($keyword),
            //         'URL_TEXT' => $keyword,
            //         // 'CLASS'    => ' badge fs-6 blueTable-thead ',
            //     ]
            // );
        }
        $max = 10;
        $keyword_box_class = '<div class="">';
        foreach ($keyword_array as $letter => $keywordArray) {
            $index = 0;
            $total = \count($keywordArray);
            if ($total >= $max) {
                $link_array[] = $keyword_box_class;
            }
            foreach ($keywordArray as $k => $keyword) {
                if ($max <= $index) {
                    $link_array[] = '</div>'.$keyword_box_class;
                    $index = 0;
                }
                ++$index;
                $link_array[] = Render::html(
                    'cloud/tag',
                    [
                        'KEY' => $field,
                        'QUERY' => urlencode($keyword),
                        'URL_TEXT' => $keyword,
                        // 'CLASS'    => ' badge fs-6 blueTable-thead ',
                    ]
                );
            }
            if ($total >= $max) {
                $link_array[] = '</div>';
            }

            $link_array[] = '</div>    <div class="'.__TAG_CAT_CLASS__.' ">';
        }

        $tag_links = implode('  ', $link_array);
        //  return $value;

        return $tag_links;
    }

    public static function Comment($text)
    {
        return \PHP_EOL.'<!-- ---------- '.$text.' ----------- --->'.\PHP_EOL;
    }
}
