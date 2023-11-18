<?php
namespace Plexweb\Core;

use Plexweb\Modules\Playlist;
use Plexweb\Modules\VideoInfo;
/**
 * plex web viewer
 */

/**
 * plex web viewer.
 */
class ProcessForms
{
    public $postArray = [];
    public $getArray  = [];
    public $redirect  =  __URL_PATH__.'/home.php';
    public object $VideoInfo;
    
    public object $playlist;
    public object $db;

    public function __construct($postArray)
    {
        global $db;
        $this->db        = $db;
        $this->VideoInfo = new VideoInfo();
        $this->postArray = $postArray;
        $this->playlist = new Playlist($this->postArray);

        if (isset($postArray['redirect_url'])) {
            $this->redirect  = $postArray['redirect_url'];
        }
     //   dump(['Process Class', $postArray]);

        if (isset($postArray['submit'])) {
            $method = $this->postArray['submit'];
            //  unset($this->postArray['submit']);
            if (method_exists($this, $method)) {
                $this->{$method}();
            } else {
                dd('No Method for '.$method.' Found');
            }
            
        }
        if (isset($this->postArray['action'])) {
            $method = $this->postArray['action'].'Playlist';
            if(method_exists(get_class($this->playlist),$method)){
                $this->playlist->$method();
            } else {
                dd($method,$this->playlist->data);
            }
        }

    }

    // public function playliststuff()
    // {
    //     if (isset($this->postArray['action'])) {
    //         $action = $this->postArray['action'];
    //     }
    //     $playlist_id = $this->postArray['playlist_id'];

    // }

    public function update_file()
    {
        dd($this->postArray);
    }

    public function update()
    {
        $keys   = array_keys($this->postArray);
        $method = $keys[0];
        $this->VideoInfo->{$method}($this->postArray[$method]);
    }

    public function GenreConfigSave()
    {
        return GenreConfigSave($this->postArray, $this->redirect);
    }

    public function ArtistConfigSave()
    {
        return ArtistConfigSave($this->postArray, $this->redirect);
    }

    public function StudioConfigSave()
    {
        return saveStudioConfig($this->postArray, $this->redirect);
    }

    public function delete_file()
    {
        return deleteFile($this->postArray);
    }

    public function playlist()
    {
        echo $this->playlist->createPlaylist();
        
//        
    }
    public function  myHeader($redirect='',$timeout=0)
    {   

        if($redirect != ''){
            $this->redirect = $redirect;
        }
        echo JavaRefresh($this->redirect, $timeout);
        
        die();
    } // end myHeader()

}
