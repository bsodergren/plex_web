<?php



class Render
{

    public $_SERVER;
    public $_SESSION;
    public $_REQUEST;
    public $navigation_link_array;


public function __construct($navigation_link_array)
{

    global $_SESSION;
    global $_REQUEST;
    global $_SERVER;

    $this->_SESSION = $_SESSION;
    $this->navigation_link_array = $navigation_link_array;
    $this->_REQUEST = $_REQUEST;
    $this->_SERVER = $_SERVER;

}
public static function display_sort_options($url_array)
{
    $html        = '';
    $request_uri = '';
    $sep         = '?';
    $current = '';





    if ($url_array['query_string'] != '') {
        parse_str($url_array['query_string'], $query_parts);

        $current = 'studio';

        if (isset($url_array['direction'])) {
            $query_parts['direction'] = $url_array['direction'];
        }

        if (isset($query_parts['sort'])) {
            $current = $query_parts['sort'];
            unset($query_parts['sort']);
        }


        $request_uri = '?' . http_build_query($query_parts);
        $sep         = '&';
    }


    foreach ($url_array['sort_types'] as $key => $value) {
        $bg = '';


        if ($current == $value) {
            $bg = ' active';
        }
        $class = "btn btn-primary btn-m" . $bg;
        $request_string = $request_uri . $sep . 'sort=' . $value;

        $html          .= self::display_directory_navlinks($url_array['url'], $key, $request_string, $class, 'role="button" aria-pressed="true"') . "\n";
    }

    return $html;
} //end display_sort_options()


public static function display_directory_navlinks($url, $text, $request_uri = '', $class = '', $additional = '')
{
    global $_SESSION;
    global $_REQUEST;

    $request_string = '';

    if ($request_uri != '') {
        $request_string = $request_uri;
    }
    if ($class != '') {
        $class = " class=\"" . $class . "\"";
    }

    // $link_url = $url . "?" . $request_key ."&genre=".$_REQUEST["genre"]."&". ;
    $html = "<a href='" . $url . $request_string . "' " . $class . " " . $additional . ">" . $text . '</a>';

    return $html;
} //end display_directory_navlinks()

public static function display_navbar_left_links($url, $text, $js = '')
{
    $style = '';
    global $_SESSION;

    if ($text == $_SESSION['library']) {
        $style = ' style="background:#778899"';
    }

    $array = [
        'MENULINK_URL'  => $url,
        'MENULINK_JS'   => $style,
        'MENULINK_TEXT' =>  $text,
    ];
    return process_template('navbar/library_links', $array);
} //end display_navbar_left_links()


public static function display_navbar_links()
{

    $html          = '';
    $dropdown_html = '';
    global $navigation_link_array;
    global $_REQUEST;

    foreach ($navigation_link_array as $name => $link_array) {
        if ($name == 'dropdown') {
            $dropdown_html = '';

            foreach ($link_array as $dropdown_name => $dropdown_array) {
                $dropdown_link_html = '';

                foreach ($dropdown_array as $d_name => $d_values) {
                    $array               = [
                        'DROPDOWN_URL_TEXT' => $d_name,
                        'DROPDOWN_URL'      => $d_values,
                    ];
                    $dropdown_link_html .= process_template('menu_dropdown_link', $array);
                }

                $array = [
                    'DROPDOWN_TEXT'  => $dropdown_name,
                    'DROPDOWN_LINKS' => $dropdown_link_html,
                ];

                $dropdown_html .= process_template('menu_dropdown', $array);
            }
        } else {

            if ($link_array['studio'] == true)
            {
                if($_REQUEST['studio']){
                    $link_array['url'] = $link_array['url'] . "?studio=".$_REQUEST['studio'];
                }
                if($_REQUEST['substudio']){
                    $link_array['url'] = $link_array['url'] . "?substudio=".$_REQUEST['substudio'];
                }
                
            }
            
            $array    = [
                'MENULINK_URL'  => $link_array['url'],
                'MENULINK_JS'   => $link_array['js'],
                'MENULINK_TEXT' => $link_array['text'],
            ];



            $url_text = process_template('menu_link', $array);

            if ($link_array['secure'] == true && $_SERVER['REMOTE_USER'] != 'bjorn') {
                $html = $html . $url_text . "\n";
            } else {
                $html = $html . $url_text . "\n";
            }



        } //end if
    } //end foreach

    return $html . $dropdown_html;
} //end display_navbar_links()

public static function display_breadcrumbs()
{


    $crumbs_html = '';
    foreach (BREADCRUMB as $text => $url) {
        if ($text == '') {
            continue;
        }

        $class = 'breadcrumb-item';
        $link = '<a href="' . $url . '">' . $text . '</a>';

        if ($url == '') {
            $class .= ' active" aria-current="page';
            $link = $text;
        }

        $params['CLASS'] = $class;
        $params['LINK'] = $link;
        $crumbs_html .= process_template('navbar/crumb', $params);
    }


    return process_template('navbar/breadcrumb', ['CRUMB_LINKS' => $crumbs_html]);
}



public static function display_SelectOptions($array, $selected = '')
{
    $html = '';
    foreach ($array as $val) {
        $checked = '';
        if ($val == $selected) {
            $checked = ' selected';
        }
        $html .= '<option value="' . $val . '" ' . $checked . '>' . $val . '</option>' . "\n";
    }

    return $html;
}


}