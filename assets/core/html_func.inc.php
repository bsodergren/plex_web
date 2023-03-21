<?php

use Nette\Utils\Random;

function createLink($url, $text)
{
    return "<a href=\"" . $url . "\" > " . $text . "</a> ";
}

function keyword_list($key, $list)
{
    $link_array = [];
    $value = '';
    $list_array = explode(",", $list);

    foreach ($list_array as $k => $keyword) {
        $link_array[] = process_template(
            "filelist/search_link",
            [
                'KEY' => $key,
                'QUERY' => urlencode($keyword),
                'URL_TEXT' => $keyword,
                'CLASS' => ' class="badge fs-6 blueTable-thead" '
            ]
        );
    }

    $value = implode("  ", $link_array);
    return $value;
}

function keyword_cloud($list)
{
    $tag_links = '';

    if (is_array($list)) {
        foreach ($list as $key => $keyword) {
            $list_array[] = $keyword['val'];
        }
    } else {
        $list_array = explode(",", $list);
    }

    $search_url = "search.php?field=keyword&query=";

    foreach ($list_array as $key => $keyword) {

        $url = $search_url . urlencode($keyword);
        $tag_links .= process_template("cloud/tag", [
            'TAG_URL' => $url,
            'TAG_TEXT' => $keyword,
            'CLASS' => ''
        ]);
    }

    $html_links = process_template("cloud/main", ['TAG_CLOUD_HTML' => $tag_links]);
    return $html_links;
}





function process_template($template, $replacement_array = '')
{
    return template::echo($template, $replacement_array);
} //end process_template()





function output($var)
{
    if (is_array($var)) {
        print_r2($var);
        return 0;
    }

    echo $var . "<br>\n";
    // return 0;

} //end output()



function JavaRefresh($url, $timeout = 0)
{
    roboloader::javaRefresh($url, $timeout);
}//end JavaRefresh()

function add_hidden($name,$value,$attributes='')
{
	$html='';
	$html.='<input '.$attributes.' type="hidden" name="'.$name.'"  value="'.$value.'">';
	return $html. "\n";
}

function draw_checkbox($name,$value,$text='Face Trim')
{
    global $pub_keywords;
    
    $checked="";
	$current_value = $value;
    
    if ($current_value == 1 ) { $checked = "checked"; }
    
    $html = '<input type="hidden" name="'.$name.'" value="0">';
    $html .= '<input class="form-check-input" type="checkbox" name="'.$name.'" value=1 '.$checked.'>'.$text;

    return $html;
}
