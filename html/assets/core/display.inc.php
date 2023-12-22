<?php
/**
 * plex web viewer
 */
/*
 * Command like Metatag writer for video files.
 * //  */
// function display_videoInfo($row, $template_base = 'filelist')
// {
//     global $db;
//     global $hidden_fields;
//     $table_html      = '';
//     $redirect_string = '';
//     $total_files     = '';
//     $js_html         = '';

//     $row_id          = $row['id'];

//     $table_body      = (new VideoDisplay($template_base))->fileInfo($row, $total_files);
//     $js_html .= $table_body['js'];
//     $table_html .= process_template($template_base.'/file_form_wrapper', [
//         'FILE_ID'         => $row_id,
//         'FILE_TABLE'      => $table_body['filecards'],
//         'REDIRECT_STRING' => $redirect_string,
//         'SUBMIT_ID'       => 'hiddenSubmit_'.$row_id,
//         'HIDDEN_INPUT'    => $hidden_fields,
//     ]);

//     // $javascript_html = process_template(
//     //     $template_base.'/list_js',
//     //     [
//     //         '__LAYOUT_URL__' => __LAYOUT_URL__,
//     //         'JS_TAG_HTML'    => $js_html,
//     //     ]
//     // );

//     return $table_html; // .$javascript_html;
// }
