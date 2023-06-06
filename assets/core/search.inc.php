<?php


function searchDBVideos($request)
{

    $group = "OR";
    if(key_exists('grp',$request)){
        $group = $request['grp'];
    }
    if(key_exists('field',$request)){
        $request[$request['field']][]=$request['query'];
    }


    foreach($request as $field => $value){

    switch($field){
        case 'studio':
        case 'substudio':
        case 'keyword':
        case 'genre':
        case 'artist':
            
            
            $whereArray = [];
            $qArray = [];
            if(is_array($value))
            {
                foreach($value as $q){
                    $q = str_replace("+"," ",$q);
                    $whereArray[] = $field . " LIKE '%" . $q . "%' ";               
                    $qArray[]=$q;
                }
                $words[$field] = implode(",", $qArray);
                
                  $where[] = " ( ". implode(" ".$group." ", $whereArray) . ") ";
            }
        break;
        }
    }

    $where_clause = " ( ". implode(" ".$group." ", $where) . ") ";

    return [$where_clause,$words];

}



function file_search($location='', $fileregex='', $class_options='', $maxdepth='')
{
    $matchedfiles = array();

    if (!$location or !is_dir($location) or !$fileregex) {
        return false;
    }

    if (isset($class_options->options["file"])) {
        // turn comma separeted list of files into array
        $matchedfiles = explode(",", $class_options->options["file"]);
    } else {
        if ($maxdepth == 1) {
            $my_DirectoryIterator="DirectoryIterator";
            $my_IteratorIterator="IteratorIterator";
        } else {
            $my_DirectoryIterator="RecursiveDirectoryIterator";
            $my_IteratorIterator="RecursiveIteratorIterator";
        }


        $Directory = new $my_DirectoryIterator($location);
        $Iterator = new $my_IteratorIterator($Directory);

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
    } else {
        return array();
    }
}


function file_get_num_results($array, $options_arg)
{
    if (isset($options_arg->options["max"])) {
        verbose_output("Max number of results " . $options_arg->options["max"]);

        return $options_arg->options["max"];
    } else {
        return count($array);
    }
}
