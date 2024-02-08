<?php

namespace Plex\Template\VideoCard\Traits;

use Plex\Template\Render;
use Plex\Template\HTML\Elements;


trait VideoRow
{


   public static function videoDuration($duration)
    {
        $seconds = round($duration / 1000);
        $hours   = floor($seconds / 3600);
    
        $minutes = round((float) $seconds / 60 % 60);
    
        $sec     = round($seconds % 60);
    
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $sec);
    }
    
   public static function display_size($bytes, $precision = 2)
    {
        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
        ];
        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
    
        return round($bytes, $precision).'<span class="fs-0-8 bold">'.$units[$pow].'</span>';
    } // end display_size()
    
    public static function byte_convert($size)
    {
        // size smaller then 1kb
        if ($size < 1024) {
            return $size.' Byte';
        }
    
        // size smaller then 1mb
        if ($size < 1048576) {
            return sprintf('%4.2f KB', $size / 1024);
        }
    
        // size smaller then 1gb
        if ($size < 1073741824) {
            return sprintf('%4.2f MB', $size / 1048576);
        }
    
        // size smaller then 1tb
        if ($size < 1099511627776) {
            return sprintf('%4.2f GB', $size / 1073741824);
        }
        // size larger then 1tb
    
        return sprintf('%4.2f TB', $size / 1073741824);
    } // end byte_convert()
    
    private function metaValue($key,$cloud = false)
    {
        $value = $this->fileInfoArray[$key];
        $value = trim($value);
        $value = str_replace(__PLEX_LIBRARY__.'/', '', $value);
        
        if (!\defined('NONAVBAR')) {
            if($cloud === true){
                $value = $this->keyword_list($key, $value);
            }
        }

        return $value;
    }


    public function row($field, $value, $id = '')
    {
        $editableClass = '';

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
            }
        }
        $this->params['FIELD_ROW_HTML'] .= Render::html(
            $this->template_base.'/Rows/row',
            [
                'FIELD' => $field,
                'VALUE' => $value,
                'ALT_CLASS' => $this->AltClass,
                'EDITABLE' => $editableClass,
            ]
        );
    }

    public function filesizeRow($field, $value, $duration, $id = '')
    {
        $editable = '';

        $this->params['FIELD_ROW_HTML'] .= Render::html(
            $this->template_base.'/Rows/duration',
            [
                'DUR_FIELD' => 'Duration',
                'DUR_VALUE' => $duration,
                'FS_FIELD' => $field,
                'FS_VALUE' => $value,
                'ALT_CLASS' => $this->AltClass,
                'EDITABLE' => $editable,
            ]
        );
    }

    public function keyword_list($key, $list)
    {
        if ('' == $list) {
            return '';
        }

        $link_array = [];
        $value = '';
        $list_array = explode(',', $list);

        foreach ($list_array as $k => $keyword) {
            $link_array[] = Render::html(
                $this->template_base.'/search_link',
                [
                    'KEY' => $key,
                    'QUERY' => urlencode($keyword),
                    'URL_TEXT' => $keyword,
                    //  'CLASS'    => ' class="badge fs-6 blueTable-thead" ',
                ]
            );
        }

        return implode('  ', $link_array);
    }

    public function cloudRow($key)
    {
        $this->row(ucfirst($key), $this->metaValue($key,true), $key);
    }

    public function metaRow($key)
    {
        $this->row(ucfirst($key), $this->metaValue($key), $key);
    }

    public function info($key)
    {
        $this->row(ucfirst($key), $this->metaValue($key), $key);
    }

    public function Rating($key)
    {
        $this->params['STAR_RATING'] =  $this->metaValue($key);
    }

    public function Studio($key)
    {
        
        $this->params['HIDDEN_STUDIO'] = Elements::add_hidden(strtolower($key), $this->metaValue($key));

        $this->cloudRow($key);
    }

    public function Substudio($key)
    {
        $this->params['HIDDEN_STUDIO'] = Elements::add_hidden(strtolower($key), $this->metaValue($key));

        $this->cloudRow($key);
    }

    public function Genre($key)
    {
        $this->cloudRow($key);
    }

    public function Artist($key)
    {
        $this->cloudRow($key);
    }

    public function Keyword($key)
    {
        $this->cloudRow($key);
    }

    public function Title($key)
    {
        $this->metaRow($key);
    }

    public function Fullpath($key)
    {
        
        $filename = $this->metaValue('filename');
        $fullpath = $this->metaValue('fullpath');

        $full_filename = $fullpath.\DIRECTORY_SEPARATOR.$filename;
        $class_missing = '';

        if (!file_exists(__PLEX_LIBRARY__.'/'.$full_filename)) {
            $class_missing = 'bg-danger';
        }

        $this->row(ucfirst($key), $full_filename);
        $this->params['FILE_MISSING'] = $class_missing;
    }

    public function Filesize($key)
    {
        $duration = self::videoDuration($this->metaValue('duration'));
        $filesize = $this->metaValue('filesize');

        $this->filesizeRow(ucfirst($key), self::display_size($filesize), $duration);
    }

    public function Format()
    {
        $fileInfo = [
            'width',
            'height',
            'format',
        ];
        foreach ($fileInfo as $key) {
            $value = $this->metaValue($key);
            $infoParams[strtoupper($key)] = $value;
        }

        $infoParams[strtoupper('bit_rate')] = self::byte_convert($this->metaValue('bit_rate'));

        // if (true == $this->showVideoDetails) {
        if (\is_array($infoParams)) {
            $this->row('Info', Render::html(
                $this->template_base.'/Rows/info',
                $infoParams
            ));
        }
        // }
    }
}
