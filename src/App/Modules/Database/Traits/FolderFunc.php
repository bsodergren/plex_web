<?php

namespace Plex\Modules\Database\Traits;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

trait FolderFunc
{
    public function fm_get_items_in_folder($path) {}

    public function fm_is_exclude_items($file)
    {
        $ext = strtolower(pathinfo($file, \PATHINFO_EXTENSION));
        if (isset($exclude_items) && \count($exclude_items)) {
            unset($exclude_items);
        }

        $exclude_items = FM_EXCLUDE_ITEMS;
        if (version_compare(\PHP_VERSION, '7.0.0', '<')) {
            $exclude_items = unserialize($exclude_items);
        }
        if (!\in_array($file, $exclude_items) && !\in_array("*.{$ext}", $exclude_items)) {
            return true;
        }

        return false;
    }

    public function fm_clean_path($path, $trim = true)
    {
        $path = $trim ? trim($path) : $path;
        $path = trim($path, '\\/');
        $path = str_replace(['../', '..\\'], '', $path);
        $path = $this->get_absolute_path($path);
        if ('..' == $path) {
            $path = '';
        }

        return str_replace('\\', '/', $path);
    }

    public function fm_get_parent_path($path)
    {
        $path = $this->fm_clean_path($path);
        if ('' != $path) {
            $array = explode('/', $path);
            if (\count($array) > 1) {
                $array = \array_slice($array, 0, -1);

                return implode('/', $array);
            }

            return '';
        }

        return false;
    }

    public function get_absolute_path($path)
    {
        $path = str_replace(['/', '\\'], \DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(\DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return implode(\DIRECTORY_SEPARATOR, $absolutes);
    }

    public function countFiles($path){


        $process = new Process(['find', $path, '-type','f']);
        $process->run();
        $output = $process->getOutput();

        return count(explode("\n",$output));
    }
    /**
     * This function scans the files and folder recursively, and return matching files.
     *
     * @return array|null
     */
    public function scan($path = '', $count = false)
    {
        // $path = $this->currentDir.'/'.$dir;
        if ($path) {
            $ite = new \DirectoryIterator($path);
            // $rii = new \RegexIterator($ite, '/('.$filter.')/i');
            //  utminfo([__METHOD__,$ite]);

            $files = [];
            foreach ($ite as $file) {
                if (true == $file->isDir()) {
                    $fi = new \FilesystemIterator($file->getPathname(), \FilesystemIterator::SKIP_DOTS);
                    $folderCount = iterator_count($fi);
                    $fullpath = $file->getPathname();

                    $root = \dirname($file->getPathname());
                    $path = str_replace($root.'/', '', $fullpath);
                    if ('..' == $path || '.' == $path) {
                        continue;
                    }
                    $videoCount = $this->countFiles($fullpath);
                    $files[] = ['folder' => $path, 'folderCount' => $folderCount, 'videoCount' => $videoCount];
                    // if (!$file->isDir()) {
                    //     $fileName = $file->getFilename();
                    //     $location = str_replace($this->currentDir, '', $file->getPath());
                    //     $files[] = [
                    //         'name' => $fileName,
                    //         'type' => 'file',
                    //         'path' => $location,
                    //     ];
                    // }
                }
            }
            return $files;
        }
    }
}
