<?php
/**
 * plex web viewer
 */

namespace Plex\Template;

class HTMLDisplay extends Template
{
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

        echo $html;
    }

    public static function keyword_list($key, $list)
    {
        $link_array = [];
        $value      = '';
        $list_array = explode(',', $list);

        foreach ($list_array as $k => $keyword) {
            $link_array[] = Template::GetHTML(
                'filelist/search_link',
                [
                    'KEY'      => $key,
                    'QUERY'    => urlencode($keyword),
                    'URL_TEXT' => $keyword,
                    //  'CLASS'    => ' class="badge fs-6 blueTable-thead" ',
                ]
            );
        }

        return implode('  ', $link_array);
    }

    public static function keyword_cloud($field = 'keyword')
    {
        global $db;
        global $_SESSION;
        $sql               = 'SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX('.$field.", ',', n.digit+1), ',', -1) val FROM ".Db_TABLE_VIDEO_TAGS.' INNER JOIN (SELECT 0 digit UNION ALL SELECT
     1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6) n
     ON LENGTH(REPLACE('.$field.", ',' , '')) <= LENGTH(".$field.")-n.digit WHERE library = '".$_SESSION['library']."' ORDER BY `val` ASC";

        $list              = $db->query($sql);
        $tag_links         = '';
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
            $letter                        = substr($keyword, 0, 1);
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
            // $link_array[] = Template::GetHTML(
            //     'cloud/tag',
            //     [
            //         'KEY'      => $field,
            //         'QUERY'    => urlencode($keyword),
            //         'URL_TEXT' => $keyword,
            //         // 'CLASS'    => ' badge fs-6 blueTable-thead ',
            //     ]
            // );
        }
        $max               = 10;
        $keyword_box_class = '<div class="">';
        foreach ($keyword_array as $letter => $keywordArray) {
            $index        = 0;
            $total        = \count($keywordArray);
            if ($total >= $max) {
                $link_array[] = $keyword_box_class;
            }
            foreach ($keywordArray as $k => $keyword) {
                if ($max <= $index) {
                    $link_array[] = '</div>'.$keyword_box_class;
                    $index        = 0;
                }
                ++$index;
                $link_array[] = Template::GetHTML(
                    'cloud/tag',
                    [
                        'KEY'      => $field,
                        'QUERY'    => urlencode($keyword),
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

        $tag_links         = implode('  ', $link_array);
        //  return $value;

        return $tag_links;
    }
    private $model_popup   = '';
    private $model_buttons = [];

    public static function echo2($text, $var = '')
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
        global $colors;
        $is_array  = false;

        if (\is_array($text)) {
            $var  = $text;
            $text = 'Array';
        }

        if (\is_array($var)) {
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

        global $colors;

        $is_array              = false;

        if (\is_array($text)) {
            $var  = $text;
            $text = 'Array';
        }

        if (\is_array($var)) {
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
        $this->model_popup .= Template::GetHTML('popup_debug_model', ['MODEL_TITLE' => $text, 'MODEL_BODY' => $var, 'MODEL_ID' => $random_id]);

        $button_html           = Template::GetHTML('popup_debug_button', ['MODEL_TITLE' => $text, 'MODEL_ID' => $random_id]);
        $this->model_buttons[] = $button_html;
    }

    public function writeModelHtml()
    {
        if (\defined('__DISPLAY_POPUP__')) {
            echo $this->model_popup;
            echo '      <div class="btn-group-vertical">';

            foreach ($this->model_buttons as $k => $html_button) {
                echo $html_button;
            }

            echo '</div>';
        }
    }
}
