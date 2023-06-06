
<?php


class Template
{
    public $html = '';


    public static function echo($template='', $array='')
    {
        $template_obj = new Template();
        $template_obj->template($template, $array);
        return $template_obj->html;
    }

    public function callback_replace($matches)
    {
        return "";
    }

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


    public function template($template, $replacement_array='')
    {
        $template_file=__HTML_TEMPLATE__."/".$template.".html";


        if (!file_exists($template_file)) {
        //    dump($template_file);

            //use default template directory
            $html_text = "<h1>NO TEMPLATE FOUND<br>";
            $html_text .= "FOR <pre>".$template_file."</pre></h1> <br>";

            $this->html .= $html_text;
        }
        $html_text = file_get_contents($template_file);

        if (is_array($replacement_array)) {
            foreach ($replacement_array as $key => $value) {
                // $value = "<!-- $key --> \n".$value;
                $key = "%%".strtoupper($key)."%%";
                if ($value != null) {
                    $html_text = str_replace($key, $value, $html_text);
                }
            }

            $html_text = preg_replace_callback('|(%%\w+%%)|', array($this, "callback_replace"), $html_text);
        }

       $html_text = preg_replace_callback('/(##(\w+,?\w+)##)(.*)(##)/iU', array($this, "callback_color"), $html_text);
       $html_text = preg_replace_callback('/(!!(\w+,?\w+)!!)(.*)(!!)/iU', array($this, "callback_badge"), $html_text);

       //'<span $2>$3</span>'


        $html_text = "<!-- start $template --> \n" . $html_text . "\n";
        $this->html .= $html_text;
        $this->html .= "\n <!-- end $template --> \n" ;
        return $this->html;
    }

    private function callback_badge($matches)
    {

        $text = $matches[3];
        $font='';
        $class = $matches[2];
        if (str_contains($matches[2], ",")) {
            $arr = explode(",", $matches[2]);
            $class = $arr[0];
            $font = "fs-".$arr[1];
        }

        $style = 'class="badge text-bg-' . $class.' '.$font.'"';
        return '<span '.$style.'>' . $text . '</span>';
    }
    private function callback_color($matches)
    {
        $text = $matches[3];
        $style = 'style="';
        if (str_contains($matches[2], ",")) {
            $colors = explode(",",$matches[2]);
            $style .= 'color: ' . $colors[0].'; background:' .$colors[1] .';';
        } else {
            $style .= 'color: ' . $matches[2].';';
        }
        $style .= '"';

        return '<span '.$style.'>' . $text . '</span>';
    }   
}
