<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Process;

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Database\VideoDb;
use Plex\Modules\Process\Traits\DbWrapper;
use Plex\Modules\Process\Traits\ExportSQL;
use Plex\Modules\Process\Traits\Favorites;
use Plex\Modules\Process\Traits\Playlist;
use Plex\Modules\Process\Traits\VideoPlayer;
use Plex\Template\Functions\Functions;
use UTMTemplate\HTML\Elements;
use Nette\Utils\FileSystem;
class Forms
{
    use DbWrapper;
    use ExportSQL;
    use Favorites;
    use Playlist;
    use VideoPlayer;

    public $postArray = [];
    public $getArray  = [];
    public $redirect  = ''; // .'/home.php';
    public $playlist_id;
    public $library;
    public $id;
    public $data;
    public $tagValue;

    public object $VideoInfo;
    public object $VideoMarker;
    public object $db;
    public object $playlist;
    public static $registeredCallbacks = false;
    private $registered_callbacks      = false;

    public function __construct($postArray)
    {
        if (true == self::$registeredCallbacks) {
            $this->registerCallback(self::$registeredCallbacks);
        }

        $this->db        = PlexSql::$DB;
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

    public function registerCallback($constant, $function = '')
    {
        if (\is_array($constant)) {
            foreach ($constant as $key => $value) {
                $this->registerCallback($key, $value);
            }
        } else {
            if (!\array_key_exists($constant, $this->registered_callbacks)) {
                $this->registered_callbacks = array_merge($this->registered_callbacks, [$constant => $function]);
            }
        }
    }

    public function process()
    {
        // foreach ($this->registered_callbacks as $pattern => $function) {
        //     if (!str_contains($pattern, '::')) {
        //         $pattern = 'self::'.$pattern;
        //         $class = $this;
        //     } else {
        //         $parts = explode('::', $pattern);
        //         // utminfo([$pattern,$parts,$function]);
        //         $class = (new $parts[0]());
        //         // $function = $parts[1];
        //     }
        // }

        $redirect           = false;
        $this->VideoInfo    = new Info($this->postArray);
        $this->VideoMarker  = new Marker($this->postArray);
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

        if (isset($this->postArray['exit'])) {
            if ('true' == $this->postArray['exit']) {
                exit;
            }
        }

        if (true === $redirect) {
            $this->myHeader($this->redirect);
        }
    }

    public function jquery()
    {
        $out = (new Functions())->getVideoPlaylistJson($this->postArray['id']);
        echo $out;
        // utminfo([$out,$this->postArray]);
        exit;
    }

    public function rating()
    {
        utminfo( 'fasd');
        [$_,$videoId] = explode('_', $this->postArray['id']);
        $rating       = $this->postArray['rating'];
        $this->VideoInfo->updateRating($videoId, $rating);
    }

    public function update_file()
    {
        $videoDb = new VideoDb();
        $file    = $videoDb->getVideoPath($this->postArray['id']);

        $mediatag = new Mediatag();
        $mediatag->addFile($file);
        $mediatag->refreshFile();
        utminfo($this->redirect);
        $this->myHeader($this->redirect.'&jo');

        exit;
        //        return $this->redirect;

        //      utmdd([__METHOD__,$this->postArray]);
    }

    public function updateVideoCard()
    {
        $video_key = VideoDb::getVideoKey($this->postArray['video_id']);
        $method    = $this->postArray['field'];
        $tagValue  = $this->postArray['value'];

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
    public function removelogs()
    {
        $logdir = __ERROR_LOG_DIRECTORY__.DIRECTORY_SEPARATOR.$this->postArray['logcat'];
        utminfo($logdir);
        FileSystem::delete($logdir);


    }
    public function playlist()
    {
        $url = $this->createPlaylist();
        echo $url;
        //  echo $this->myHeader($url);
        exit;
    }

    public function addMarker()
    {
        $url = $this->VideoMarker->addMarkerVideo();

        echo $url;
        //  echo $this->myHeader($url);
        exit;
    }

    public function getMarker()
    {
        $out = $this->VideoMarker->getMarkerVideos();

        echo $out;
        //  echo $this->myHeader($url);
        exit;
    }

    public function updateMarker()
    {
        $url = $this->VideoMarker->updateMarker();
        echo $url;
        //  echo $this->myHeader($url);
        exit;
    }

    public function deleteMarker()
    {
        $url = $this->VideoMarker->deleteMarker();
        echo $url;
        //  echo $this->myHeader($url);
        exit;
    }

    public function myHeader($redirect = '', $timeout = 0)
    {
        if ('' != $redirect) {
            $this->redirect = $redirect;
        }
        // utminfo($this->redirect);
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

                    $id    = $pcs[1];
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
        $db       = PlexSql::$DB;
        $__output = '';
        foreach ($data_array as $key => $val) {
            if (true == str_contains($key, '_')) {
                $value = trim($val);

                if ('' != $value) {
                    $pcs = explode('_', $key);

                    $id    = $pcs[1];
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
        $db       = PlexSql::$DB;
        $__output = '';

        foreach ($data_array as $key => $val) {
            if (true == str_contains($key, '_')) {
                $value = trim($val);

                if ('' != $value) {
                    $pcs = explode('_', $key);

                    $id    = $pcs[1];
                    $field = $pcs[0];
                    $set   = '`'.$field.'` = "'.$value.'"';

                    if ('null' == $value) {
                        $set = '`'.$field.'`= NULL ';
                    }

                    $sql = 'UPDATE '.Db_TABLE_STUDIOS.'  SET '.$set.' WHERE id = '.$id;
                    $db->query($sql);
                }
            }
        }

        if (false != $redirect) {
            return Elements::JavaRefresh($redirect, $timeout);
        }
    }
}
