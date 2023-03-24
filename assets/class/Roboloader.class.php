<?php

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

        echo '<pre>' . var_export($value, 1) . '</pre>';

        if ($exit == 1) {
            exit;
        }
    }

    public static function get_filelist($directory, $ext = 'log', $skip_files = 0)
    {
        $files_array = [];
        if ($all = opendir($directory)) {
            while ($filename = readdir($all)) {
                if (!is_dir($directory . '/' . $filename)) {
                    if (preg_match('/(' . $ext . ')$/', $filename)) {
                        $file = filesystem::normalizePath($directory . '/' . $filename);

                        if ($skip_files == 1) {
                            if (!self::skipFile($file)) {
                                $files_array[] = $file;
                            }
                        } else {
                            $files_array[] = $file;
                        }
                    } //end if
                } //end if
            } //end while
            closedir($all);
        } //end if
        sort($files_array);
        return $files_array;
    }

    public static function skipFile($filename)
    {
        $f = fopen($filename, 'r');
        $line = fgets($f);
        fclose($f);
        return strpos($line, '#skip');
    }

    public static function javaRefresh($url, $timeout = 0)
    {
        global $_REQUEST;

        $html = '<script>' . "\n";


        if ($timeout > 0) {
            $html .= 'setTimeout(function(){ ';
        }

        $html .= "window.location.href = '" . $url . "';";

        if ($timeout > 0) {
            $timeout = $timeout * 1000;
            $html .= '}, ' . $timeout . ');';
        }
        $html .= "\n" . '</script>';

        echo $html;
    }

    public static function setSkipFile($filename)
    {

        if (!self::skipFile($filename)) {
            $replacement = '<?php';
            $replacement .= ' #skip';
            $__db_string = FileSystem::read($filename);
            $__db_write = str_replace('<?php', $replacement, $__db_string);
            FileSystem::write($filename, $__db_write);
        }
    }


}

