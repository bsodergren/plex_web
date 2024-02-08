<?php

namespace Plex\Template\Display\Traits;

use Plex\Template\Render;

trait VideoRow
{
    public function row($field, $value, $class, $id = '')
    {
        $editable = '';
        $value = trim($value);

        if (\defined('NONAVBAR')) {
            if ('' != $id) {
                $id = ucfirst($id);
                $editableClass = 'edit'.$id;
                $functionName = 'make'.$id.'Editable';

                $this->params['VIDEOINFO_EDIT_JS'] .= Render::javascript(
                    $this->template_base.'/Rows/row',
                    [
                        'ID_NAME' => $id,
                        'EDITABLE' => $editableClass,
                        'FUNCTION' => $functionName,
                        'VIDEO_KEY' => $this->params['video_key'],
                    ]
                );

                $editable = $editableClass;

                $add_button = Render::html(
                    $this->template_base.'/add_button',
                    [
                        'EDITABLE' => $editable,
                    ]
                );
            }
        }
        $this->params['FIELD_ROW_HTML'] .= Render::html(
            $this->template_base.'/Rows/row',
            [
                // 'ADD_BUTTON'  => $add_button,
                'FIELD' => $field,
                'VALUE' => $value,
                'ALT_CLASS' => $this->AltClass,
                'EDITABLE' => $editable,
            ]
        );
    }

    public function filesizeRow($field, $value, $duration, $class, $id = '')
    {
        $editable = '';

        $this->params['FIELD_ROW_HTML'] .= Render::html(
            $this->template_base.'/Rows/duration',
            [
                'DUR_FIELD' => 'Duration',
                'DUR_VALUE' => $duration,
                'FS_FIELD' => $field,
                'FS_VALUE' => $value,
                'ALT_CLASS' => $class,
                'EDITABLE' => $editable,
            ]
        );
    }

    public function metaRow($key)
    {
        $value = $this->fileInfoArray[$key];
        $value = keyword_list($key, $value);
        $this->info($key, $value);
    }

    public function info($key)
    {
        $value = $this->fileInfoArray[$key];


        $value = str_replace(__PLEX_LIBRARY__.'/', '', $value);

        $this->row(ucfirst($key), $value, $this->AltClass, $key);
    }

    public function Rating($key)
    {
        $value = $this->fileInfoArray[$key];
        $this->params['STAR_RATING'] = $value;
    }

    public function Studio($key)
    {
        $this->params['HIDDEN_STUDIO'] = add_hidden(strtolower($key), $this->fileInfoArray[$key]);

        $this->metaRow($key);
    }

    public function Substudio($key)
    {
        $this->params['HIDDEN_STUDIO'] = add_hidden(strtolower($key), $this->fileInfoArray[$key]);

        $this->metaRow($key);
    }

    public function Genre($key)
    {
        $this->metaRow($key);
    }

    public function Artist($key)
    {
        $this->metaRow($key);
    }

    public function Keyword($key)
    {
        $this->metaRow($key);
    }

    public function Title($key)
    {
        $this->metaRow($key);
    }

    public function Fullpath($key)
    {
        $filename = $this->fileInfoArray['filename'];
        $fullpath = $this->fileInfoArray['fullpath'];

        $full_filename = $fullpath.\DIRECTORY_SEPARATOR.$filename;
        $class_missing = '';
        if (!file_exists($full_filename)) {
            $class_missing = 'bg-danger';
        }
        $value = str_replace(__PLEX_LIBRARY__.'/', '', $full_filename);
        $this->row(ucfirst($key), $value, $this->AltClass);
        $this->params['FILE_MISSING'] = $class_missing;
    }

    public function Filesize($key)
    {
        $duration = videoDuration($this->fileInfoArray['duration']);
        $filesize = $this->fileInfoArray['filesize'];

        $this->filesizeRow(ucfirst($key), display_size($filesize), $duration, $this->AltClass);
    }

    public function Format()
    {

        $fileInfo = [
            'width',
            'height',
            'format',
        ];
        foreach ($fileInfo as $key) {
            $value = $this->fileInfoArray[$key];
            $infoParams[strtoupper($key)] = $value;
        }

        $infoParams[strtoupper('bit_rate')] = byte_convert($this->fileInfoArray['bit_rate']);


        // if (true == $this->showVideoDetails) {
            if (\is_array($infoParams)) {
                $this->row('Info', Render::html(
                    $this->template_base.'/Rows/info',
                    $infoParams
                ), $this->AltClass);
            }
        // }
    }
}
