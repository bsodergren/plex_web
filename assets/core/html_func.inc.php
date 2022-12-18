<?php


function callback_replace($matches)
{
    return '';

}//end callback_replace()


function process_template($template, $replacement_array='')
{
    $template_file = __LAYOUT_PATH__.'/template/'.$template.'.html';

    $html_text = file_get_contents($template_file);

    if (is_array($replacement_array)) {
        foreach ($replacement_array as $key => $value) {
            $key       = '%%'.strtoupper($key).'%%';
            $html_text = str_replace($key, $value, $html_text);
        }

        $html_text = preg_replace_callback('|(%%\w+%%)|', 'callback_replace', $html_text);
    }

    $html_text = "\n <!-- start $template --> \n".$html_text."\n";
    return $html_text;

}//end process_template()


function output($var)
{
    if (is_array($var)) {
        print_r2($var);
        return 0;
    }

    echo $var."\n";
    // return 0;

}//end output()


function create_form($url, $method, $input)
{
    $html  = '';
    $html .= '<form action="'.$url.'" method="'.$method.'">'."\n";
    $html .= $input;
    $html .= "</form>\n";
    output($html);

}//end create_form()


function add_submit_button($name, $value, $attributes='')
{
    $html  = '';
    $html .= '<input '.$attributes.' type="submit" name="'.$name.'"  value="'.$value.'">';
    return $html."\n";

}//end add_submit_button()


function add_hidden($name, $value, $attributes='')
{
    $html  = '';
    $html .= '<input '.$attributes.' type="hidden" name="'.$name.'"  value="'.$value.'">';
    return $html."\n";

}//end add_hidden()


function draw_link($url, $text, $attributes='', $return=true)
{
    global $_SESSION;

    $html  = '';
    $html .= '<a '.$attributes.'  href="'.$url.'">'.$text.'</a>';
    if ($return == true) {
        return $html."\n";
    } else {
        output($html);
    }

}//end draw_link()


function draw_textbox($name, $value, $attributes='')
{
    $html  = '';
    $html .= '<input '.$attributes.' type="text" name="'.$name.'" placeholder="'.$value.'" value="'.$value.'">';
    return $html;

}//end draw_textbox()


function draw_checkbox($name, $value, $text='Face Trim')
{
    global $pub_keywords;

    $checked = '';

    $current_value = $value;

    if ($current_value == 1) {
        $checked = 'checked';
    }

    $html  = '';
    $html .= '<input type="checkbox" name="'.$name.'" value="1" '.$checked.'>'.$text;
    return $html;

}//end draw_checkbox()


function draw_radio($name, $value)
{
    $html = '';

    foreach ($value as $option) {
        $html .= '<input type="radio" class="'.$option['class'].'" name="'.$name.'" value="'.$option['value'].'" '.$option['checked'].'>'.$option['text'].'&nbsp;';
    }

    // $html = $html . "<br>"."\n";
    return $html;

}//end draw_radio()


function JavaRefresh($url, $timeout=0)
{
    global $_REQUEST;

    $html = '<script>'."\n";

    if ($timeout > 0) {
        $html .= 'setTimeout(function(){ ';
    }

    $html .= "window.location.href = '".$url."';";

    if ($timeout > 0) {
        $html .= '}, '.$timeout.');';
    }

    $html .= "\n".'</script>';

    return $html;

}//end JavaRefresh()
