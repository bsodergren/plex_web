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

    public static $Render = false;

    private static $RenderHTML = '';

    public static function echo($template = '', $array = '')
    {
        $template_obj = new self();
        $template_obj->template($template, $array);
        if(self::$Render === true){
            self::$RenderHTML .= $template_obj->html;
        } else {
            echo $template_obj->html;
        }
    }

    public static function render()
    {
       global $db,$pageObj,$url_array;
        $output = self::$RenderHTML;
        
        self::$RenderHTML = '';
        require __LAYOUT_HEADER__;
        $header = self::$RenderHTML;

        self::$RenderHTML = '';
        require __LAYOUT_FOOTER__;
        $footer = self::$RenderHTML;

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
            $s_delim   = 'V__';
            $e_delim   = '__V';
        }

        $template_file = __HTML_TEMPLATE__.'/'.$template.$extension;

        if (!file_exists($template_file)) {
            //    dump($template_file);

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

        $html_text     = preg_replace_callback('/(##(\w+,?\w+)##)(.*)(##)/iU', [$this, 'callback_color'], $html_text);
        $html_text     = preg_replace_callback('/(!!(\w+,?\w+)!!)(.*)(!!)/iU', [$this, 'callback_badge'], $html_text);

        // '<span $2>$3</span>'
        //  $html_text     = str_replace('  ', ' ', $html_text);
        $html_text     = trim($html_text);
        //   $html_text     = "<!-- start $template -->".PHP_EOL.$html_text.PHP_EOL."<!-- end $template -->". PHP_EOL;
        $this->html    = $html_text.\PHP_EOL;
        // $this->html
        if ('' != $js) {
            $this->html = "<script>".PHP_EOL.$this->html.PHP_EOL."</script>".PHP_EOL;
        }
        return $this->html;
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
