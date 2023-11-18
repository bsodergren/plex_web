<?php
namespace Plexweb\Utilities;
/**
 * Command like Metatag writer for video files.
 */

use Nette\Utils\Arrays;
use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;


class Logger
{
    public static function getErrorLogs()
    {
        $err_array = [];

        if ($all = opendir(__ERROR_LOG_DIRECTORY__)) {
            while ($file = readdir($all)) {
                if (!is_dir(__ERROR_LOG_DIRECTORY__.'/'.$file)) {
                    if (preg_match('/(log)$/', $file)) {
                        $err_array[]  = filesystem::normalizePath(__ERROR_LOG_DIRECTORY__.'/'.$file);
                    } // end if
                } // end if
            } // end while
            closedir($all);
        } // end if

        return $err_array;
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
                    $s      =  $class.$row['function'].':'.$s;
                    $file   = $row['file'];
                    break;
            }
        }
        $file  = pathinfo($file, \PATHINFO_BASENAME);

        return $file.':'.$lineno.':'.$s;
    }

    public static function log($text, $var = '', $logfile = 'default.log')
    {
        //  if (Settings::isTrue('__SHOW_DEBUG_PANEL__')) {
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

        $html_string   = json_encode([
            'TIMESTAMP' => DateTime::from(null),
            'FUNCTION'  => $function_list,
            'MSG_TEXT'  => $text,
            'MSG_VALUE' => $html_var,
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
}

function logger($text, $var = '', $logfile = 'default.log')
{
    logger::log($text, $var, $logfile);
}

function getErrorLogs()
{
    return logger::getErrorLogs();
}
