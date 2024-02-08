<?php 
namespace Plex\Template\HTML;

use Nette\Utils\FileSystem;
use Plex\Template\Template;

class Elements
{


    public static function getTemplate($file)
    {
        $file_copy = str_replace('layouts', 'tmpLayout', $file);
        if (file_exists($file_copy)) {
            $file_dir = \dirname($file);

            FileSystem::createDir($file_dir);
            FileSystem::rename($file_copy, $file);
            FileSystem::delete($file_copy);

            return true;
        }

        return false;
    }
    public static function stylesheet($stylesheet)
    {

        $stylesheet =  'css/' . $stylesheet;
        $file = __LAYOUT_PATH__ . '/'. $stylesheet;

        if(file_exists($file) == false ){
            self::getTemplate($file);
        }

        return Template::getHtml('elements/html/link',['CSS_URL' => __LAYOUT_URL__ .$stylesheet]);
    }

    public static function javascript($javafile)
    {
        $javafile =  'js/' . $javafile;
        $file = __LAYOUT_PATH__ . '/'. $javafile;

        if(file_exists($file) == false ){
            self::getTemplate($file);
        }
        return Template::getHtml('elements/html/script',['SCRIPT_URL' => __LAYOUT_URL__ .$javafile]);
    }


    public static function addButton($text,$type='button',$class='btn button',$extra='',$javascript='')
    {

        

        return Template::getHtml('elements/html/button',[
            'TEXT' =>$text,
            'TYPE' => $type,
            'CLASS' => $class,
            'EXTRA' => $extra,
            'JAVASCRIPT' => $javascript,
    ]);
    }
}