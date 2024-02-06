<?php
/**
 * plex web viewer
 */

if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle)
    {
        return str_starts_with($haystack, $needle);
    }
}

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return '' !== $needle && false !== mb_strpos($haystack, $needle);
    }
}

function strpos_array($haystack, $needles, &$str_return)
{
    if (is_array($needles)) {
        foreach ($needles as $str => $v) {
            if (is_array($str)) {
                $pos = strpos_array($haystack, $str);
            } else {
                $pos = strpos($haystack, $str);
            }

            if (false !== $pos) {
                $str_return[] = $str;
            }
        }
    } else {
        return strpos($haystack, $needles);
    }
}

function truncateString($string, $maxlength, $ellipsis = false)
{
    if (mb_strlen($string) <= $maxlength) {
        return $string;
    }

    if (str_contains($string, "\033[0m")) {
        $string       = str_replace("\033[0m", '', $string);
        $color_length = mb_strlen("\033[0m");
        $color_close  = "\033[0m";
    }
    if (empty($ellipsis)) {
        $ellipsis = '';
    }

    if (true === $ellipsis) {
        $ellipsis = '…';
    }

    $ellipsis_length = mb_strlen($ellipsis);

    $maxlength       = $maxlength - $ellipsis_length - $color_length;

    return trim(mb_substr($string, 0, $maxlength)).$ellipsis.$color_close;
}

function translate($string = '')
{
    // "trans -b -no-warn -no-autocorrect "
    return $string;
}

function printCode($array, $path = false, $top = true)
{
    $data      = '';
    $delimiter = '~~|~~';

    $p         = null;
    if (is_array($array)) {
        foreach ($array as $key => $a) {
            if (!is_array($a) || empty($a)) {
                if (is_array($a)) {
                    $data .= $path."['{$key}'] = array();".$delimiter;
                } else {
                    $data .= $path."['{$key}'] = \"".htmlentities(addslashes($a)).'";'.$delimiter;
                }
            } else {
                $data .= printCode($a, $path."['{$key}']", false);
            }
        }
    }

    if ($top) {
        $return = '';
        foreach (explode($delimiter, $data) as $value) {
            if (!empty($value)) {
                $return .= '$array'.$value.'<br>';
            }
        }

        return $return;
    }

    return $data;
}
