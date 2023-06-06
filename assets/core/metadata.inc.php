<?php


function metadata_get_fileData($file='')
{
    $AtomicParsley = __ATOM__;

    $run_cmd = $AtomicParsley." '".realpath($file)."' -t";
    $results = shell_exec($run_cmd);
    return $results;
}//end metadata_get_fileData()


function metadata_get_value($file='', $tag='')
{
    $metadata = metadata_get_filedata($file);

    switch ($tag) {
        case 'studio':
            $regex = '/(alb).*\:\ (.*)/';
            break;

        case 'genre':
            $regex = '/(gen).*\:\ (.*)/';
            break;

        case 'title':
            $regex = '/(nam).*\:\ (.*)/i';
            break;

        case 'artist':
            $regex = '/(\"©ART\").*\:\ (.*)/';
            break;
    }

    preg_match($regex, $metadata, $matches);

    if (isset($matches[2])) {
        $output = $matches[2];
        return strval($output);
    } else {
        return false;
    }
}//end metadata_get_value()


function metadata_write_filedata($file='', $value_array=[])
{
    $AtomicParsley = __ATOM__;
    $options       = '';

    foreach ($value_array as $tag => $value) {
        if ($tag == 'studio' || $tag == 'substudio') {
            $tag = 'album';
        }

        $value = str_replace("\'", "'", $value);

        $options .= '--'.$tag.'="'.$value.'" ';
    }

    logger('writing options', $options);
    $run_cmd = $AtomicParsley." '".realpath($file)."' ".$options.' -W';
    $results = shell_exec($run_cmd);

    return $results;
}//end metadata_write_filedata()


function missingStudio($key, $row)
{
    global $in_directory;
    $path_name = $row['fullpath'];
    $genre     = $row['genre'];

    $dir = $in_directory;


    $video_path = str_replace(__PLEX_LIBRARY__.'/'.$dir.'/', '', $path_name);
    $video_path = str_replace($genre, '', $video_path);
    $video_path = str_replace('//', '', $video_path);

    $studio    = $video_path;
    $substudio = '';

    if (str_contains($video_path, '/')) {
        $pcs       = explode('/', $video_path);
        $substudio = $pcs[0];
        $studio    = $pcs[1];
    }

    $value_array = [
        $key    => [$$key],
        'style' => ['color:red'],
    ];

    return $value_array;
}//end missingStudio()


function missingGenre($key, $row)
{
    $path_name = $row['fullpath'];
    $value     = '';

    preg_match('/(group|mmf|mff|single|only girls|bimale|trans|only blowjobs|compilation|Bisexual male|step fantasy|threesome)/i', $path_name, $output_array);
    if (isset($output_array[0])) {
        $value = $output_array[0];
    }

    $value_array = [
        $key    => [$value],
        'style' => ['color:red'],
    ];

    return $value_array;
}//end missingGenre()


function missingArtist($key, $row)
{
    global $studio_pattern;
    global $__namesArray;
    global $artistNameFixArray;

    global $studio_ignore;

    $value_array  = [];

    if ($row['substudio'] != '') {
        $match_studio = $row['substudio'];
    } else {
        $match_studio = $row['studio'];
    }

    $studio_match = strtolower(str_replace(' ', '_', $match_studio));

    unset($__match);
    if (key_exists($studio_match, $studio_pattern)) {
        $__match = $studio_match;
        logger("studio_pattern", $__match);
    }

    // print_r2($studio_ignore);
    // print_r2(str_replace(" ","_",strtolower($row['substudio'])));
    if (in_array(str_replace(" ", "_", strtolower($row['substudio'])), $studio_ignore)) {
        return false;
    }

    if (isset($__match)) {
        if (key_exists('artist', $studio_pattern[$__match])) {
            $pattern   = $studio_pattern[$__match]['artist']['pattern'];
            $delimeter = $studio_pattern[$__match]['artist']['delimeter'];
            $group     = $studio_pattern[$__match]['artist']['group'];

            logger("studio_pattern", $pattern);
            logger("studio_pattern", $delimeter);
            logger("studio_pattern", $group);
            logger("studio_pattern", $row['filename']);
            preg_match($pattern, $row['filename'], $matches);

            if (count($matches) > 0) {
                $names_array = explode($delimeter, $matches[$group]);
                $name_list = '';
                $full_name_array = [];

                foreach ($names_array as $name) {
                    $pieces = preg_split('/(?=[A-Z_])/', $name);

                    $full_name = '';
                    foreach ($pieces as $part) {
                        $part = str_replace('_', '', $part);

                        if ($part == '') {
                            continue;
                        }

                        if ($part == '_') {
                            continue;
                        }

                        $full_name .= ' ' . $part;
                    }

                    $full_name = trim($full_name);
                    if (array_search(str_replace(' ', '', strtolower($full_name)), $__namesArray) == false) {
                        if (array_key_exists($full_name, $artistNameFixArray)) {
                            $full_name = $artistNameFixArray[$full_name];
                        }

                        $full_name_array[] = ucfirst($full_name);
                    }
                } //end foreach

                $name_list = implode(', ', $full_name_array);
                $value_array = [
                    $key => [$name_list],
                    'style' => ['color:red'],
                ];
            }
        }//end if
    }//end if

    return $value_array;
}//end missingArtist()


function missingTitle($key, $row)
{
    global $studio_pattern;
    global $__namesArray;

    $value_array  = [];
    if ($row['substudio'] != '') {
        $match_studio = $row['substudio'];
    } else {
        $match_studio = $row['studio'];
    }



    $studio_match = strtolower(str_replace(' ', '_', $match_studio));

    unset($__match);
    if (key_exists($studio_match, $studio_pattern)) {
        $__match = $studio_match;
    }

    if (isset($__match)) {
        if (key_exists('title', $studio_pattern[$__match])) {
            $pattern = $studio_pattern[$__match]['title']['pattern'];
            $group   = $studio_pattern[$__match]['title']['group'];
            logger("studio_pattern", $pattern);
            logger("studio_group", $group);
            $delimeter = '_';
            if (key_exists('delimeter', $studio_pattern[$__match]['title'])) {
                $delimeter = $studio_pattern[$__match]['title']['delimeter'];
            }
            logger("studio_filename", $row['filename']);
            preg_match($pattern, $row['filename'], $matches);

            if (count($matches) > 0) {
                $title       = $matches[$group];
                $title       = strtolower(str_replace($delimeter, ' ', $title));
                if (key_exists('episode_pattern', $studio_pattern[$__match]['title'])) {
                    $epi_pattern = $studio_pattern[$__match]['title']['episode_pattern'];

                    preg_match($epi_pattern, $title, $epi_matches);
                    $title       = $epi_matches[3];
                    $__episode = strtoupper($epi_matches[2]);
                    $__season = strtoupper($epi_matches[1]);
                    $title = "$__season:$__episode $title";
                }

                $title       = ucwords($title);
                $value_array = [
                    $key    => [$title],
                    'style' => ['color:red'],
                ];
            }
        }
    }

    return $value_array;
}//end missingTitle()
