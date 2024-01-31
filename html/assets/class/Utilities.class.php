<?php
/**
 * plex web viewer
 */

use Nette\Utils\Arrays;
use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;

function matcharray($array, $string)
{
    if (!Arrays::contains($array, $string)) {
        return false;
    }

    return true;
}
class Colors
{
    private $foreground_colors = [];

    private $background_colors = [];

    private $fg_color;

    public function __construct()
    {
        // Set up shell colors
        $this->foreground_colors['black']        = '0;30';
        $this->foreground_colors['dark_gray']    = '1;30';
        $this->foreground_colors['blue']         = '0;34';
        $this->foreground_colors['light_blue']   = '1;34';
        $this->foreground_colors['green']        = '0;32';
        $this->foreground_colors['light_green']  = '1;32';
        $this->foreground_colors['cyan']         = '0;36';
        $this->foreground_colors['light_cyan']   = '1;36';
        $this->foreground_colors['red']          = '0;31';
        $this->foreground_colors['light_red']    = '1;31';
        $this->foreground_colors['purple']       = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown']        = '0;33';
        $this->foreground_colors['yellow']       = '1;33';
        $this->foreground_colors['light_gray']   = '0;37';
        $this->foreground_colors['white']        = '1;37';

        $this->background_colors['black']        = '40';
        $this->background_colors['red']          = '41';
        $this->background_colors['green']        = '42';
        $this->background_colors['yellow']       = '43';
        $this->background_colors['blue']         = '44';
        $this->background_colors['magenta']      = '45';
        $this->background_colors['cyan']         = '46';
        $this->background_colors['light_gray']   = '47';
    } // end __construct()

    public function getClassColor()
    {
        if (isset($this->foreground_colors[$this->fg_color])) {
            return 'color:'.$this->fg_color.';';
        }

        return '';
    }

    public function getColoredDiv($html, $background_color)
    {
        $class_tag = '';
        if (isset($this->background_colors[$background_color])) {
            $class_tag = 'class';
        }
    }

    // Returns colored string
    public function getColoredSpan($string, $foreground_color = null, $background_color = null)
    {
        $this->fg_color = $foreground_color;

        return '<span style="'.$this->getClassColor().'">'.$string.'</span>';
    } // end getColoredHTML()

    public function getColoredString($string, $foreground_color = null, $background_color = null)
    {
        $colored_string = '';

        // Check if given foreground color found
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[".$this->foreground_colors[$foreground_color].'m';
        }

        // Check if given background color found
        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[".$this->background_colors[$background_color].'m';
        }

        // Add string and end coloring
        $colored_string .= $string."\033[0m";

        return $colored_string;
    } // end getColoredString()

    // Returns all foreground color names
    public function getForegroundColors()
    {
        return array_keys($this->foreground_colors);
    } // end getForegroundColors()

    // Returns all background color names
    public function getBackgroundColors()
    {
        return array_keys($this->background_colors);
    } // end getBackgroundColors()
} // end class

class display
{
    private $model_popup   = '';
    private $model_buttons = [];
    public static $Random;

    public static function echo($text, $var = '')
    {
        /*
        if (defined('__DISPLAY_POPUP__')) {
            global $model_display;
            $model_display->model($text, $var);
            return 0;
        }
    */

        $pre_style = 'style="border: 1px solid #ddd;border-left: 3px solid #f36d33;color: #666;page-break-inside: avoid;font-family: monospace;font-size: 15px;line-height: 1.6;margin-bottom: 1.6em;max-width: 100%;overflow: auto;padding: 1em 1.5em;display: block;word-wrap: break-word;"';
        $div_style = 'style="display: inline-block;width: 100%;border: 1px solid #000;text-align: left;font-size:1.5rem;"';
        global $colors;
        $is_array  = false;

        if (is_array($text)) {
            $var  = $text;
            $text = 'Array';
        }

        if (is_array($var)) {
            $var      = var_export($var, 1);
            $var      = $colors->getColoredHTML($var, 'green');
            $var      = "<pre {$pre_style}>".$var.'</pre>';
            $is_array = true;
        } else {
            $var = $colors->getColoredHTML($var, 'green');
        }

        $text      = $colors->getColoredHTML($text);

        echo "<div {$div_style}>".$text.' '.$var."</div><br>\n";
    }

    public function model($text, $var = '')
    {
        $pre_style             = 'style="border: 1px solid #ddd;border-left: 3px solid #f36d33;color: #666;page-break-inside: avoid;font-family: monospace;font-size: 15px;line-height: 1.6;margin-bottom: 1.6em;max-width: 100%;overflow: auto;padding: 1em 1.5em;display: block;word-wrap: break-word;"';

        global $colors;

        $is_array              = false;

        if (is_array($text)) {
            $var  = $text;
            $text = 'Array';
        }

        if (is_array($var)) {
            $var      = var_export($var, 1);
            // $var=$colors->getColoredHTML($var, "green");
            $var      = "<pre {$pre_style}>".$var.'</pre>';
            $is_array = true;
        }

        // else {
        //    $var = $colors->getColoredHTML($var, "green");
        // }
        // $text=$colors->getColoredHTML($text);

        $random_id             = 'Model_'.substr(md5(rand()), 0, 7);
        $this->model_popup .= process_template('popup_debug_model', ['MODEL_TITLE' => $text, 'MODEL_BODY' => $var, 'MODEL_ID' => $random_id]);

        $button_html           = process_template('popup_debug_button', ['MODEL_TITLE' => $text, 'MODEL_ID' => $random_id]);
        $this->model_buttons[] = $button_html;
    }

    public function writeModelHtml()
    {
        if (defined('__DISPLAY_POPUP__')) {
            echo $this->model_popup;
            echo '      <div class="btn-group-vertical">';

            foreach ($this->model_buttons as $k => $html_button) {
                echo $html_button;
            }

            echo '</div>';
        }
    }
public static function RandomId($prefix='', $length = 10)
{
    return $prefix.substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

   public static function Random($length = 10) {
        self::$Random= self::RandomId('',$length);
    }
}

class ExecutionTime
{
    private $startTime;

    private $endTime;

    public function __toString()
    {
        return 'This process used '.$this->runTime($this->endTime, $this->startTime, 'utime')." ms for its computations\nIt spent ".$this->runTime($this->endTime, $this->startTime, 'stime')." ms in system calls\n";
    } // end __toString()

    public function start()
    {
        $this->startTime = getrusage();
    } // end start()

    public function end()
    {
        $this->endTime = getrusage();
    } // end end()

    private function runTime($ru, $rus, $index)
    {
        return ($ru["ru_{$index}.tv_sec"] * 1000 + (int) ($ru["ru_{$index}.tv_usec"] / 1000)) - ($rus["ru_{$index}.tv_sec"] * 1000 + (int) ($rus["ru_{$index}.tv_usec"] / 1000));
    } // end runTime()
} // end class

class escape
{
    private $string;

    public function string($string)
    {
        for ($i = 0; $i < strlen($string); ++$i) {
            $char = $string[$i];
            $ord  = ord($char);
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

class Logger
{
    public static function getErrorLogs()
    {
        $err_array = [];

        if ($all = opendir(__ERROR_LOG_DIRECTORY__)) {
            while ($file = readdir($all)) {
                if (!is_dir(__ERROR_LOG_DIRECTORY__.'/'.$file)) {
                    if (preg_match('/(log)$/', $file)) {
                        $err_array[] = filesystem::normalizePath(__ERROR_LOG_DIRECTORY__.'/'.$file);
                    } // end if
                } // end if
            } // end while
            closedir($all);
        } // end if

        return $err_array;
    }

    public static function log($text, $var = '', $logfile = 'default.log')
    {
        $colors        = new Colors();

        //  if (Settings::isTrue('__SHOW_DEBUG_PANEL__')) {
        if (!file_exists(__ERROR_LOG_DIRECTORY__)) {
            filesystem::createdir(__ERROR_LOG_DIRECTORY__, 0755);
        }

        $function_list = self::get_caller_info();
        $errorLogFile  = __ERROR_LOG_DIRECTORY__.'/'.$logfile;
        $html_var      = '';
        $html_string   = '';
        $html_msg      = '';
        $html_func     = '';

        if (is_array($var) || is_object($var)) {
            $html_var = self::printCode($var);
        } else {
            $html_var = $var;
        }
        // $html_var = htmlentities($html_var);
        // $html_var = '<pre>' . $html_var . '</pre>';

        // $html_string   = json_encode([
        //     'TIMESTAMP' => DateTime::from(null),
        //     'FUNCTION'  => $function_list,
        //     'MSG_TEXT'  => $text,
        //     'MSG_VALUE' => $html_var,
        // ]);

        $html_string   = implode('  ', [
            DateTime::from(null),
            $colors->getColoredString($function_list, 'red'),
            $colors->getColoredString($text, 'blue'),
            $html_var,
        ]);
        $r             = file_put_contents($errorLogFile, $html_string."\n", \FILE_APPEND);

        //  }

        //        $logger->INFO($log_string);
    }

    public static function printCode($array, $path = false, $top = true)
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
                    $data .= self::printCode($a, $path."['{$key}']", false);
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

    private static function get_caller_info()
    {
        $trace = debug_backtrace();

        $s     = '';
        $file  = $trace[2]['file'];
        foreach ($trace as $row) {
            $class = '';

            switch ($row['function']) {
                case __FUNCTION__:
                    break;

                case 'logger':
                    $lineno = $row['line'];

                    break;

                case 'log':
                    break;

                case 'require_once':
                    break;

                case 'include_once':
                    break;

                case 'require':
                    break;

                case 'include':
                    break;

                case '__construct':
                    break;

                case '__directory':
                    break;

                case '__filename':
                    break;

                default:
                    if ('' != $row['class']) {
                        $class = $row['class'].$row['type'];
                    }
                    $s      = $class.$row['function'].':'.$s;
                    $file   = $row['file'];

                    break;
            }
        }
        $file  = pathinfo($file, \PATHINFO_BASENAME);

        return $file.':'.$lineno.':'.$s;
    }
}

function logger($text, $var = '', $logfile = 'default.log')
{
    logger::log($text, $var, $logfile);
}

function getErrorLogs()
{
    return logger::getErrorLogs();
}
