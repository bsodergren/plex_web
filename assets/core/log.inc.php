<?php
    
use Monolog\Level;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

$dateFormat = "n/j,g:ia";

$output = "%datetime%>%level_name%>%message%\n";

// finally, create a formatter
$formatter = new LineFormatter($output, $dateFormat);
$stream = new StreamHandler(ERROR_LOG_FILE, Logger::INFO);
$stream->setFormatter($formatter);

$logger = new Logger('security');
$logger->pushHandler($stream);


function get_caller_info()
{
    $trace = debug_backtrace();

    $s='';
    $file=$trace[1]['file'];
    foreach ( $trace as $row)
    {
        switch ($row['function']) {
            case __FUNCTION__:break;
            case "logger":
                $lineno = $row["line"];
                break;
            case "require_once":break;
            case "include":break;
            default:
                $s =  $row['function'].":" .$s;
                $file=$row['file'];
        }
    }
    $file= pathinfo($file,PATHINFO_BASENAME);
    return $file.":".$s . $lineno .":";
}

function logger($msg, $var='') 
{
    global  $logger;
    global $colors;
    $function_list= get_caller_info();
    if( is_array($var) || is_object($var) )
    {
        $html_var=var_export($var,1);
        $var=$colors->getColoredString(var_export($var,1), "yellow");
    } else {
        $html_var=$var;
        $var=$colors->getColoredString($var, "yellow");
    }
    
    $html_func=$function_list;
    $func=$colors->getColoredString($function_list,"blue");
     
    $html_msg=$msg;
    $msg=$colors->getColoredString($msg, "red");
    
    $html_string = $html_func . ":" .$html_msg . " " . $html_var ;

    $string = $func . ":" .$msg . " " . $var ;
    $logger->INFO($string);
    if(__HTML_ERRORS__ == true) {
        display_log($html_string);
    }
}


function log_write_debug_data($process,$data=array(),$index='')
{

    if($index != '' ) {
        $index = "_".$index;
    }
    
	$text_data=var_export($data,true);
	file_write_file(__ERROR_LOG_DIR__."/".$process.$index.".txt",$text_data,'w',false);
    
}
