<?php
/**
 * Command like Metatag writer for video files.
 */

use Nette\Utils\FileSystem;

class RoboLoader
{
    public $refresh = false;
    protected $conn;

    public function __construct($db_conn)
    {
        $this->conn = $db_conn;
    }

    public static function echo($value, $exit = 0)
    {
        echo '<pre>'.var_export($value, 1).'</pre>';

        if (1 == $exit) {
            exit;
        }
    }

    public static function javaRefresh($url, $timeout = 0)
    {
        global $_REQUEST;

        $html = '<script>'."\n";

        if ($timeout > 0) {
            $html .= 'setTimeout(function(){ ';
        }

        $html .= "window.location.href = '".$url."';";

        if ($timeout > 0) {
            $timeout = $timeout * 1000;
            $html .= '}, '.$timeout.');';
        }
        $html .= "\n".'</script>';

        echo $html;
    }

}
