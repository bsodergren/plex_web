<?php


function callback_replace($matches)
{
    return '';

}//end callback_replace()


function process_template($template, $replacement_array='')
{

    $template_file = __HTML_TEMPLATE__.$template.'.html';

    $html_text = file_get_contents($template_file);

    if (is_array($replacement_array)) {
        foreach ($replacement_array as $key => $value) {
            $key       = '%%'.strtoupper($key).'%%';
            $html_text = str_replace($key, $value, $html_text);
        }

        $html_text = preg_replace_callback('|(%%\w+%%)|', 'callback_replace', $html_text);
    }

    $html_text = "\n <!-- start  $template --> \n".$html_text."\n";
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
