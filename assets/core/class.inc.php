<?php

class Colors
{
    private $foreground_colors = array();
    private $background_colors = array();

    public function __construct()
    {
        // Set up shell colors
        $this->foreground_colors['black'] = '0;30';
        $this->foreground_colors['dark_gray'] = '1;30';
        $this->foreground_colors['blue'] = '0;34';
        $this->foreground_colors['light_blue'] = '1;34';
        $this->foreground_colors['green'] = '0;32';
        $this->foreground_colors['light_green'] = '1;32';
        $this->foreground_colors['cyan'] = '0;36';
        $this->foreground_colors['light_cyan'] = '1;36';
        $this->foreground_colors['red'] = '0;31';
        $this->foreground_colors['light_red'] = '1;31';
        $this->foreground_colors['purple'] = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown'] = '0;33';
        $this->foreground_colors['yellow'] = '1;33';
        $this->foreground_colors['light_gray'] = '0;37';
        $this->foreground_colors['white'] = '1;37';

        $this->background_colors['black'] = '40';
        $this->background_colors['red'] = '41';
        $this->background_colors['green'] = '42';
        $this->background_colors['yellow'] = '43';
        $this->background_colors['blue'] = '44';
        $this->background_colors['magenta'] = '45';
        $this->background_colors['cyan'] = '46';
        $this->background_colors['light_gray'] = '47';
    }

    // Returns colored string
    public function getColoredHTML($string, $foreground_color = NULL, $background_color = NULL)
    {
        $colored_string = "<span";

        // Check if given foreground color found
        if(isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= " style=\"color:" . $foreground_color . "\"";
        }
        // Add string and end coloring
        $colored_string .= ">" . $string . "</span>";

        return $colored_string;
    }

    public function getColoredString($string, $foreground_color = NULL, $background_color = NULL)
    {
        $colored_string = "";

        // Check if given foreground color found
        if(isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
        }
        // Check if given background color found
        if(isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
        }

        // Add string and end coloring
        $colored_string .= $string . "\033[0m";

        return $colored_string;
    }

    // Returns all foreground color names
    public function getForegroundColors()
    {
        return array_keys($this->foreground_colors);
    }

    // Returns all background color names
    public function getBackgroundColors()
    {
        return array_keys($this->background_colors);
    }
}


class ExecutionTime
{
    private $startTime;
    private $endTime;

    public function start()
    {
        $this->startTime = getrusage();
    }

    public function end()
    {
        $this->endTime = getrusage();
    }

    private function runTime($ru, $rus, $index)
    {
        return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000)) - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
    }

    public function __toString()
    {
        return "This process used " . $this->runTime($this->endTime, $this->startTime, "utime") . " ms for its computations\nIt spent " . $this->runTime($this->endTime, $this->startTime, "stime") . " ms in system calls\n";
    }
}


class escape
{

    private $string;

    public function string($string)
    {
        for($i = 0; $i < strlen($string); ++$i) {
            $char = $string[$i];
            $ord = ord($char);
            if($char !== "'" && $char !== "\"" && $char !== '\\' && $ord >= 32 && $ord <= 126) {
                $return .= $char;
            } else {
                $return .= '\\x' . dechex($ord);
            }
        }
        return $string;
    }


    public function mysql($string)
    {
        global $mysqli;

        $string = Escape::string($string);

        if(__USE_MSYQL__ == TRUE) {
            $string = mysqli_real_escape_string($mysqli, $string);
        }

        return $string;
    }


    public function clean($string)
    {

        $string = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $string);

        $string = str_replace("---", "--", $string);
        $string = str_replace("--", "-", $string);
        $string = str_replace("-;", "", $string);
        $string = str_replace(";;;", ";;", $string);
        $string = str_replace(";;", ";", $string);
        $string = str_replace("   ", "  ", $string);
        $string = str_replace("  ,", "", $string);
        $string = str_replace("(", "", $string);
        $string = str_replace(")", "", $string);

        $string = str_replace("  ", "", $string);
        $string = str_replace(", ,", ",,", $string);
        $string = str_replace(",;", ",", $string);
        $string = str_replace('" "', '""', $string);
        if(str_starts_with($string, ";")) {
            $string = ltrim($string, ';');
        }
        $string = trim($string);
        logger("string = '%s%'", $string);

        return $string;
    }
}

?>