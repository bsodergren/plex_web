
<?php


class Template
{
    public $html = '';


    public static function echo($template='',$array='')
    {
        $template_obj = new Template();
        $template_obj->template($template,$array);
        return $template_obj->html;
        
    }

    public function callback_replace($matches)
    {
        return "";
    }

    public function clear(){
        $this->html = '';
    }

    public function return($template='',$array='')
    {
        if($template)
        {
            $this->template($template,$array);
        }
        
        $html = $this->html;
        $this->clear();
        return $html;
    }

    public function render($template='',$array='')
    {
        if($template)
        {
            $this->template($template,$array);
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
                if($value != null ) {
                    $html_text = str_replace($key, $value, $html_text);
                }
            }

            $html_text = preg_replace_callback('|(%%\w+%%)|', array($this, "callback_replace"), $html_text);
        }

      //  $html_text = "<!-- start $template --> \n" . $html_text . "\n";
        $this->html .= $html_text;
        return $this->html;
    }




}