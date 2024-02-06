<?php
namespace Plex\Core;
/**
 * plex web viewer
 */

use Nette\Utils\FileSystem;
use Nette\Loaders\RobotLoader;

class RoboLoader extends RobotLoader
{
    public $refresh = false;
    protected $conn;

    public function __construct()
    {
    }

    public static function echo($value, $exit = 0)
    {
        echo '<pre>'.var_export($value, 1).'</pre>';

        if (1 == $exit) {
            exit;
        }
    }

    public static function get_filelist($directory, $ext = 'log', $skip_files = 0)
    {
        $files_array = [];
        if ($all = opendir($directory)) {
            while ($filename = readdir($all)) {
                if ('.' == $filename) {
                    continue;
                }
                if ('..' == $filename) {
                    continue;
                }
                $file = Filesystem::normalizePath($directory.'/'.$filename);
                if (!is_dir($file)) {
                    if (preg_match('/('.$ext.')$/', $filename)) {
                        if (1 == $skip_files) {
                            if (!self::skipFile($file)) {
                                $files_array[] = $file;
                            }
                        } else {
                            $files_array[] = $file;
                        }
                    } // end if
                } else {
                    $files_array = array_merge($files_array, self::get_filelist($file, $ext, $skip_files));
                }

                // end if
            } // end while
            closedir($all);
        } // end if
        sort($files_array);

        return $files_array;
    }

    public static function skipFile($filename)
    {
        $f    = fopen($filename, 'r');
        $line = fgets($f);
        fclose($f);

        return strpos($line, '#skip');
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
            $timeout *= 1000;
            $html .= '}, '.$timeout.');';
        }
        $html .= "\n".'</script>';
        logger("Looking for redirect", $html);

        echo $html;
    }

    public static function setSkipFile($filename)
    {
        if (!self::skipFile($filename)) {
            $replacement = '<?php';
            $replacement .= ' #skip';
            $__db_string = FileSystem::read($filename);
            $__db_write  = str_replace('<?php', $replacement, $__db_string);
            FileSystem::write($filename, $__db_write);
        }
    }
}
