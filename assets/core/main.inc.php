<?php
function display_size($bytes, $precision = 2) 
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= (1 << (10 * $pow)); 
    return round($bytes, $precision) . '<span class="fs-0-8 bold">' . $units[$pow] . "</span>";
}
function byte_convert($size) {
    # size smaller then 1kb
    if ($size < 1024) return $size . ' Byte';
    # size smaller then 1mb
    if ($size < 1048576) return sprintf("%4.2f KB", $size/1024);
    # size smaller then 1gb
    if ($size < 1073741824) return sprintf("%4.2f MB", $size/1048576);
    # size smaller then 1tb
    if ($size < 1099511627776) return sprintf("%4.2f GB", $size/1073741824);
    # size larger then 1tb
    else return sprintf("%4.2f TB", $size/1073741824);
  }

function uri_SQLQuery($request_array)
{
    foreach($request_array as $key => $value) {
        if($key == "sort") {
            continue;
        }
        if($key == "direction") {
            continue;
        }
        if($key == "pageno") {
            continue;
        }

        $string_value = $value;
        if(is_array($value)) {
            $string_value = $value[1];
        }

        $query_string = "= '$string_value'";
        if($string_value == "NULL") {
            $query_string = "IS NULL";
        }

        $uri_array[] = "$key $query_string";

    }

    $uri_query[] = implode(" AND ", $uri_array);

    if(key_exists("sort", $request_array) && key_exists("direction", $request_array)) {
        $sort_query = $request_array['sort'] . " " . $request_array['direction'];
        $uri_query[] = $sort_query;
    }

    return $uri_query;
}


function uri_String($request_array)
{
    foreach($request_array as $key => $value) {
        if(is_array($value)) {
            $string_value = $value[0];
        } else {
            $string_value = $value;
        }
        $uri_array[] = "$key=$string_value";
    }

    $uri_string = implode("&", $uri_array);
    return "?" . $uri_string;
}

function process_form($redirect_url)
{
    global $_POST;

    if(isset($_POST['submit'])) {
        if($_POST['submit'] == "save") {
            echo saveData($_POST, $redirect_url);
            exit;
        }

        if(str_starts_with($_POST['submit'], "delete")) {
            echo deleteEntry($_POST, $redirect_url);
            exit;
        }

        if(str_starts_with($_POST['submit'], "filedelete")) {
            echo deleteFile($_POST, $redirect_url);
            exit;
        }

        if(str_starts_with($_POST['submit'], "hide")) {
            echo hideEntry($_POST, $redirect_url);
            exit;
        }
    }
}


function doRequest($request, $callback, $return = 0, $redirect = FALSE)
{
    global $_REQUEST;

    $arr = array_keys($_REQUEST, $request, TRUE);

    if(count($arr) > 0) {
        $request = $arr[0];
    }

    if(isset($_REQUEST[$request])) {
        return $callback($_REQUEST, $redirect);

    } else {
        return 0;
    }
}


function missingStudio($key, $row)
{
    global $in_directory;
    $path_name = $row['fullpath'];
    $genre = $row['genre'];

    $dir = $in_directory;
    if ($in_directory == "Studio") {
        $dir = "Studios";}
        if ($in_directory == "Home Videos") {
            $dir = "HomeVideos";}
    

            $video_path = str_replace(__PLEX_LIBRARY__."/".$dir."/", "", $path_name);
            $video_path = str_replace($genre, "", $video_path);
            $video_path = str_replace("//", "", $video_path);

            $studio = $video_path;
            $substudio = '';

            if (str_contains($video_path,"/") ){
                $pcs = explode("/", $video_path);
                $substudio = $pcs[0];
                $studio = $pcs[1];
            }
            
        $value_array = array($key => array($$key), "style" => array("color:red"));

    return $value_array;
}

function missingGenre($key, $row)
{

    $path_name=$row['fullpath'];
    $value = "";

    preg_match('/(group|mmf|mff|single|only girls|bimale|trans|only blowjobs|compilation|Bisexual male|step fantasy|threesome)/i', $path_name, $output_array);
    if (isset($output_array[0]))
    {
        $value = $output_array[0];
    }
    $value_array = array($key => array($value), "style" => array("color:red"));

    return $value_array;

}

function missingArtist($key, $row)
{
    global $studio_pattern;
    global $__namesArray;
    global $artistNameFixArray;

    global $studio_ignore;

    $value_array = array();
    $match_studio = $row['studio'];

    $studio_match = strtolower(str_replace(" ", "_", $match_studio));


    unset($__match);
    if(key_exists($studio_match, $studio_pattern)) {
        $__match = $studio_match;
    }

//print_r2($studio_ignore);
//print_r2(str_replace(" ","_",strtolower($row['substudio'])));
//echo in_array(str_replace(" ","_",strtolower($row['substudio'])), $studio_ignore );
    if(isset($__match)) {

        $pattern = $studio_pattern[$__match]['artist']['pattern'];
        $delimeter = $studio_pattern[$__match]['artist']['delimeter'];
        $group = $studio_pattern[$__match]['artist']['group'];

        preg_match($pattern, $row['filename'], $matches);


        if(count($matches) > 0) {
            $names_array = explode($delimeter, $matches[$group]);
            $name_list = "";
            $full_name_array = array();

            foreach($names_array as $name) {
                $pieces = preg_split('/(?=[A-Z_])/', $name);
                $full_name = "";
                foreach($pieces as $part) {

                    $part = str_replace(" ", "", $part);

                    if($part == "") {
                        continue;
                    }
                    if($part == "_") {
                        continue;
                    }
                    $full_name .= " " . $part;

                }

                $full_name = trim($full_name);
                if(array_search(str_replace(" ", "", strtolower($full_name)), $__namesArray) == FALSE) {

                    if(array_key_exists($full_name, $artistNameFixArray)) {
                        $full_name = $artistNameFixArray[$full_name];
                    }
                    $full_name_array[] = ucfirst($full_name);
                }
            }
            $name_list = implode(", ", $full_name_array);
            $value_array = array($key => array($name_list), "style" => array("color:red"));
        }
    }

    return $value_array;
}

function missingTitle($key, $row)
{
    global $studio_pattern;
    global $__namesArray;

    $value_array = array();
    $match_studio = $row['studio'];

    $studio_match = strtolower(str_replace(" ", "_", $match_studio));

    unset($__match);
    if(key_exists($studio_match, $studio_pattern)) {
        $__match = $studio_match;
    }

    if(isset($__match)) {
        if(key_exists("title", $studio_pattern[$__match])) {
            $pattern = $studio_pattern[$__match]['title']['pattern'];
            $group = $studio_pattern[$__match]['title']['group'];

            preg_match($pattern, $row['filename'], $matches);

            if(count($matches) > 0) {
                $title = $matches[$group];
                $title = strtolower(str_replace("_", " ", $title));
                $title = ucwords($title);
                $value_array = array($key => array($title), "style" => array("color:red"));
            }
        }
    }
    return $value_array;
}

function deleteEntry($data_array, $redirect = FALSE, $timeout = 4)
{
    global $db;
    global $_SERVER;
    if(key_exists("submit", $data_array)) {
        $key = $data_array['submit'];
        if(str_contains($key, "_") == TRUE) {
            $pcs = explode("_", $key);
            $id = $pcs[1];
            $field = $pcs[0];
            if($field == "delete") {
                logger("Delete entry", $id);
                $db->where("id", $id);
                $user = $db->getOne(Db_TABLE_FILEDB);

                $thumbnail_file = $_SERVER['DOCUMENT_ROOT'] . $user['thumbnail'];
                chk_file($thumbnail_file, 'delete');

                $db->where('id', $id);
                $db->delete(Db_TABLE_FILEDB);
            }
        }
    }
    if($redirect != FALSE) {
        return JavaRefresh($redirect, $timeout);
    }
}

function deleteFile($data_array, $redirect = FALSE, $timeout = 4)
{
    global $db;
    global $_SERVER;

    if(key_exists("submit", $data_array)) {
        $key = $data_array['submit'];
        if(str_contains($key, "_") == TRUE) {
            $pcs = explode("_", $key);
            $id = $pcs[1];
            $field = $pcs[0];
            if($field == "filedelete") {
                $db->where("id", $id);
                $user = $db->getOne(Db_TABLE_FILEDB);

                $thumbnail_file = $_SERVER['DOCUMENT_ROOT'] . $user['thumbnail'];
                $video_file = $user['fullpath'] . $user['filename'];

                chk_file($thumbnail_file, 'delete');
                chk_file($video_file, 'delete');

                $db->where('id', $id);
                $db->delete(Db_TABLE_FILEDB);
            }
        }
    }

    if($redirect != FALSE) {
        return JavaRefresh($redirect, $timeout);
    }
}


function hideEntry($data_array, $redirect = FALSE, $timeout = 4)
{
    global $db;

    if(key_exists("submit", $data_array)) {
        $key = $data_array['submit'];
        if(str_contains($key, "_") == TRUE) {
            $pcs = explode("_", $key);
            $id = $pcs[1];
            $field = $pcs[0];
            if($field == "hide") {
                $sql = "UPDATE " . Db_TABLE_FILEDB . " SET added = (CURRENT_TIMESTAMP - INTERVAL 3 day) WHERE id = " . $id;
                logger("hide sql", $sql);

                $result = $db->query($sql);

                $db->where('id', $id);
                $db->delete(Db_TABLE_FILEDB);
            }
        }
    }
    if($redirect != FALSE) {
        return JavaRefresh($redirect, $timeout);
    }

}

function saveData($data_array, $redirect = FALSE, $timeout = 4)
{
    global $db;
    

    foreach($data_array as $key => $value) {
        if(str_contains($key, "_") == TRUE) {
            $value = trim($value);

            if($value != "") {
                $pcs = explode("_", $key);

                $id = $pcs[0];
                $field = $pcs[1];
                $atom_field=$field;
                $atom_value=$value;

                if($field == "id") {
                    continue;
                }
                if($field == "filename") {
                    $filename = $value;
                    continue;
                }
                if(isset($pcs[2])) {
                    $field .= "_" . $pcs[2];
                }

                if($value == "NULL") {
                    $sql = "UPDATE " . Db_TABLE_FILEDB . "  SET `" . $field . "` = NULL WHERE id = " . $id;

                    $db->query($sql);
                } else {

                    if($field == "artist") {
                        if(str_contains($value, "-") == TRUE) {
                            $value = str_replace("-", " ", $value);
                        }
                        if(str_contains($value, ",") == TRUE) {
                            $value = str_replace(" ,", ",", $value);
                            $value = str_replace(", ", ",", $value);
                        }

                        $names_arr = explode(",", $value);
                        $names_list = "";

                        foreach($names_arr as $str_name) {
                            $str_name = ucwords(strtolower($str_name));
                            $names_list = $str_name . "," . $names_list;
                        }

                        $value = rtrim($names_list, ',');

                    }

                    if ($field == "studio" || $field == "substudio")
                    {                                                  
                    
                        if($field == "substudio") {
                            $studio_value=metadata_get_value($filename, "studio");
                            $atom_field="studio";
                            $atom_value="$studio_value/$value";
                        }
                    }

                    $atom_value = trim($atom_value);
                    $value = trim($value);

                   metadata_write_filedata($filename,[$atom_field => $atom_value]);


                    $data = array($field => $value);
                    $db->where('id', $id);
                    $db->update(Db_TABLE_FILEDB, $data);
                    
                }
            }
        }
    }
    if($redirect != FALSE) {
        return JavaRefresh($redirect, $timeout);
    }

}

function myHeader($redirect = __URL_PATH__ . "/home.php")
{


    header("refresh:0;url=" . $redirect);

}


function getBaseUrl($pathOnly = FALSE)
{
    // output: /myproject/index.php
    $currentPath = $_SERVER['PHP_SELF'];

    // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index )
    $pathInfo = pathinfo($currentPath);

    // output: localhost
    $hostName = $_SERVER['HTTP_HOST'];

    // output: http://
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https://' ? 'https://' : 'http://';

    if($pathOnly == TRUE)
        return $protocol . $hostName . $pathInfo['dirname'] . "/";
    // return: http://localhost/myproject/
    return $protocol . $hostName . $pathInfo['dirname'] . "/";
}


function print_r2($val)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}

function print_request($array)
{
    if(is_array($array)) {
        //$newarray=array();

        foreach($array as $key => $value) {

            if($value != "") {
                $newarray[$key] = $value;
            }
        }

        if(isset($newarray)) {
            print_r2($newarray);
        }
    }
}


function toint($string)
{

    $string_ret = str_replace(",", "", $string);
    return $string_ret;
}

function array_find($needle, $haystack)
{
    foreach($haystack as $item) {
        if(strpos($item, $needle) !== FALSE) {
            return $item;
            break;
        }
    }
}