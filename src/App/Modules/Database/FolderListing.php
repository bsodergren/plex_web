<?php

namespace Plex\Modules\Database;

use Plex\Core\Request;
use Plex\Modules\Database\Traits\FolderFunc;
use Plex\Modules\Database\Traits\VideoLookup;

class FolderListing
{

    use FolderFunc;
    use VideoLookup;
    
    public object $db;
    public $currentpage;
    public $request;
    public $urlPattern;
    public static $searchId;
    public object $ReqObj;
    public $exclude_items = ['*.sh','*.mov','*.m2ts'];
    public $show_hidden_files = false;

    public function __construct(Request $ReqObj)
    {
        \defined('FM_EXCLUDE_ITEMS') || \define('FM_EXCLUDE_ITEMS',
            version_compare(\PHP_VERSION, '7.0.0', '<') ? serialize($this->exclude_items) : $this->exclude_items);

        $p = $_GET['p'] ?? ($_POST['p'] ?? '');
        // clean path
        $p = $this->fm_clean_path($p);
        // instead globals vars
        $currentDir[] = __PLEX_LIBRARY__;

        if (null !== PlexSql::getLibrary()) {
            $currentDir[] = $_SESSION['library'];
        }
            $root_path = implode(\DIRECTORY_SEPARATOR, $currentDir);


        \define('FM_PATH', $root_path .'/'. $p);
        defined('FM_SHOW_HIDDEN') || define('FM_SHOW_HIDDEN', $this->show_hidden_files);
        defined('FM_ROOT_PATH') || define('FM_ROOT_PATH', $root_path);
        $this->ReqObj = $ReqObj;
        $uri = $this->ReqObj->getURI();
        $urlPattern = $this->ReqObj->geturlPattern();
        $url_array = $this->ReqObj->url_array();
        $currentpage = $this->ReqObj->currentPage;
        // $this->db           = new PlexSql('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $this->db = new PlexSql();
        $this->currentpage = $currentpage;
        $this->request = $this->ReqObj->http_request;
        $this->urlPattern = $urlPattern;
    //    $this->getcwdFolder();s
    }

  
    public function getCurrentFileList()
    {
        // get parent folder
        $path = FM_PATH;
        $this->parent = $this->fm_get_parent_path(FM_PATH);
        $objects = is_readable($path) ? scandir($path) : [];
        $folders = [];
        $files = [];
        $current_path = \array_slice(explode('/', $path), -1)[0];
        if (\is_array($objects) && $this->fm_is_exclude_items($current_path)) {
            foreach ($objects as $file) {
                if ('.' == $file || '..' == $file) {
                    continue;
                }
                if (!FM_SHOW_HIDDEN && '.' === substr($file, 0, 1)) {
                    continue;
                }
                $new_path = $path.'/'.$file;
                if (@is_file($new_path) && $this->fm_is_exclude_items($file)) {
                    $files[] = $file;
                }
            }
        }

        if (!empty($files)) {
            natcasesort($files);
        }
     
        return $files;
    }
    public function getCurrentFolderList()
    {
        // get parent folder
        $path = FM_PATH;
        $this->parent = $this->fm_get_parent_path(FM_PATH);
        $objects = is_readable($path) ? scandir($path) : [];
        $folders = [];
        $files = [];
        $current_path = \array_slice(explode('/', $path), -1)[0];
        if (\is_array($objects) && $this->fm_is_exclude_items($current_path)) {
            foreach ($objects as $file) {
                if ('.' == $file || '..' == $file) {
                    continue;
                }
                if (!FM_SHOW_HIDDEN && '.' === substr($file, 0, 1)) {
                    continue;
                }
                $new_path = $path.'/'.$file;
               if (@is_dir($new_path) && '.' != $file && '..' != $file && $this->fm_is_exclude_items($file)) {
                    $folders[] = $file;
                }
            }
        }

    
        if (!empty($folders)) {
            natcasesort($folders);
        }
        return $folders;
    }
   

    public function getFolderList() {}


}
