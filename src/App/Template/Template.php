<?php
/**
 * plex web viewer
 */

namespace Plex\Template;

use Nette\Utils\FileSystem;
use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
use KubAT\PhpSimple\HtmlDomParser;
use Plex\Template\Functions\Functions;
use Plex\Template\Traits\Callbacks;

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

    public static function echo($template = '', $array = '')
    {
        $template_obj       = new self();
        $template_obj->template($template, $array);

      //  $template_obj->html = $template_obj->parse_urllink($template_obj->html);

        if (true === self::$Render) {
            self::$RenderHTML .= $template_obj->html;
        } else {
            echo $template_obj->html;
        }
    }

    public static function GetHTML($template = '', $array = [])
    {
        $template_obj = new self();
        $template_obj->template($template, $array);

        return $template_obj->html;
    }

    public static function render()
    {
        global $db,$pageObj,$url_array,$studio_url;
        $output           = self::$RenderHTML;

        self::$RenderHTML = '';

        Header::Display();
        $header           = self::$RenderHTML;

        self::$RenderHTML = '';

        Footer::Display();
        $footer           = self::$RenderHTML;

        echo $header.$output.$footer;
    }

    public static function return($template = '', $array = '', $js = '')
    {
        $template_obj = new self();
        $template_obj->template($template, $array, $js);

        return $template_obj->html;
    }


    public function template($template = '', $replacement_array = '', $js = '')
    {
        $extension     = '.html';
        $s_delim       = '%%';
        $e_delim       = '%%';
        if ('' != $js) {
            $extension = '.js';
            $s_delim   = '!!';
            $e_delim   = '!!';
        }

        $template_file = __HTML_TEMPLATE__.'/'.$template.$extension;
        if (!file_exists($template_file)) {
          
                $html_text = '<h1>NO TEMPLATE FOUND<br>';
                $html_text .= 'FOR <pre>'.$template_file.'</pre></h1> <br>';

                $this->html .= $html_text;
        }

        $html_text     = file_get_contents($template_file);
        foreach (__TEMPLATE_CONSTANTS__ as $_ => $key) {
            $value = \constant($key);

            if (\is_array($value)) {
                continue;
            }

            $key   = $s_delim.strtoupper($key).$e_delim;
            if (null != $value) {
                //   dump([$key,$value]);
                $html_text = str_replace($key, $value, $html_text);
            }
        }

        if (\is_array($replacement_array)) {
            foreach ($replacement_array as $varkey => $value) {
                // $value = "<!-- $key --> \n".$value;
                if (null != $value) {
                    $key       = '%%'.strtoupper($varkey).'%%';
                    $html_text = str_replace($key, $value, $html_text);

                    $key       = '!!'.strtoupper($varkey).'!!';
                    $html_text = str_replace($key, $value, $html_text);
                }
            }
        }

        $html_text     = preg_replace_callback('|(%%\w+%%)|', [$this, 'callback_replace'], $html_text);
        $html_text     = preg_replace_callback('|(\!\!\w+\!\!)|', [$this, 'callback_replace'], $html_text);

        $html_text     = preg_replace_callback(self::FUNCTION_CALLBACK, [$this, 'callback_parse_function'], $html_text);
        $html_text     = preg_replace_callback(self::STYLESHEET_CALLBACK, [$this, 'callback_parse_include'], $html_text);
        $html_text     = preg_replace_callback(self::JAVASCRIPT_CALLBACK, [$this, 'callback_parse_include'], $html_text);

        $html_text     = preg_replace_callback('/(##(\w+,?\w+)##)(.*)(##)/iU', [$this, 'callback_color'], $html_text);
        $html_text     = preg_replace_callback('/(!!(\w+,?\w+)!!)(.*)(!!)/iU', [$this, 'callback_badge'], $html_text);

        // '<span $2>$3</span>'
        //  $html_text     = str_replace('  ', ' ', $html_text);
        $html_text     = trim($html_text);
        //   $html_text     = "<!-- start $template -->".PHP_EOL.$html_text.PHP_EOL."<!-- end $template -->". PHP_EOL;
        $this->html    = $html_text.\PHP_EOL;
        // $this->html
        if ('' != $js) {
            $this->html = '<script>'.\PHP_EOL.$this->html.\PHP_EOL.'</script>'.\PHP_EOL;
        }

        return $this->html;
    }

}
