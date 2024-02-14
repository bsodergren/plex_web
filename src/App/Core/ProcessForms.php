<?php

namespace Plex\Core;

/*
 * plex web viewer
 */

use Nette\Utils\Callback;

use Plex\Template\HTML\Elements;
use Symfony\Component\Process\Process;

class ProcessForms
{
    public $postArray = [];
    public $getArray = [];
    public $redirect = ''; // .'/home.php';
    public object $VideoInfo;

    public object $playlist;
    public object $db;

    public function __construct($postArray)
    {
        global $db;
        $this->db = $db;
        $this->VideoInfo = new VideoInfo();
        $this->postArray = $postArray;
        $this->playlist = new Playlist($this->postArray);
        $this->redirect = $_SERVER['HTTP_REFERER'];
        if (isset($postArray['redirect_url'])) {
            $this->redirect = $postArray['redirect_url'];
        }
            

        if (isset($postArray['submit'])) {
            $method = $this->postArray['submit'];

            unset($this->postArray['submit']);
            if (method_exists($this, $method)) {
                $this->{$method}();
            } else {
                dd('No Method for '.$method.' Found');
            }
            $this->myHeader();
        }
        if (isset($this->postArray['action'])) {
            $method = $this->postArray['action'];
            if (str_contains($method, 'Playlist')) {
                if (method_exists(\get_class($this->playlist), $method)) {
                    $this->playlist->{$method}();
                } else {
                    dd('No Method for '.$method.' Found in playlist');
                }
            } else {
                if (method_exists($this, $method)) {
                    $this->{$method}();
                } else {
                    dd('No Method for '.$method.' Found in this');
                }
            }
            $this->myHeader();
        }
        // dd($_SERVER);
    }

    // public function playliststuff()
    // {
    //     if (isset($this->postArray['action'])) {
    //         $action = $this->postArray['action'];
    //     }
    //     $playlist_id = $this->postArray['playlist_id'];

    // }

    public function rating()
    {
        [$_,$videoId] = explode('_', $this->postArray['id']);
        $rating = $this->postArray['rating'];
        $this->VideoInfo->updateRating($videoId, $rating);
        //        dd($this->postArray);
    }

    public function update_file()
    {
        dd($this->postArray);
    }

    public function update()
    {
        $keys = array_keys($this->postArray);
        $method = $keys[0];
        $tagValue = $this->postArray[$method];
        $video_key = $this->postArray[$keys[1]];
        // dump([__METHOD__,$video_key,$tagValue,$method,$keys]);
        // dump(['update', $method, $video_key]);
        $this->VideoInfo->{$method}($tagValue, $video_key);
    }

    public function ProcessOutput($type, $buffer)
    {
        $buffer = str_replace('\n\n', '\n', $buffer);
        echo Template::put($buffer);
    }

    public function ProcessProgressBar($type, $buffer)
    {
        $timeout = $buffer / 60;
        echo Template::ProgressBar($timeout, 'UpdateBar');
    }

    public function refresh()
    {
        $this->myHeader('home.php', 0);

        return 0;
        $callback = Callback::check([$this, 'ProcessProgressBar']);

        $mediaupdate = '/home/bjorn/scripts/Mediatag/bin/mediaupdate';
        $mediadb = '/home/bjorn/scripts/Mediatag/bin/mediadb';
        $path = __PLEX_LIBRARY__.\DIRECTORY_SEPARATOR.$_SESSION['library'];

        $process = new Process([$mediaupdate, '--path', $path, '-q']);
        // dump( $process->getCommandLine());
        $process->setTimeout(60000);
        $process->start();
        $process->wait($callback);
        unset($process);
        $callback = Callback::check([$this, 'ProcessOutput']);

        $process = new Process([$mediadb, '--path', $path]);
        $process->setTimeout(60000);
        // dump( $process->getCommandLine());
        $process->start();
        $process->wait($callback);
        unset($process);
        $process = new Process([$mediadb, '--path', $path, '-tDi']);
        $process->setTimeout(60000);
        // dump( $process->getCommandLine());
        $process->start();
        $process->wait($callback);
        unset($process);

        $process = new Process([$mediadb, '--path', $path, '-u']);
        $process->setTimeout(60000);
        // dump( $process->getCommandLine());
        $process->start();
        $process->wait($callback);
        Template::ProgressBar(5);
        //  dd($_SESSION['library']);
        $this->myHeader('home.php', 5);

        return 0;
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
        self::deleteFile($this->postArray);
    }

    public function playlist()
    {
        $url = $this->playlist->createPlaylist();
        echo $url;
        //  echo $this->myHeader($url);
        exit;
    }
    public function RemovePlaylistVideo()
    {
        $url = $this->playlist->RemovePlaylistVideo();
        echo $url;
        //  echo $this->myHeader($url);
        exit;
    }
    public function myHeader($redirect = '', $timeout = 0)
    {
        if ('' != $redirect) {
            $this->redirect = $redirect;
        }
        echo Elements::javaRefresh($this->redirect, $timeout);

        exit;
    } // end myHeader()

    public static function StaticGenreConfigSave($data_array, $redirect, $timeout = 0)
    {
        global $db;

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
        global $db;

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
        global $db;

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
