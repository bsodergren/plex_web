<?php
/**
 * plex web viewer
 */

namespace Plex\Utilities;

class PlexStrings
{
    public static function __callStatic($method, $arg)
    {
        $obj    = new static();
        $result = \call_user_func_array([$obj, $method], $arg);
        if (method_exists($obj, $method)) {
            return $result;
        }

        return $obj;
    }

    public static function display_size($bytes, $precision = 2)
    {
        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
        ];
        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, \count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).'<span class="fs-0-8 bold">'.$units[$pow].'</span>';
    } // end display_size()

    public static  function string($string)
    {
        $return = '';
        for ($i = 0; $i < \strlen($string); ++$i) {
            $char = $string[$i];
            $ord  = \ord($char);
            if ("'" !== $char && '"' !== $char && '\\' !== $char && $ord >= 32 && $ord <= 126) {
                $return .= $char;
            } else {
                $return .= '\\x'.dechex($ord);
            }
        }

        return $string;
    } // end string()

    public static  function mysql($string)
    {
        global $mysqli;

        $string = self::string($string);

        if (__USE_MSYQL__ == true) {
            $string = mysqli_real_escape_string($mysqli, $string);
        }

        return $string;
    } // end mysql()

    public static  function clean($string)
    {
        $string = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $string);

        $string = str_replace('---', '--', $string);
        $string = str_replace('--', '-', $string);
        $string = str_replace('-;', '', $string);
        $string = str_replace(';;;', ';;', $string);
        $string = str_replace(';;', ';', $string);
        $string = str_replace('   ', '  ', $string);
        $string = str_replace('  ,', '', $string);
        $string = str_replace('(', '', $string);
        $string = str_replace(')', '', $string);

        $string = str_replace('  ', '', $string);
        $string = str_replace(', ,', ',,', $string);
        $string = str_replace(',;', ',', $string);
        $string = str_replace('" "', '""', $string);
        if (str_starts_with($string, ';')) {
            $string = ltrim($string, ';');
        }

        $string = trim($string);
        \logger("string = '%s%'", $string);

        return $string;
    } // end clean()

    public static function videoDuration($duration)
    {
        $seconds = round($duration / 1000);
        $hours   = floor($seconds / 3600);

        $minutes = round((float) $seconds / 60 % 60);

        $sec     = round($seconds % 60);

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $sec);
    }
}
