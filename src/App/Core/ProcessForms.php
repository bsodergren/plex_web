<?php
namespace Plex\Core;
/**
 * plex web viewer
 */

use Plex\Core\VideoInfo;
use Nette\Utils\Callback;
use Plex\Template\Template;
use Symfony\Component\Process\Process;

class ProcessForms
{
    public $postArray = [];
    public $getArray  = [];
    public $redirect  = ''; // .'/home.php';
    public object $VideoInfo;

    public object $playlist;
    public object $db;

    public function __construct($postArray)
    {
        global $db;
        $this->db        = $db;
        $this->VideoInfo = new VideoInfo();
        $this->postArray = $postArray;
        $this->playlist  = new Playlist($this->postArray);
        $this->redirect  = $_SERVER['HTTP_REFERER'];

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
        }
        if (isset($this->postArray['action'])) {
            $method = $this->postArray['action'];
            if (str_contains($method, 'Playlist')) {
                if (method_exists(get_class($this->playlist), $method)) {
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
        }
        // dd($_SERVER);
        $this->myHeader();
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
        $keys      = array_keys($this->postArray);
        $method    = $keys[0];
        $tagValue  = $this->postArray[$method];
        $video_key = $this->postArray[$keys[1]];
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
        $callback    = Callback::check([$this, 'ProcessProgressBar']);

        $mediaupdate = '/home/bjorn/scripts/Mediatag/bin/mediaupdate';
        $mediadb     = '/home/bjorn/scripts/Mediatag/bin/mediadb';
        $path        = __PLEX_LIBRARY__.\DIRECTORY_SEPARATOR.$_SESSION['library'];

        $process     = new Process([$mediaupdate, '--path', $path, '-q']);
        $process->setTimeout(60000);
        $process->start();
        $process->wait($callback);
        unset($process);
        $callback    = Callback::check([$this, 'ProcessOutput']);

        $process     = new Process([$mediadb, '--path', $path]);
        $process->setTimeout(60000);
        $process->start();
        $process->wait($callback);
        unset($process);
        $process     = new Process([$mediadb, '--path', $path, '-tDi']);
        $process->setTimeout(60000);
        $process->start();
        $process->wait($callback);
        unset($process);

        $process     = new Process([$mediadb, '--path', $path, '-u']);
        $process->setTimeout(60000);
        $process->start();
        $process->wait($callback);
        Template::ProgressBar(5);
        //  dd($_SESSION['library']);
        $this->myHeader('home.php', 5);

        return 0;
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
    }

    public function myHeader($redirect = '', $timeout = 0)
    {
        if ('' != $redirect) {
            $this->redirect = $redirect;
        }
        echo JavaRefresh($this->redirect, $timeout);

        exit;
    } // end myHeader()
}


/*
function proccess_settings($redirect_url = '')
{
    global $form;
    global $_POST;
    global $db;

    // get our form values and assign them to a variable
    foreach ($_POST as $key => $value) {
        switch (true) {
            case 'submit' == $key:
                break;

            case str_contains($key, 'setting_'):
                $pcs                   = explode('_', $key);
                $field                 = $pcs[1];
                $new_settiings[$field] = $value;

                break;

            case str_contains($key, '-NAME'):
                break;

            case array_key_exists($key, __SETTINGS__):
                $data                  = ['value' => $value];
                $db->where('name', $key);
                $db->update(Db_TABLE_SETTINGS, $data);

                break;

            case str_contains($key, '-ADD'):
                if (!array_key_exists(str_replace('-ADD', '', $key), __SETTINGS__)) {
                    if (!array_key_exists(str_replace('-NAME', '', $key), __SETTINGS__)) {
                        $key_name = str_replace('-ADD', '-NAME', $key);
                        if (array_key_exists($key_name, $_POST)) {
                            $value                     = $_POST[$key_name];
                            $field                     = str_replace('-NAME', '', $key_name);
                            $transfer_settings[$field] = [
                                'value' => $value,
                                'type'  => 'text',
                            ];
                        }
                    }
                }

                break;
        } // end switch
    } // end foreach

    if (is_array($transfer_settings)) {
        foreach ($transfer_settings as $name => $arr) {
            $id = $db->insert(Db_TABLE_SETTINGS, ['name' => $name, 'value' => $arr['value'], 'type' => $arr['type']]);
        }
    }

    if (is_array($new_settiings)) {
        if ('' != $new_settiings['name']) {
            $id = $db->insert(Db_TABLE_SETTINGS, $new_settiings);
        }
    }

    $form->printr($db->getLastError());
    // show a success message if no errors
    if ($form->ok()) {
        return $form->redirect($redirect_url);
    }
} // end proccess_settings()



// function process_form($redirect_url = '')
// {
//     global $_POST,$_REQUEST;

//     if (isset($_POST['submit'])) {
//         if ('GenreConfigSave' == $_POST['submit']) {
//             return GenreConfigSave($_POST, $redirect_url);

//             exit;
//         }
//         if ('ArtistConfigSave' == $_POST['submit']) {
//             return ArtistConfigSave($_POST, $redirect_url);

//             exit;
//         }

//         if ('StudioConfigSave' == $_POST['submit']) {
//             return saveStudioConfig($_POST, $redirect_url);

//             exit;
//         }
//         if ('delete_file' == $_POST['submit']) {
//             return deleteFile($_POST);

//             exit;
//         }

//         if (str_starts_with($_POST['submit'], 'Playlist')) {
//             createPlaylist($_POST, $redirect_url);
//             myHeader();

//             exit;
//         }
//         if (str_starts_with($_POST['submit'], 'All Files')) {
//             createPlaylist($_POST, $redirect_url);
//             myHeader();

//             exit;
//         }
//         if (str_starts_with($_POST['submit'], 'Move')) {
//             $playlist_id = createPlaylist($_POST, $redirect_url);
//             moveFiles($_POST, $playlist_id);

//             exit;
//         }
//     } // end if

//     if ('' != $redirect_url) {
//         return myHeader($redirect_url, 0);
//     }
// } // end process_form()
*/