<?php

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

//use Monolog\Handler\FirePHPHandler;
//use Monolog\Handler\RotatingFileHandler;

$dateFormat = "n/j,g:ia";

$output = "%datetime%>%level_name%>%message%\n";
// finally, create a formatter
$formatter = new LineFormatter($output, $dateFormat);
//$stream = new RotatingFileHandler (ERROR_LOG_FILE,3, Logger::INFO);

$stream = new StreamHandler(ERROR_LOG_FILE, Logger::INFO);
$stream->setFormatter($formatter);

$logger = new Logger('security');
$logger->pushHandler($stream);


function get_caller_info()
{
    $trace = debug_backtrace();

    $s = '';
    $file = $trace[1]['file'];
    foreach($trace as $row) {
        switch($row['function']) {
            case __FUNCTION__:
                break;
            case "logger":
                $lineno = $row["line"];
                break;
            case "require_once":
                break;
            case "include":
                break;
            default:
                $s = $row['function'] . ":" . $s;
                $file = $row['file'];
        }
    }
    $file = pathinfo($file, PATHINFO_BASENAME);
    return $file . ":" . $s . $lineno . ":";
}

function logger($msg, $var = '')
{
    global $logger;
    global $colors;
    $function_list = get_caller_info();
    $html_var = '';
    $log_var = '';

    $html_var_string=$var;
    if(is_array($var) || is_object($var)) {
         $html_var = printCode($var);
        $html_var_string = str_replace("<br>", "</span><br><span style=\"margin-left: 40px\">", $html_var);
        $var=var_export($var,1);

    }

    if($var != '') {

       // $html_var_string = wordwrap($html_var, 80, "<br>");
        $html_var = $colors->getColoredHTML("<span style=\"margin-left: 40px\">\n" . $html_var_string . "\n</span>\n", "green");
        $log_var = $colors->getColoredString($var, "green");
    }


    $html_func = $colors->getColoredHTML($function_list, "blue");
    $log_func = $colors->getColoredString($function_list, "blue");

    $html_msg = $colors->getColoredHTML("<span style=\"margin-left: 40px\">".$msg."</span>", "red");
    $log_msg = $colors->getColoredString($msg, "red");

    $html_string = $html_func . "<br>" . $html_msg . "<br>" . $html_var . "<br>";

    $log_string = $log_func . ":" . $log_msg . " " . $log_var;

    if(__HTML_POPUP__ == TRUE) {
        $logger->INFO($html_string);
    } else {
        $logger->INFO($log_string);
    }
}


function log_write_debug_data($process, $data = array(), $index = '')
{

    if($index != '') {
        $index = "_" . $index;
    }

    $text_data = var_export($data, TRUE);
    file_write_file(__ERROR_LOG_DIR__ . "/" . $process . $index . ".txt", $text_data, 'w', FALSE);

}
