<?php
/**
 * plex web viewer
 */

/**
 * plex web viewer.
 */
class Template
{
    public $html;

    public static $Render      = false;
    public static $flushdummy;
    public static $BarStarted  = false;
    public static $BarHeight   = 30;

    private static $RenderHTML = '';
    public const FUNCTION_CALLBACK     = '|{{function=([a-zA-Z_]+)\|?(.*)?}}|i';

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
        $template_obj = new self();
        $template_obj->template($template, $array);
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

        require __LAYOUT_HEADER__;
        $header           = self::$RenderHTML;

        self::$RenderHTML = '';

        require __LAYOUT_FOOTER__;
        $footer           = self::$RenderHTML;

        echo $header.$output.$footer;
    }

    public static function return($template = '', $array = '', $js = '')
    {
        $template_obj = new self();
        $template_obj->template($template, $array, $js);

        return $template_obj->html;
    }

    public function callback_replace($matches)
    {
        return '';
    }

    /*

        public function clear()
        {
            $this->html = '';
        }

        public function return($template='', $array='')
        {
            if ($template) {
                $this->template($template, $array);
            }

            $html = $this->html;
            $this->clear();
            return $html;
        }

        public function render($template='', $array='')
        {
            if ($template) {
                $this->template($template, $array);
            }

            $html = $this->html;
            $this->clear();
            echo $html;
        }
    */

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
             dump($template_file);

            // use default template directory
            $html_text = '<h1>NO TEMPLATE FOUND<br>';
            $html_text .= 'FOR <pre>'.$template_file.'</pre></h1> <br>';

            $this->html .= $html_text;
        }
        $html_text     = file_get_contents($template_file);
        foreach (__TEMPLATE_CONSTANTS__ as $key) {
            $value = constant($key);
            $key   = $s_delim.strtoupper($key).$e_delim;
            if (null != $value) {
                $html_text = str_replace($key, $value, $html_text);
            }
        }

        if (is_array($replacement_array)) {
            
            foreach ($replacement_array as $key => $value) {

                // $value = "<!-- $key --> \n".$value;
                $key = $s_delim.strtoupper($key).$e_delim;
                if (null != $value) {
                    $html_text = str_replace($key, $value, $html_text);
                }
            }
        }

        $html_text     = preg_replace_callback('|(%%\w+%%)|', [$this, 'callback_replace'], $html_text);
        $html_text     = preg_replace_callback('|(\!\!\w+\!\!)|', [$this, 'callback_replace'], $html_text);



        $html_text         = preg_replace_callback(self::FUNCTION_CALLBACK, [$this, 'callback_parse_function'], $html_text);

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

    public function callback_parse_function($matches)
    {
         $helper = new HTML_Func;
        $method = $matches[1];
        dump(__METHOD__,$helper);
        // $value = Helper::$method();
       // if(method_exists($helper,$method)){
           return  $helper->$method($matches);
       // }

    }
        private function callback_badge($matches)
    {
        $text  = $matches[3];
        $font  = '';
        $class = $matches[2];
        if (str_contains($matches[2], ',')) {
            $arr   = explode(',', $matches[2]);
            $class = $arr[0];
            $font  = 'fs-'.$arr[1];
        }

        $style = 'class="badge text-bg-'.$class.' '.$font.'"';

        return '<span '.$style.'>'.$text.'</span>';
    }

    private function callback_color($matches)
    {
        $text  = $matches[3];
        $style = 'style="';
        if (str_contains($matches[2], ',')) {
            $colors = explode(',', $matches[2]);
            $style .= 'color: '.$colors[0].'; background:'.$colors[1].';';
        } else {
            $style .= 'color: '.$matches[2].';';
        }
        $style .= '"';

        return '<span '.$style.'>'.$text.'</span>';
    }
}
