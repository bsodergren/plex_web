<?php
require_once '../_config.inc.php';
define('TITLE', 'Home');

$url_array           = [
    'url'          => $_SERVER['SCRIPT_NAME'],
    'query_string' => $query_string,
    'current'      => $_SESSION['sort'],
    'direction'    => $_SESSION['direction'],
    'sort_types'   => [
        'Replacement' => 'replacement,hide,name',
        'Artist'      => 'hide,name,replacement',
        'hide'        => 'hide,replacement,name',
    ],
];

if (isset($_SESSION['direction'])) {
    $direction = $_SESSION['direction'];
}

if (isset($_SESSION['sort'])) {
    switch ($_SESSION['sort']) {
        case 'Replacement': break;
        case 'name': break;
        case 'hide': break;
        default:
            $_SESSION['sort'] = 'name';

            break;
    }

    $sort       = $_SESSION['sort'];
    $arr        = explode(',', $sort);
    $arr[0]     = $arr[0].' '.$direction;
    $sort_query = implode(',', $arr);
}

$pageObj             = new ArtistPagenate($currentPage, $urlPattern);

$sql                 = 'SELECT * FROM '.Db_TABLE_ARTISTS.' ORDER BY '.$sort_query;
$limit               = $pageObj->itemsPerPage;
$offset              = $pageObj->offset;

if (false != $limit && false == $offset) {
    $sql = $sql.' LIMIT '.$limit.'';
}
if (false != $limit && false != $offset) {
    $sql = $sql.'  LIMIT '.$offset.', '.$limit.'';
}
// dump($sql);
$results             = $db->query($sql);

$redirect_string     = 'Config/'.__THIS_FILE__.$request_string_query;

include __LAYOUT_HEADER__;

?>

<main role="main" class="container">
	<a
		href="<?php echo 'Config/'.__THIS_FILE__; ?>">back</a>
	<br>
	<br>
	<?php

    $artist_row_html = '';

foreach ($results as $key => $row) {
    $artist_row_html .= process_template(
        'config/artist/artist_row',
        [
            'ARTIST_ID'       => 'replacement_'.$row['id'],
            'ARTIST_NAME'     => $row['name'],
            'ARTIST_REP'      => $row['replacement'],
            'ARTIST_CHECKBOX' => draw_checkbox('hide_'.$row['id'], $row['hide'], ''),
        ]
    );
}

$hidden              = add_hidden('submit', 'ArtistConfigSave');
$hidden .= add_hidden('redirect', $redirect_string);
$artist_main_html .= process_template('config/artist/form_wrapper', [
    'HIDDEN'           => $hidden,
    'ARTIST_FORM_HTML' => $artist_row_html,
]);

Template::echo(
    'config/artist/main',
    [
        'ARTIST_MAIN_HTML' => $artist_main_html,
    ]
);

?>
</main>

<?php
define('__SHOW_PAGES__', 1);

require __LAYOUT_FOOTER__;
?>