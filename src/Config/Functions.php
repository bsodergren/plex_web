<?php

use Plex\Core\Request;
use Nette\Utils\FileSystem;
use Plex\Core\Utilities\Logger;


/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\VarDumper\Caster\ScalarStub;
use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('MediaDump')) {
    /**
     * @author Nicolas Grekas <p@tchwork.com>
     * @author Alexandre Daubois <alex.daubois@gmail.com>
     */
    function MediaDump(mixed ...$vars): mixed
    {
        echo "fasdfdsa";
        if (!$vars) {
            VarDumper::dump(new ScalarStub('🐛'));

            return null;
        }

        if (array_key_exists(0, $vars) && 1 === count($vars)) {
            VarDumper::dump($vars[0]);
            $k = 0;
        } else {
            foreach ($vars as $k => $v) {
                VarDumper::dump($v, is_int($k) ? 1 + $k : $k);
            }
        }

        if (1 < count($vars)) {
            return $vars;
        }

        return $vars[$k];
    }
}
if (!function_exists('dd')) {
    function dd(mixed ...$vars): never
    {
        if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) && !headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        if (array_key_exists(0, $vars) && 1 === count($vars)) {
            VarDumper::dump($vars[0]);
        } else {
            foreach ($vars as $k => $v) {
                VarDumper::dump($v, is_int($k) ? 1 + $k : $k);
            }
        }

        exit(1);
    }
}

function chk_file($value, $command = 'delete', $options = '')
{
    switch ($command) {
        case 'rename':
            if (is_file($value)) {
                if (is_file($options)) {
                    chk_file($options, 'delete');
                }

                logger("Renaming {$value} to {$options}");
                rename($value, $options);
            }

            break;

        case 'delete':
            if (is_file($value)) {
                logger("deleting {$value}");
                $e = unlink($value);
                // dd($e);
            }

            break;
    }
}// end chk_file()

function deleteFile($array)
{
    global $db;
    $id = $array['id'];

    $videoInfo = $db->where('id', $id)->getone(Db_TABLE_VIDEO_FILE);
    $file = $videoInfo['fullpath'].\DIRECTORY_SEPARATOR.$videoInfo['filename'];
    $thumbnail = APP_HTML_ROOT.$videoInfo['thumbnail'];

    // chk_file($file, 'delete');
    rename_file($file);
    chk_file($thumbnail, 'delete');

    $res = $db->where('id', $id)->delete(Db_TABLE_VIDEO_FILE);
    //    $res       =  $db->where('video_key', $videoInfo['video_key'])->delete(Db_TABLE_VIDEO_INFO);
    $res = $db->where('playlist_video_id', $id)->delete(Db_TABLE_PLAYLIST_VIDEOS);

    // echo '<script type="text/javascript">   window.opener.location.reload(true); window.close(); </script>';
}

function rename_file($file)
{
    $newFile = str_replace('XXX'.\DIRECTORY_SEPARATOR, 'XXX'.\DIRECTORY_SEPARATOR.'Backup'.\DIRECTORY_SEPARATOR, $file);
    FileSystem::rename($file, $newFile, true);
}

function logger($text, $var = '', $logfile = 'default.log')
{
    Logger::log($text, $var, $logfile);
}

function getErrorLogs()
{
    return Logger::getErrorLogs();
}
function urlQuerystring($input_string, $exclude = '', $query = false)
{
    return Request::urlQuerystring($input_string, $exclude, $query);
}
function uri_String($request_array, $start = '?')
{
    return Request::uri_String($request_array, $start);
}
function uri_SQLQuery($request_array)
{
    return Request::uri_SQLQuery($request_array);
}
