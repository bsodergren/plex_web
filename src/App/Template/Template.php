<?php
/**
 * plex web viewer
 */

namespace Plex\Template;

use Nette\Utils\FileSystem;
use Plex\Template\HTML\Elements;
use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
use KubAT\PhpSimple\HtmlDomParser;
use Plex\Template\Traits\Callbacks;
use Plex\Template\Functions\Functions;

class Template
{
    use Callbacks;
    public $html;

    public static $Render          = false;
    public static $flushdummy;
    public static $BarStarted      = false;
    public static $BarHeight       = 30;

    private static $RenderHTML     = '';

    public function __construct()
    {
        ob_implicit_flush(true);
        @ob_end_flush();

        $flushdummy       = '';
        for ($i = 0; $i < 1200; ++$i) {
            $flushdummy .= '      ';
        }
        self::$flushdummy = $flushdummy;
    }

    public static function ProgressBar($timeout = 5, $name = 'theBar')
    {
        if ('start' == strtolower($timeout)) {
            self::$BarStarted = true;
            self::pushhtml('progress_bar', ['NAME' => $name, 'BAR_HEIGHT' => self::$BarHeight]);

            return;
        }

        if ($timeout > 0) {
            $timeout *= 1000;
            $update_inv = $timeout / 100;
            if (false == self::$BarStarted) {
                self::pushhtml('progress_bar', ['NAME' => $name, 'BAR_HEIGHT' => self::$BarHeight]);
                self::$BarStarted = false;
            }

            self::pushhtml('progressbar_js', ['SPEED' => $update_inv, 'NAME' => $name]);
        }
    }

    public static function pushhtml($template, $params = [])
    {
        $contents = self::GetHTML($template, $params);
        self::push($contents);
    }

    public static function put($contents, $color = null, $break = true)
    {
        $nlbr = '';
        if (null !== $color) {
            $colorObj = new Colors();
            //    $contents = $colorObj->getColoredSpan($contents, $color);
        }
        if (true == $break) {
            $nlbr = '<br>';
        }
        // echo $contents;
        self::push($contents.'  '.$nlbr);
    }

    public static function push($contents)
    {
        echo $contents; // , self::$flushdummy;
        flush();
        @ob_flush();
    }


    public function template($template = '', $replacement_array = '', $extension = 'html')
    {
        unset($this->replacement_array);
        if($extension == ''){
            $extension = 'html';
        }
        
        $extension     = '.'.$extension;

        $template_file = __HTML_TEMPLATE__.'/'.$template.$extension;
        if (!file_exists($template_file)) {
          
                $html_text = '<h1>NO TEMPLATE FOUND<br>';
                $html_text .= 'FOR <pre>'.$template_file.'</pre></h1> <br>';
                $this->html = $html_text;
                return $html_text;
        }

        $html_text     = file_get_contents($template_file);
        $this->replacement_array = $replacement_array;

        $html_text     = preg_replace_callback(self::VARIABLE_CALLBACK, [$this, 'callback_parse_variable'], $html_text);
        $html_text     = preg_replace_callback(self::JS_VAR_CALLBACK, [$this, 'callback_parse_variable'], $html_text);
        $html_text     = preg_replace_callback(self::FUNCTION_CALLBACK, [$this, 'callback_parse_function'], $html_text);
        $html_text     = preg_replace_callback(self::STYLESHEET_CALLBACK, [$this, 'callback_parse_include'], $html_text);
        $html_text     = preg_replace_callback(self::JAVASCRIPT_CALLBACK, [$this, 'callback_parse_include'], $html_text);
        $html_text     = preg_replace_callback('/(##(\w+,?\w+)##)(.*)(##)/iU', [$this, 'callback_color'], $html_text);
        
        // $html_text     = preg_replace_callback('/(!!(\w+,?\w+)!!)(.*)(!!)/iU', [$this, 'callback_badge'], $html_text);

        $html_text     = trim($html_text);
        // 
        if ('.js' == $extension) {
            $html_text = '<script>'.\PHP_EOL.$html_text.\PHP_EOL.'</script>'.\PHP_EOL;
        }
        $this->html = $html_text;
        return $html_text;
    }

}
