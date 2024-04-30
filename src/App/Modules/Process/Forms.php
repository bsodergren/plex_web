<?php

namespace Plex\Modules\Process;

/*
 * plex web viewer
 */

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Database\VideoDb;
use Plex\Modules\Process\Traits\DbWrapper;
use Plex\Modules\Process\Traits\Favorites;
use Plex\Modules\Process\Traits\Mediatag;
use Plex\Modules\Process\Traits\Playlist;
use Plex\Modules\Process\Traits\VideoPlayer;
use Plex\Template\Functions\Functions;
use UTMTemplate\HTML\Elements;

class Forms
{
    use DbWrapper;
    use Favorites;
    use Playlist;
    use VideoPlayer;

    public $postArray = [];
    public $getArray = [];
    public $redirect = ''; // .'/home.php';
    public $playlist_id;
    public $library;
    public $id;
    public $data;
    public $tagValue;

    public object $VideoInfo;
    public object $VideoChapter;
    public object $db;
    public object $playlist;

    public function __construct($postArray)
    {
        $this->db = PlexSql::$DB;
        $this->postArray = $postArray;

        $this->redirect = $_SERVER['HTTP_REFERER'];

        if (isset($this->postArray['redirect_url'])) {
            $this->redirect = $this->postArray['redirect_url'];
        }

        if (isset($this->postArray['playlist_id'])) {
            $this->playlist_id = $this->postArray['playlist_id'];
        }
        if (isset($this->postArray['id'])) {
            $this->id = $this->postArray['id'];
        }
        $this->library = $_SESSION['library'];
    }

    public function process()
    {
        $redirect = false;
        $this->VideoInfo = new Info($this->postArray);
        $this->VideoChapter = new Chapter($this->postArray);

        if (isset($this->postArray['submit'])) {
            $method = $this->postArray['submit'];
            unset($this->postArray['submit']);
            if (method_exists($this, $method)) {
                $this->{$method}();
            } else {
                utmdd('No Method for '.$method.' Found');
            }
            $redirect = true;
        }
        if (isset($this->postArray['action'])) {
            $method = $this->postArray['action'];
            if (str_contains($method, 'Playlist')) {
                if (method_exists($this, $method)) {
                    $this->{$method}();
                } else {
                    utmdd('No Method for '.$method.' Found in playlist');
                }
            } else {
                if (method_exists($this, $method)) {
                    $this->{$method}();
                } else {
                    utmdd('No Method for '.$method.' Found in this');
                }
            }
            $redirect = true;
        }
        if (true === $redirect) {
            $this->myHeader($this->redirect);
        }
    }

    public function jquery()
    {
        $out = (new Functions())->getVideoPlaylistJson($this->postArray['id']);
        echo $out;
        // utmdump([$out,$this->postArray]);
        exit;
    }

    public function rating()
    {
        [$_,$videoId] = explode('_', $this->postArray['id']);
        $rating = $this->postArray['rating'];
        $this->VideoInfo->updateRating($videoId, $rating);
    }

    public function update_file()
    {
        $videoDb = new VideoDb();
        $file = $videoDb->getVideoPath($this->postArray['id']);

        $mediatag = new Mediatag();
        $mediatag->addFile($file);
        $mediatag->refreshFile();
        $this->myHeader($this->redirect.'&jo');

        exit;
        //        return $this->redirect;

        //      utmdd([__METHOD__,$this->postArray]);
    }

    public function updateVideoCard()
    {
        $video_key = VideoDB::getVideoKey($this->postArray['video_id']);
        $method = $this->postArray['field'];
        $tagValue = $this->postArray['value'];

        $this->VideoInfo->{$method}($tagValue, $video_key);
        exit;

    }

    public function GenreConfigSave()
    {
        return self::StaticGenreConfigSave($this->postArray, $this->redirect);
    }

    public function ArtistConfigSave()
    {
        return self::StaticArtistConfigSave($this->postArray, $this->redirect);
    }

    public function StudioConfigSave()
    {
        return self::StaticsaveStudioConfig($this->postArray, $this->redirect);
    }

    public function delete_file()
    {
        $this->VideoInfo->deleteFile();
    }

    public function playlist()
    {
        $url = $this->createPlaylist();
        echo $url;
        //  echo $this->myHeader($url);
        exit;
    }

    public function addChapterVideo()
    {
        $url = $this->VideoChapter->addChapterVideo();
        echo $url;
        //  echo $this->myHeader($url);
        exit;
    }

    public function updateChapter()
    {
        $url = $this->VideoChapter->updateChapter();
        echo $url;
        //  echo $this->myHeader($url);
        exit;
    }

    public function myHeader($redirect = '', $timeout = 0)
    {
        if ('' != $redirect) {
            $this->redirect = $redirect;
        }
        // UtmDump($this->redirect);
        echo Elements::javaRefresh($this->redirect, $timeout);

        exit;
    } // end myHeader()

    public static function StaticGenreConfigSave($data_array, $redirect, $timeout = 0)
    {
        $db = PlexSql::$DB;

        $__output = '';

        foreach ($data_array as $key => $val) {
            if (true == str_contains($key, '_')) {
                $value = trim($val);

                if ('' != $value) {
                    $pcs = explode('_', $key);

                    $id = $pcs[1];
                    $field = $pcs[0];
                    if ('null' == $value) {
                        $set = '`'.$field.'`= NULL ';
                    } else {
                        if ('keep' != $field) {
                            $value = '"'.$value.'"';
                        }

                        $set = '`'.$field.'` = '.$value;
                    }

                    $sql = 'UPDATE '.Db_TABLE_GENRE.'  SET '.$set.' WHERE id = '.$id;
                    $db->query($sql);
                }
            }
        }
        if (false != $redirect) {
            return Elements::JavaRefresh($redirect, $timeout);
        }
    }

    public static function StaticArtistConfigSave($data_array, $redirect, $timeout = 0)
    {
        $db = PlexSql::$DB;
        $__output = '';
        foreach ($data_array as $key => $val) {
            if (true == str_contains($key, '_')) {
                $value = trim($val);

                if ('' != $value) {
                    $pcs = explode('_', $key);

                    $id = $pcs[1];
                    $field = $pcs[0];
                    if ('null' == $value) {
                        $set = '`'.$field.'`= NULL ';
                    } else {
                        if ('hide' != $field) {
                            $value = '"'.$value.'"';
                        }

                        $set = '`'.$field.'` = '.$value;
                    }

                    $sql = 'UPDATE '.Db_TABLE_ARTISTS.'  SET '.$set.' WHERE id = '.$id;
                    $db->query($sql);
                }
            }
        }
        if (false != $redirect) {
            return Elements::JavaRefresh($redirect, $timeout);
        }
    }

    public static function StaticsaveStudioConfig($data_array, $redirect, $timeout = 0)
    {
        $db = PlexSql::$DB;
        $__output = '';

        foreach ($data_array as $key => $val) {
            if (true == str_contains($key, '_')) {
                $value = trim($val);

                if ('' != $value) {
                    $pcs = explode('_', $key);

                    $id = $pcs[1];
                    $field = $pcs[0];
                    $set = '`'.$field.'` = "'.$value.'"';

                    if ('null' == $value) {
                        $set = '`'.$field.'`= NULL ';
                    }

                    $sql = 'UPDATE '.Db_TABLE_STUDIO.'  SET '.$set.' WHERE id = '.$id;
                    $db->query($sql);
                }
            }
        }

        if (false != $redirect) {
            return Elements::JavaRefresh($redirect, $timeout);
        }
    }
}
