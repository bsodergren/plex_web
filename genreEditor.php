<?php
require_once("_config.inc.php");
define('TITLE', "Home");



$url_array = [
    'url'          => $_SERVER['SCRIPT_NAME'],
    'query_string' => $query_string,
    'current'      => $_SESSION['sort'],
    'direction'    => $_SESSION['direction'],
    'sort_types'   => [
        'Replacement'   => 'replacement,keep,genre',
        'Genre' => 'keep,genre,replacement',
        'Keep' => 'keep,replacement,genre',
    ],
];


if (isset($_SESSION['direction'])) {
    $direction = $_SESSION['direction'];
}


if (isset($_SESSION['sort'])) {
    switch($_SESSION['sort']){
        case 'Replacement': break;
        case 'Genre': break;
        case 'Keep': break;
        default:
        $_SESSION['sort'] = "Keep";
        break;
    }

    $sort = $_SESSION['sort'];
    $arr = explode(",", $sort);
    $arr[0] = $arr[0] . " " . $direction;
    $sort_query = implode(",", $arr);
}

$pageObj = new GenrePagenate($currentPage, $urlPattern);

$sql = "SELECT * FROM " . Db_TABLE_GENRE . " ORDER BY " . $sort_query;
$limit = $pageObj->itemsPerPage;
$offset = $pageObj->offset;

if ($limit != FALSE && $offset == FALSE) {
    $sql = $sql . " LIMIT " . $limit . '';
}
if ($limit != FALSE && $offset != FALSE) {
    $sql = $sql . "  LIMIT " . $offset . ", " . $limit . '';
}

$results       = $db->query($sql);

$redirect_string = __THIS_FILE__ . $request_string_query;

include __LAYOUT_HEADER__;

?>

<main role="main" class="container">
    <a href="<?php echo __THIS_FILE__; ?>">back</a>
    <br>
    <br>
    <?php
    
    $genre_row_html = '';

    foreach ($results as $key => $row) {
        $genre_row_html .= process_template(
            "config/genre/genre_row",
            [
                'GENRE_ID' => "replacement_" . $row['id'],
                'GENRE_NAME' => $row['genre'],
                'GENRE_REP' => $row['replacement'],
                'GENRE_CHECKBOX' =>  draw_checkbox("keep_" . $row['id'],  $row['keep'], ""),
            ]
        );
    }



    $hidden = add_hidden("submit", "GenreConfigSave");
    $hidden .= add_hidden("redirect", $redirect_string);
    $genre_main_html .=  process_template("config/genre/form_wrapper", [
        'HIDDEN' => $hidden,
        "GENRE_FORM_HTML" => $genre_row_html
    ]);

    echo  process_template(
        "config/genre/main",
        [
            'GENRE_MAIN_HTML' => $genre_main_html

        ]
    );




    ?>
</main>

<?php
define('__SHOW_PAGES__', 1);

require __LAYOUT_FOOTER__;
