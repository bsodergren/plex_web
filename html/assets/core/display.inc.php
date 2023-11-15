<?php
/**
 * plex web viewer
 */

/**
 * Command like Metatag writer for video files.
 */
function display_fileRow($params, $field, $value, $class, $template_base = 'filelist')
{
    $params['FIELD_ROW_HTML'] .= process_template(
        $template_base.'/file_row',
        [
            'FIELD'     => $field,
            'VALUE'     => $value,
            'ALT_CLASS' => $class,
        ]
    );

    return $params;
}

function display_fileInfo($fileInfoArray, $total_files,$template_base = 'filelist')
{
    global $db;
    $table_body_html              = [];
    $x                            = 0;
    $row_id                       = $fileInfoArray['id'];
    // $row_filename = $row_id.":".$row['filename'];
    $row_filename                 = $fileInfoArray['filename'];
    $row_fullpath                 = $fileInfoArray['fullpath'];
    $row_video_key                = $fileInfoArray['video_key'];

    if (isset($fileInfoArray['result_number'])) {
        $result_number = $fileInfoArray['result_number'];
    }

  

    $params['FILE_NAME']          = $row_filename;
    if ($fileInfoArray['title']) {
        $params['FILE_NAME'] = $fileInfoArray['title'];
    }
    $params['ROW_ID']             = '';
    if (!defined('NONAVBAR')) {

        $params['FILE_NAME']     =  process_template($template_base.'/popup_js',  ['APP_HOME'=>APP_HOME,'ROW_ID' => $row_id,'FILE_NAME'=>$params['FILE_NAME']]);

        $params['VERTICAL_TEXT'] = process_template($template_base.'/file_vertical',  ['ROW_ID' => '&nbsp;&nbsp;&nbsp;'.$result_number.' of '.$total_files]);
        $params['SEARCH_BUTTON'] = process_template($template_base.'/file_search', []);
    }

    $params['DELETE_ID']          = 'delete_'.$row_id;
    $params['FILE_NAME_ID']       = $row_id.'_filename';
    $params['FULL_PATH']          = $row_fullpath;
    $params['FILE_ID']            = $row_id;
    // krsort($fileInfoArray);
    // dd($fileInfoArray);
    foreach ($fileInfoArray as $key => $value) {
        $class       = (0 == $x % 2) ? 'blueTable-tr-even' : '';
        $value_array = [];

        switch ($key) {
            // case 'favorite':
            case 'title':
            case 'fullpath':
                $value  = str_replace(__PLEX_LIBRARY__.'/', '', $value);
                $params = display_fileRow($params, ucfirst($key), $value, $class, $template_base);
                ++$x;

                break;

            case 'added':
                $params = display_fileRow($params, ucfirst($key), $value, $class, $template_base);
                ++$x;

                break;

            case 'thumbnail':
                if (__SHOW_THUMBNAILS__ == true) {
                    $params['THUMBNAIL_HTML'] .= process_template(
                        $template_base.'/file_thumbnail',
                        [
                            'THUMBNAIL' => __URL_HOME__.$value,
                            'FILE_ID'   => $row_id,
                        ]
                    );
                }

                break;

            case 'duration':
                $params = display_fileRow($params, 'Duration', videoDuration($value), $class, $template_base);
                ++$x;

                break;

            case 'studio':
            case 'substudio':
                if ('' != $value) {
                    if (!defined('NONAVBAR')) {
                        $value = keyword_list($key, $value);

                        
                                                // $value = process_template(
                                                //     "filelist/search_link",
                                                //     [
                                                //         'KEY' => $key,
                                                //         'QUERY' => urlencode($value),
                                                //         'URL_TEXT' => $value
                                                //     ]
                                                // );
                                                
                    }

                    $params = display_fileRow($params, ucfirst($key), $value, $class, $template_base);
                    ++$x;
                }

                break;
         

            case 'artist':
            case 'genre':
            case 'keyword':
                if ('' != $value) {
                    if (!defined('NONAVBAR')) {
                        $value = keyword_list($key, $value);
                    }
                    $params = display_fileRow($params, ucfirst($key), $value, $class, $template_base);
                    ++$x;
                }

                break;
                case 'filesize':
                    $params = display_fileRow($params, ucfirst($key), display_size($value), $class, $template_base);
                    ++$x;
                    break;
            case 'video_info':
                // if (defined('NONAVBAR')) {
                foreach ($value as $infokey => $fileInfoValue) {
                    //    $class = ($x % 2 == 0) ? 'blueTable-tr-even' : '';
                    //    $value_array = [];
                    switch ($infokey) {
                  

                        case 'bit_rate':
                            $infoParams[strtoupper($infokey)] =  byte_convert($fileInfoValue);

                            break;

                        case 'width':
                        case 'height':
                        case 'format':
                            $infoParams[strtoupper($infokey)] = $fileInfoValue;

                            break;
                    }
                }
                // $infoParams['ALT_CLASS'] =  $class;

                $params = display_fileRow($params, '', process_template(
                    $template_base.'/file_videoinfo',
                    $infoParams
                ), $class, $template_base);

                ++$x;

                break;
        } // end switch
    } // end foreach
    $table_body_html['filecards'] = process_template($template_base.'/file', $params);
    if (!defined('NONAVBAR')) {
        $tag_list           = '';
        $sql                = 'SELECT tag_name FROM tags WHERE file_id = '.$row_id;
        $res                = $db->query($sql);
        if (count($res) > 0) {
            foreach ($res as $_ => $row) {
                $tag_array[$_] = $row['tag_name'];
            }

            $tag_list = implode(',', $tag_array);
            $tag_list = str_replace(',', "','", $tag_list);
            $tag_list = "['".$tag_list."']";

            $tag_list = ' tagInput'.$row_id.'.addData('.$tag_list.');';
        }

        $params['TAG_DATA'] = $tag_list;
    }
    //    $table_body_html['js'] = process_template("filelist/tag_js", $params);

    return $table_body_html;
}

function display_videoInfo($row, $template_base = 'filelist')
{
    global $db;
    global $hidden_fields;
    $table_html      = '';
    $redirect_string = '';
    $total_files     = '';
    $js_html         = '';

    $row_id          = $row['id'];

    $table_body      = display_fileInfo($row,  $total_files,$template_base);
    $js_html .= $table_body['js'];
    $table_html .= process_template($template_base.'/file_form_wrapper', [
        'FILE_ID'         => $row_id,
        'FILE_TABLE'      => $table_body['filecards'],
        'REDIRECT_STRING' => $redirect_string,
        'SUBMIT_ID'       => 'hiddenSubmit_'.$row_id,
        'HIDDEN_INPUT'    => $hidden_fields,
    ]);

    // $javascript_html = process_template(
    //     $template_base.'/list_js',
    //     [
    //         '__LAYOUT_URL__' => __LAYOUT_URL__,
    //         'JS_TAG_HTML'    => $js_html,
    //     ]
    // );

    return $table_html;//.$javascript_html;
}

function display_filelist($results, $option = '', $page_array = [], $template_base = 'filelist')
{
    global $db;
    global $hidden_fields;
    $table_html      = '';
    $redirect_string = '';
    $total_files     = '';
    $js_html         = '';

    if (isset($page_array['total_files'])) {
        $total_files = $page_array['total_files'];
    }

    if (isset($page_array['redirect_string'])) {
        $redirect_string = $page_array['redirect_string'];
    }

    foreach ($results as $id => $row) {
        $row_id     = $row['id'];
        $videoInfo  = [];

        $cols       = ['filesize', 'format', 'bit_rate', 'width', 'height'];
        $db->where('video_key', $row['video_key']);
        $videoInfo  = $db->get(Db_TABLE_FILEINFO, null, $cols);

        if (array_key_exists(0, $videoInfo)) {
            $row['video_info'] = $videoInfo[0];
        }

        $table_body = display_fileInfo($row, $total_files, $template_base);
        $js_html .= $table_body['js'];
        $table_html .= process_template($template_base.'/file_form_wrapper', [
            'FILE_ID'         => $row_id,
            'FILE_TABLE'      => $table_body['filecards'],
            'REDIRECT_STRING' => $redirect_string,
            'SUBMIT_ID'       => 'hiddenSubmit_'.$row_id,
            'HIDDEN_INPUT'    => $hidden_fields,
        ]);
    } // end foreach

    // $javascript_html = process_template(
    //     $template_base.'/list_js',
    //     [
    //         '__LAYOUT_URL__' => __LAYOUT_URL__,
    //         'JS_TAG_HTML'    => $js_html,
    //     ]
    // );

    return $table_html;//.$javascript_html;
} // end display_filelist()

function hidden_Field($name, $value)
{
    return '<input type="hidden" name="'.$name.'" value="'.$value.'">'."\n";
}
