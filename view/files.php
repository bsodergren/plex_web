<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php"));

require_once "../_config.inc.php";

define('TITLE', "Home");
define('PAGENATION', true);

$redirect_string = "view/files.php?genre=" . $_REQUEST['genre'];



if (isset($_REQUEST['genre'])) {
    $genre = $_REQUEST['genre'];


    $genre = str_replace("-", " ", $genre);
    $genre = str_replace("_", "/", $genre);

    $uri["genre"] = array($_REQUEST['genre'], $genre);

    if ($genre == "NULL") {
        //$sql_genre = " and " ." genre IS NULL ";
        $sql_genre = "";
    } else {
        $sql_genre = " genre LIKE '%" . $genre . "%' ";
    }
}

    $order_sort = "  title " . $_SESSION['direction'];
    if (isset($_SESSION['sort'])) {
        $order_sort = $_SESSION['sort'] . " " . $_SESSION['direction'];
        $request_key = "&genre=" . $_REQUEST['genre'];

    }
    if (isset($_GET['current'])) {
        $uri["current"] = $_GET['current'];
    }

    $request_key = uri_String($uri);

    $where = $lib_where . " AND " . $sql_genre;
    $db->where ("genre", '%'.$genre.'%', 'like');
    $db->where ("library", $in_directory, 'like');

    $db->withTotalCount()->get(Db_TABLE_FILEDB);
    $total_results = $db->totalCount;
    logger("all total_results", $total_results);
    $total_pages = ceil($db->totalCount / $no_of_records_per_page);

    /*
    $field_list = ' id, video_key,filename,thumbnail,title,artist,genre,studio,substudio,duration,favorite,fullpath,library ';

    $sql = ' select (@row_num:=@row_num +1) AS result_number, '.$field_list;
    $sql = $sql . ' from ( select ' . $field_list;
    $sql = $sql . ' from metatags_filedb WHERE '.$where.' order by '.$order_sort.' LIMIT '.$offset.', '.$no_of_records_per_page.' ) t1,';
    $sql = $sql . ' (select @row_num:='.$offset.') t2;';
*/

    $sql = query_builder("select", $where, false, $order_sort, $no_of_records_per_page, $offset);
    logger("all genres", $sql);

    $results = $db->query($sql);


    $url_array = array(
    "url" => $_SERVER['PHP_SELF'],
    "rq_string" => $request_key,
    "direction" => $_SESSION['direction'],
    "sort_types" => array(
    "Studio" => "studio",
    "Artist" => "artist",
    "Filename" => "filename",
    "Title" => "title",
    "Duration" => "Duration"
    )
    );

        include __LAYOUT_HEADER__;

    ?>

<main role="main" class="container">
<?php echo $total_results; ?> number of files<br>

<form action="process.php" method="post" id="formId">
<!-- <button type='submit' name="submit" onclick="hideSubmit('save')">Save</button> 
<button type='submit' name="submit" onclick="hideSubmit('delete')">Delete</button> -->

<input type='hidden' id="redirect" value="<?php echo $redirect_string; ?>">
    </p>
    <?php 
    if (isset($_REQUEST['genre'])) {
        //echo '<form action="files.php" method="post" id="myform">'."\n";

        //    $array=array(
        //        "VALUE_STUDIO" => $_REQUEST[$studio_key],
        //        "NAME_STUDIO" => $studio_key,
        //        "VALUE_GENRE" => $_REQUEST['genre'],
        //        "NAME_GENRE" => "genre");
        //    echo process_template("main_form",$array);


        $page_array = [
            'total_files'            => $total_results
        ];
        echo display_filelist($results,'',$page_array);

        //    echo "</form>";
    }

    ?>
    		<input type=hidden id="hiddenSubmit" name=submit value="">
    </form>
</main>
    <?php



require __LAYOUT_FOOTER__; 
