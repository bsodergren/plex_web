<?php




if(!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }
}


if(!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== FALSE;
    }
}

function strpos_array($haystack, $needles, &$str_return)
{
    if(is_array($needles)) {
        foreach($needles as $str => $v) {
            if(is_array($str)) {
                $pos = strpos_array($haystack, $str);
            } else {
                $pos = strpos($haystack, $str);
            }

            if($pos !== FALSE) {
                $str_return[] = $str;
            }
        }
    } else {
        return strpos($haystack, $needles);
    }
}

function truncateString($string, $maxlength, $ellipsis = FALSE)
{

    if(mb_strlen($string) <= $maxlength) {
        return $string;
    }

    if(str_contains($string, "\033[0m")) {
        $string = str_replace("\033[0m", "", $string);
        $color_length = mb_strlen("\033[0m");
        $color_close = "\033[0m";
    }
    if(empty($ellipsis)) {
        $ellipsis = '';
    }

    if($ellipsis === TRUE) {
        $ellipsis = '…';
    }

    $ellipsis_length = mb_strlen($ellipsis);

    $maxlength = $maxlength - $ellipsis_length - $color_length;

    $string = trim(mb_substr($string, 0, $maxlength)) . $ellipsis . $color_close;

    return $string;

}


function translate($string = "")
{

// "trans -b -no-warn -no-autocorrect "
    return $string;
}

function printCode($array, $path = FALSE, $top = TRUE)
{
    $data = "";
    $delimiter = "~~|~~";

    $p = NULL;
    if(is_array($array)) {
        foreach($array as $key => $a) {
            if(!is_array($a) || empty($a)) {
                if(is_array($a)) {
                    $data .= $path . "['{$key}'] = array();" . $delimiter;
                } else {
                    $data .= $path . "['{$key}'] = \"" . htmlentities(addslashes($a)) . "\";" . $delimiter;
                }
            } else {
                $data .= printCode($a, $path . "['{$key}']", FALSE);
            }
        }
    }

    if($top) {
        $return = "";
        foreach(explode($delimiter, $data) as $value) {
            if(!empty($value)) {
                $return .= '$array' . $value . "<br>";
            }
        };
        return $return;
    }

    return $data;
}
  
  