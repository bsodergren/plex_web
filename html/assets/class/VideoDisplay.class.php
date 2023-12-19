<?php
/**
 * plex web viewer
 */

class VideoDisplay
{
    private $template_base = '';

    public $showVideoDetails = false;
    public function __construct($template_base = 'filelist') {
        $this->template_base = $template_base ;
    }

    public function fileRow($params, $field, $value, $class, $id = '')
    {
        $videoinfo_js = '';
        $editable     = '';
        if (defined('NONAVBAR')) {
            if ('' != $id) {
                $id            = ucfirst($id);
                $editableClass = 'edit'.$id;
                $functionName  = 'make'.$id.'Editable';

                $params['VIDEOINFO_EDIT_JS'] .= process_javascript(
                    'videoinfo/filerow_js',
                    [
                        'ID_NAME'   => $id,
                        'EDITABLE'  => $editableClass,
                        'FUNCTION'  => $functionName,
                        'VIDEO_KEY' => $params['video_key'],
                    ]
                );

                $editable      = $editableClass;
            }
        }
        $params['FIELD_ROW_HTML'] .= process_template(
            'videoinfo/file_row',
            [
                'FIELD'             => $field,
                'VALUE'             => $value,
                'ALT_CLASS'         => $class,
                'EDITABLE'          => $editable,
            ]
        );

        return $params;
    }

    public function fileInfo($fileInfoArray, $total_files)
    {
        global $db;
        $table_body_html              = [];
        $x                            = 0;
        $row_id                       = $fileInfoArray['id'];
        // $row_filename = $row_id.":".$row['filename'];
        $row_filename                 = $fileInfoArray['filename'];
        $row_fullpath                 = $fileInfoArray['fullpath'];
        $row_video_key                = $fileInfoArray['video_key'];

        if (isset($fileInfoArray['rownum'])) {
            $result_number = $fileInfoArray['rownum'];
        }

        $params['FILE_NAME']          = $row_filename;
        if ($fileInfoArray['title']) {
            $params['FILE_NAME'] = $fileInfoArray['title'];
        }
        $params['ROW_ID']             = '';
        if (!defined('NONAVBAR')) {
            $params['FILE_NAME']     =  process_template(
                $this->template_base.'/popup_js',
                ['APP_HOME'     => APP_HOME,
                    'ROW_ID'    => $row_id,
                    'FILE_NAME' => $params['FILE_NAME']]
            );

            $params['VERTICAL_TEXT'] = process_template(
                $this->template_base.'/file_vertical',
                ['ROW_ID' => '&nbsp;&nbsp;&nbsp;'.$result_number.' of '.$total_files]
            );
        }

        // $params['DELETE_ID']          = 'delete_'.$row_id;
        $params['FILE_NAME_ID']       = $row_id.'_filename';
        $params['FULL_PATH']          = $row_fullpath;
        $params['FILE_ID']            = $row_id;
        $params['DELETE_ID']          = add_hidden('id', $row_id);
        // krsort($fileInfoArray);
        // dd($fileInfoArray);
        $params['video_key']          = $row_video_key;
        foreach ($fileInfoArray as $key => $value) {
            $class       = (0 == $x % 2) ? 'text-bg-primary' : 'text-bg-secondary';
            $value_array = [];

            switch ($key) {
                // case 'favorite':
                case 'library':
                case 'title':
                    $value  = str_replace(__PLEX_LIBRARY__.'/', '', $value);
                    $params = $this->fileRow($params, ucfirst($key), $value, $class, $key);
                    ++$x;

                    break;

                case 'filename':
                case 'fullpath':
                    $value  = str_replace(__PLEX_LIBRARY__.'/', '', $value);
                    $params = $this->fileRow($params, ucfirst($key), $value, $class);
                    ++$x;

                    break;

                case 'added':
                    $params = $this->fileRow($params, ucfirst($key), $value, $class);
                    ++$x;

                    break;

                case 'thumbnail':
                    if (__SHOW_THUMBNAILS__ == true) {
                        $params['THUMBNAIL_HTML'] .= process_template(
                            $this->template_base.'/file_thumbnail',
                            [
                                'THUMBNAIL' => __URL_HOME__.$value,
                                'FILE_ID'   => $row_id,
                            ]
                        );
                    }

                    break;

                case 'duration':
                    $params = $this->fileRow($params, 'Duration', videoDuration($value), $class);
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

                        $params = $this->fileRow($params, ucfirst($key), $value, $class, $key);
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
                        $params = $this->fileRow($params, ucfirst($key), $value, $class, $key);
                        ++$x;
                    }

                    break;

                case 'filesize':
                    $params = $this->fileRow($params, ucfirst($key), display_size($value), $class);
                    ++$x;

                    break;

                // case 'video_info':
                //     // if (defined('NONAVBAR')) {
                //     foreach ($value as $infokey => $fileInfoValue) {
                //         //    $class = ($x % 2 == 0) ? 'bg-info' : '';
                //         //    $value_array = [];
                //         switch ($infokey) {
                            case 'bit_rate':
                                $infoParams[strtoupper($key)] =  byte_convert($value);

                                break;

                            case 'width':
                            case 'height':
                            case 'format':
                                $infoParams[strtoupper($key)] = $value;

                                break;
                    //     }
                    // }
                    // $infoParams['ALT_CLASS'] =  $class;

             

                    //break;
            } // end switch


           // ++$x;
        } // end foreach
        if($this->showVideoDetails == true){
            $params = $this->fileRow($params, '', process_template(
                $this->template_base.'/file_videoinfo',
                $infoParams
            ), $class);
        }


        $table_body_html['filecards'] = process_template($this->template_base.'/file', $params);

        return $table_body_html;
    }



    function fileList($results, $option = '', $page_array = [])
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
    
            // $cols       = ['format', 'bit_rate', 'width', 'height'];
            // $db->where('video_key', $row['video_key']);
            // $videoInfo  = $db->get(Db_TABLE_VIDEO_INFO, null, $cols);
    
            // if (array_key_exists(0, $videoInfo)) {
            //     $row['video_info'] = $videoInfo[0];
            // }
    
            $table_body =  $this->fileInfo($row, $total_files);
    
            $js_html .= $table_body['js'];
            $table_html .= process_template($this->template_base.'/file_form_wrapper', [
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
    
        return $table_html; // .$javascript_html;
    } // end display_filelist()
    


}
