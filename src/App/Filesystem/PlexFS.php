<?php
/**
 * plex web viewer
 */

namespace Plex\Filesystem;

/*
 * plex web viewer
 */

/*
 * plex web viewer.
 */

use Nette\Utils\FileSystem;

class PlexFS
{
    public static function chk_file($value, $command = 'delete', $options = '')
    {
        switch ($command) {
            case 'rename':
                if (is_file($value)) {
                    if (is_file($options)) {
                        self::chk_file($options, 'delete');
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

    public static function deleteFile($array)
    {
        global $db;
        $id        = $array['id'];

        $videoInfo = $db->where('id', $id)->getone(Db_TABLE_VIDEO_FILE);
        $file      = $videoInfo['fullpath'].\DIRECTORY_SEPARATOR.$videoInfo['filename'];
        $thumbnail = APP_HTML_ROOT.$videoInfo['thumbnail'];

        self::chk_file($file, 'delete');

        self::chk_file($thumbnail, 'delete');

        $res       = $db->where('id', $id)->delete(Db_TABLE_VIDEO_FILE);
        //    $res       =  $db->where('video_key', $videoInfo['video_key'])->delete(Db_TABLE_VIDEO_INFO);
        $res       = $db->where('playlist_video_id', $id)->delete(Db_TABLE_PLAYLIST_VIDEOS);

        // echo '<script type="text/javascript">   window.opener.location.reload(true); window.close(); </script>';
    }

    public static function get_filelist($directory, $ext = 'log')
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
                        $files_array[] = $file;
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
}
