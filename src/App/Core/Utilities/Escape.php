<?php
/**
 *  Plexweb
 */

namespace Plex\Core\Utilities;

class Escape
{
    private $string;

    public function string($string)
    {
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

    public function mysql($string)
    {
        global $mysqli;

        $string = self::string($string);

        if (__USE_MSYQL__ == true) {
            $string = mysqli_real_escape_string($mysqli, $string);
        }

        return $string;
    } // end mysql()

    public function clean($string)
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
        logger("string = '%s%'", $string);

        return $string;
    } // end clean()
} // end class
