<?php
/**
 * plex web viewer
 */

/**
 * Command like Metatag writer for video files.
 */
function searchDBVideos($request)
{
    $group        = 'OR';
    if (array_key_exists('grp', $request)) {
        $group = $request['grp'];
    }

    if (array_key_exists('field', $request)) {
        if (array_key_exists('query', $request)) {
            $request[$request['field']][] = $request['query'];
        }
    }

    if (array_key_exists('field', $request)) {
        $req_field = $request['field'];
        if (is_array($req_field)) {
            foreach ($req_field as $f) {
                if (array_key_exists($f, $request)) {
                    $field = $request[$f];
                    if (!is_array($field)) {
                        $array       = explode(',', $field);
                        $request[$f] = $array;
                    } else {
                        $request[$f] = $field;
                    }
                }
            }
        }
    }

    if (array_key_exists('searchField', $request)) {
        if (is_array($request['searchField'])) {
            foreach ($request['searchField'] as $_ => $f) {
                $request[$f] = explode(',', $request['query']);
            }
        } else {
            $request[$request['searchField']][] = $request['query'];
        }
    }

    foreach ($request as $field => $value) {
        switch ($field) {
            case 'studio':
            case 'substudio':
            case 'keyword':
                $whereArray = [];
                $qArray     = [];

                // no break
            case 'genre':
                $whereArray = [];
                $qArray     = [];

                // no break
            case 'artist':
                $whereArray = [];
                $qArray     = [];
                if (is_array($value)) {
                    foreach ($value as $q) {
                        $q            = str_replace('+', ' ', $q);
                        $whereArray[] = $field." LIKE '%".$q."%' ";
                        $qArray[]     = $q;
                    }
                    $words[$field] = implode(',', $qArray);

                    $where[]       = ' ( '.implode(' '.$group.' ', $whereArray).') ';
                }

                break;
        }
    }

    $where_clause = ' ( '.implode(' OR ', $where).') ';

    return [$where_clause, $words];
}

function file_search($location = '', $fileregex = '', $class_options = '', $maxdepth = '')
{
    $matchedfiles = [];

    if (!$location || !is_dir($location) || !$fileregex) {
        return false;
    }

    if (isset($class_options->options['file'])) {
        // turn comma separeted list of files into array
        $matchedfiles = explode(',', $class_options->options['file']);
    } else {
        if (1 == $maxdepth) {
            $my_DirectoryIterator = 'DirectoryIterator';
            $my_IteratorIterator  = 'IteratorIterator';
        } else {
            $my_DirectoryIterator = 'RecursiveDirectoryIterator';
            $my_IteratorIterator  = 'RecursiveIteratorIterator';
        }

        $Directory = new $my_DirectoryIterator($location);
        $Iterator  = new $my_IteratorIterator($Directory);

        foreach ($Iterator as $info) {
            $__file_ext = $info->getExtension();
            if (strtolower($fileregex) == strtolower($__file_ext)) {
                $matchedfiles[] = $info->getPathname();
            }
        }
    }

    if (count($matchedfiles) >= 1) {
        sort($matchedfiles);

        return $matchedfiles;
    }

    return [];
}

function file_get_num_results($array, $options_arg)
{
    if (isset($options_arg->options['max'])) {
        verbose_output('Max number of results '.$options_arg->options['max']);

        return $options_arg->options['max'];
    }

    return count($array);
}
