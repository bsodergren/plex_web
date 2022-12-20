<?php


function display_pagenationPages($url, $request_string='', $pageno='', $total_pages='')
{
    logger('Request url', $url);
    if ($request_string != '') {
        logger('Request String', $request_string);
        $request_string = ltrim($request_string, '?');
        logger('Request String', $request_string);
        $request_string = preg_replace('/(&pageno=[\d]+)/iu', '', $request_string);
        logger('Request String', $request_string);
        $request_string = preg_replace('/(direction=\w+)&.*/iU', '', $request_string);
        logger('Request String', $request_string);
    }

    $html = '<a href="'.$url.'?pageno=1&'.$request_string.'">First</a> | ';

    if (($total_pages - 6) >= 0) {
        if ($pageno <= ($total_pages - 6)) {
            $two   = ($pageno + 2);
            $three = ($pageno + 3);
            $four  = ($pageno + 4);
            $five  = ($pageno + 5);
            $six   = ($pageno + 6);
        } else {
            $o     = ($total_pages - $pageno);
            $two   = ($total_pages - (6 + $o));
            $three = ($total_pages - (5 + $o));
            $four  = ($total_pages - (4 + $o));
            $five  = ($total_pages - (3 + $o));
            $six   = ($total_pages - (2 + $o));
        }
    } else {
        $two   = ($pageno + 2);
        $three = ($pageno + 3);
        $four  = ($pageno + 4);
        $five  = ($pageno + 5);
        $six   = ($pageno + 6);
    }//end if

    $previous_pages = [
        $two,
        $three,
        $four,
        $five,
        $six,
    ];

    $html = '<a href="'.$url.'?pageno=1&'.$request_string.'">First</a> | ';

    if ($pageno >= $total_pages) {
        $next_page = $pageno;
        $url_next  = $url.'?'.$request_string;
    } else {
        $url_next = $url.'?pageno='.($pageno + 1).'&'.$request_string;
    }

    $html .= '<a href="'.$url_next.'">Next</a> | ';

    foreach ($previous_pages as $page_number) {
        if ($page_number <= $total_pages) {
            $next_page = $page_number;
            $url_next  = $url.'?pageno='.($next_page).'&'.$request_string;

            $html .= '<a href="'.$url_next.'">'.$next_page.'</a> | ';
        }
    }

    if ($pageno <= 1) {
        $url_prev = $url.'?'.$request_string;
    } else {
        $url_prev = $url.'?pageno='.($next_page - 1).'&'.$request_string;
    }

    $html .= '<a href="'.$url_prev.'">Prev</a> | ';

    $html .= '<a href="'.$url.'?pageno='.$total_pages.'&'.$request_string.'">Last</a>';
    echo $html;

}//end display_pagenationPages()


/**
 * @param  $url
 * @param  $request_string
 * @param  $pageno
 * @param  $total_pages
 * @return string
 */


function display_pagenation($url, $request_string='', $pageno='', $total_pages='')
{
    if ($request_string != '') {
        $request_string = ltrim($request_string, '?');
        $request_string = preg_replace('/(&pageno=[\d+])/iU', '', $request_string);
        $request_string = preg_replace('/(direction=\w+)&.*/iU', '', $request_string);
    }

    $html = '<a href="'.$url.'?pageno=1&'.$request_string.'">First</a> | ';

    if ($pageno <= 1) {
        $url_prev = $url.'?'.$request_string;
    } else {
        $url_prev = $url.'?pageno='.($pageno - 1).'&'.$request_string;
    }

    $html .= '<a href="'.$url_prev.'">Prev</a> | ';
    if ($pageno >= $total_pages) {
        $url_next = $url.'?'.$request_string;
    } else {
        $url_next = $url.'?pageno='.($pageno + 1).'&'.$request_string;
    }

    $html .= '<a href="'.$url_next.'">Next</a> | ';
    $html .= '<a href="'.$url.'?pageno='.$total_pages.'&'.$request_string.'">Last</a>';

    echo $html;

}//end display_pagenation()


/**
 * @param  $url_array
 * @return string
 */
function display_sort_options($url_array)
{
    $html        = '';
    $request_uri = '';
    $sep         = '?';
    global $_SERVER;
    $current = '';
    if (isset($url_array['rq_string'])) {
        parse_str($url_array['rq_string'], $output);
        $current = 'studio';
        if (isset($output['sort'])) {
            $current = $output['sort'];
        }

        $request_uri = preg_replace('/(sort=\w+)&.*/iU', '', $url_array['rq_string']);
        $sep         = '&';
    }

    foreach ($url_array['sort_types'] as $key => $value) {
        $bg = '';
        if ($current == $value) {
            $bg = ' background: #ff9900;';
        }

        $request_string = $request_uri.$sep.'sort='.$value;
        $html          .= ' <span style="font-size: 22px; '.$bg.'">';
        $html          .= display_directory_navlinks($url_array['url'], $key, $request_string);
        $html          .= '</span> |';
    }

    $html = rtrim($html, '|');
    return $html;

}//end display_sort_options()


function display_directory_navlinks($url, $text, $request_uri='')
{
    global $_SESSION;
    global $_REQUEST;

    $request_string = '';

    if ($request_uri != '') {
        $request_string = $request_uri;
    }

    // $link_url = $url . "?" . $request_key ."&genre=".$_REQUEST["genre"]."&". ;
    $html = "<a href='".$url.$request_string."'>".$text.'</a>';

    return $html;

}//end display_directory_navlinks()


function display_filelist($results, $option='', $page_array=[])
{
    global $db;
    $output = '';

    $output .= '<div class="container">'."\n";

        $total_files = $page_array['total_files'];

    foreach ($results as $id => $row) {
        $output .= '<table class="blueTable" > '."\n";
        $row_id  = $row['id'];
        // $row_filename = $row_id.":".$row['filename'];
        $row_filename  = $row['filename'];
        $row_fullpath  = $row['fullpath'];
        $row_video_key = $row['video_key'];
        $result_number = $row['result_number'];

        $button       = false;
        $extra_button = '';
        if ($option == 'hide') {
            $button = 'hide';
        }

        if ($option == 'filedelete') {
            $button = 'filedelete';
        }

        if ($button == true) {
            // $extra_button = '<input type="submit" name="submit" value="'.$button.'" id="'.$button.'_'.$row_id.'" onclick="doSubmitValue(this.id);">';
        }

        $array       = [
            'FILE_NAME'    => $row_filename,
            'DELETE_ID'    => 'delete_'.$row_id,
            'FILE_NAME_ID' => $row_id.'_filename',
            'FULL_PATH'    => $row_fullpath,
            'FILE_NO'      => $result_number,
            'TOTAL_FILES'  => $total_files,
            'HIDE_BUTTON'  => $extra_button,
        ];
        $output     .= process_template('metadata_row_header', $array);
        $value_array = [];
        $output     .= '<tbody> '."\n";

        foreach ($row as $key => $value) {
            $template_name = 'metadata_row';

            switch ($key) {
                case 'id':
                break;

                case 'filename':
                break;

                case 'fullpath':

                break;

                case 'video_key':

                break;

                case 'result_number':

                break;

                case 'library':

                break;

                case 'thumbnail':
                    if (__SHOW_THUMBNAILS__ == true) {
                        $output .= process_template('metadata_thumbnail', ['THUMBNAIL' => $value, 'FILE_ID' => $row_id]);
                    } else {
                        $output .= process_template('metadata_thumbnail', []);
                    }

                    // $output .= process_template("metadata_thumbnail",["THUMBNAIL"=>$value,"FILE_ID"=>$row_id]);
                    // $output .=  "<tr><td></td><td><img src='".$value."' onclick=\"popup('/plex_web/video.php?id=".$row_id."', 'video')\"></td><td></td></tr>";
                break;

                case 'duration':
                    $seconds         = round($value / 1000);
                    $duration_output = sprintf('%02d:%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60), ($seconds % 60));
                    // $output .=  "<tr><td></td><td>".$duration_output."</td><td></td></tr>";
                    $output .= process_template('metadata_button', ['DURATION' => $duration_output]);

                break;

                case 'favorite':
                    $yeschecked = ($value == '1') ? 'checked' : '';
                    $nochecked  = ($value == '0') ? 'checked' : '';

                    $array = [
                        'FILE_ID'    => $row_id,
                        'FIELD_KEY'  => $key,
                        'FIELD_NAME' => $row_id.'_'.$key,
                    // "PLACEHOLDER" =>  $placeholder,
                        'YESCHECKED' => $yeschecked,
                        'NOCHECKED'  => $nochecked,
                    ];

                    $output .= process_template('metadata_favorite_row', $array);

                    $sql                 = 'select  * from '.Db_TABLE_FILEINFO." WHERE video_key = '".$row_video_key."'";
                    $result              = $db->query($sql);
                    $file_info_array     = ['FULL_PATH' => $row_fullpath];
                    $additional_fileinfo = [];
                    if (isset($result[0])) {
                        $file_info = $result[0];

                        $additional_fileinfo = [
                            'HEIGHT'   => $file_info['height'],
                            'WIDTH'    => $file_info['width'],
                            'BITRATE'  => display_size($file_info['bit_rate']),
                            'FILESIZE' => byte_convert($file_info['filesize']),

                        ];
                    }

                    $fileinfo_array = array_merge($file_info_array, $additional_fileinfo);

                    $output .= process_template('metadata_row_fileinfo', $fileinfo_array);

                break;

                case 'substudio':
                    $template_name = 'metadata_row_studio';
                case 'studio':
                    $template_name = 'metadata_row_studio';

                default:
                    $placeholder = 'placeholder="'.$value.'"';
                    if ($value == '') {
                        $placeholder = '';
                        switch ($key) {
                            case 'artist':
                                $value_array = missingArtist($key, $row);

                            break;

                            case 'title':
                                $value_array = missingTitle($key, $row);

                            break;

                            case 'genre':
                                $value_array = missingGenre($key, $row);
                            break;

                            case 'studio':
                                $value_array = missingStudio($key, $row);
                            break;

                            case 'substudio':
                                $value_array = missingStudio($key, $row);
                            break;
                        }//end switch
                    }//end if

                    if (isset($value_array[$key][0]) && $value_array[$key][0] != '') {
                        $value = ' value="'.$value_array[$key][0].'"';
                        if (isset($value_array['style'][0]) && $value_array['style'][0] != '') {
                            $value = $value.' style="'.$value_array['style'][0].'"';
                        }
                    } else {
                        if ($key == 'studio' || $key == 'substudio') {
                            $value = $value;
                        } else {
                            $value = '';
                        }
                    }

                    $array = [

                        'FIELD_KEY'   => $key,
                        'FIELD_NAME'  => $row_id.'_'.$key,
                        'PLACEHOLDER' => $placeholder,
                        'VALUE'       => $value,
                    ];

                    $output .= process_template($template_name, $array);
                    unset($value_array);
                    unset($value);
            }//end switch
        }//end foreach

        $output .= '</tbody></table><p> '."\n";
    }//end foreach

    $output .= '</div> '."\n";

    echo $output;

}//end display_filelist()


function display_navbar_left_links($url, $text, $js='')
{
    global $_SESSION;
    $style = '';

    if ($text == $_SESSION['library']) {
        $style = ' style="background:#778899"';
    }

    $array = [
        'MENULINK_URL'  => $url,
        'MENULINK_JS'   => $style,
        'MENULINK_TEXT' => $text,
    ];
    return process_template('menu_link', $array);

}//end display_navbar_left_links()


function display_navbar_links()
{
    global $navigation_link_array;
    global $_SERVER;
    $html          = '';
    $dropdown_html = '';

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
            $array    = [
                'MENULINK_URL'  => $link_array['url'],
                'MENULINK_JS'   => $link_array['js'],
                'MENULINK_TEXT' => $link_array['text'],
            ];
            $url_text = process_template('menu_link', $array);

            if ($link_array['secure'] == true && $_SERVER['REMOTE_USER'] != 'bjorn') {
                $html = $html.$url_text."\n";
            } else {
                $html = $html.$url_text."\n";
            }
        }//end if
    }//end foreach

    return $html.$dropdown_html;

}//end display_navbar_links()


function display_log($string)
{
    echo '<pre>'.$string."</pre>\n";

}//end display_log()
