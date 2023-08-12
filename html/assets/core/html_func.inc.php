<?php
/**
 * Command like Metatag writer for video files.
 */

function keyword_list($key, $list)
{
    $link_array = [];
    $value      = '';
    $list_array = explode(',', $list);

    foreach ($list_array as $k => $keyword) {
        $link_array[] = process_template(
            'filelist/search_link',
            [
                'KEY'      => $key,
                'QUERY'    => urlencode($keyword),
                'URL_TEXT' => $keyword,
                'CLASS'    => ' class="badge fs-6 blueTable-thead" ',
            ]
        );
    }

    $value      = implode('  ', $link_array);

    return $value;
}

function keyword_cloud($list, $field = 'keyword')
{
    $tag_links  = '';

    if (is_array($list)) {
        foreach ($list as $key => $keyword) {
            $list_array[] = $keyword['val'];
        }
    } else {
        $list_array = explode(',', $list);
    }

    $search_url = 'search.php?field='.$field.'&query=';

    foreach ($list_array as $k => $keyword) {
        $link_array[] = process_template(
            'filelist/search_link',
            [
                'KEY'      => $field,
                'QUERY'    => urlencode($keyword),
                'URL_TEXT' => $keyword,
                'CLASS'    => ' class="badge fs-6 blueTable-thead" ',
            ]
        );
    }

    $tag_links  = implode('  ', $link_array);
    //  return $value;

    $html_links = process_template('cloud/main', ['TAG_CLOUD_HTML' => $tag_links]);

    return $html_links;
}

function process_template($template, $replacement_array = '')
{
    return template::return($template, $replacement_array);
} // end process_template()

function JavaRefresh($url, $timeout = 0)
{
    roboloader::javaRefresh($url, $timeout);
}// end JavaRefresh()

function add_hidden($name, $value, $attributes = '')
{
    $html = '';
    $html .= '<input '.$attributes.' type="hidden" name="'.$name.'"  value="'.$value.'">';

    return $html."\n";
}

function draw_checkbox($name, $value, $text = '')
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
