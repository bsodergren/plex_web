<?php
/**
 * plex web viewer
 */

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
    $id        = $array['id'];

    $videoInfo = $db->where('id', $id)->getone(Db_TABLE_VIDEO_FILE);
    $file      = $videoInfo['fullpath'].\DIRECTORY_SEPARATOR.$videoInfo['filename'];
    $thumbnail = APP_HTML_ROOT.$videoInfo['thumbnail'];

    chk_file($file, 'delete');

    chk_file($thumbnail, 'delete');

    $res       = $db->where('id', $id)->delete(Db_TABLE_VIDEO_FILE);
    //    $res       =  $db->where('video_key', $videoInfo['video_key'])->delete(Db_TABLE_VIDEO_INFO);
    $res       = $db->where('playlist_video_id', $id)->delete(Db_TABLE_PLAYLIST_VIDEOS);

    // echo '<script type="text/javascript">   window.opener.location.reload(true); window.close(); </script>';
}
