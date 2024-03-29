<?php

namespace Plex\Core;

/*
 * plex web viewer
 */

use Nette\Loaders\RobotLoader;
use Nette\Utils\FileSystem;
use Symfony\Component\Yaml\Yaml;

class RoboLoader extends RobotLoader
{
    public $refresh = false;
    protected $conn;

    public function __construct() {}

    public static function loadPage()
    {
        $page_config = Yaml::parseFile(__PAGE_CONFIG__, Yaml::PARSE_CONSTANT);
        $current_page = [];
        if (\array_key_exists(__THIS_PAGE__, $page_config)) {
            $current_page = $page_config[__THIS_PAGE__];
        }
        $default = $page_config['default'];
        foreach ($default as $key => $value) {
            if (\array_key_exists($key, $current_page)) {
                $page_setting = $current_page[$key];
                $defaults[__THIS_PAGE__]['Config'][$key] = $current_page[$key];
            } else {
                $page_setting = $default[$key];
                $defaults[__THIS_PAGE__]['default'][$key] = $default[$key];
            }
            \define($key, $page_setting);
        }
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
        $f = fopen($filename, 'r');
        $line = fgets($f);
        fclose($f);

        return strpos($line, '#skip');
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
