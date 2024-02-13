<?php

use Plex\Template\Render;

use Plex\Template\Pageinate\ArtistPagenate;
use Plex\Template\HTML\Elements;

require_once '../_config.inc.php';
define('TITLE', 'Home');
define('ALPHA_SORT', true);
define('__SHOW_SORT__',false);


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
$url_array           = [
    'url'          => $_SERVER['SCRIPT_NAME'],
    'query_string' => $query_string,
    'current'      => $_SESSION['sort'],
    'direction'    => $_SESSION['direction'],
    'sort_types'   => [
        'Artist'      => 'name',
    ],
];
$pageObj             = new ArtistPagenate($currentPage, $urlPattern);
$where              = '';
$query              = urlQuerystring($urlPattern, ['current', 'allfiles'], true);
if (count($query) > 0) {
    $where = ' WHERE '.$query['sql'];
}

$sql                = 'SELECT * FROM '.Db_TABLE_ARTISTS.$where.' ORDER BY '.$sort_query;

$limit               = $pageObj->itemsPerPage;
$offset              = $pageObj->offset;

if (false != $limit && false == $offset) {
    $sql = $sql.' LIMIT '.$limit.'';
}
if (false != $limit && false != $offset) {
    $sql = $sql.'  LIMIT '.$offset.', '.$limit.'';
}
$results             = $db->query($sql);

$redirect_string     = 'Config/'.__THIS_FILE__.$request_string_query;
 \Plex\Template\Layout\Header::Display();

?>

<main role="main" class="container">
	<a
		href="<?php echo 'Config/'.__THIS_FILE__; ?>">back</a>
	<br>
	<br>
	<?php

    $artist_row_html = '';

foreach ($results as $key => $row) {
    $artist_row_html .= Render::html(
        'config/artist/artist_row',
        [
            'ARTIST_ID'       => 'replacement_'.$row['id'],
            'ARTIST_NAME'     => $row['name'],
            'ARTIST_REP'      => $row['replacement'],
            'ARTIST_CHECKBOX' => Elements::draw_checkbox('hide_'.$row['id'], $row['hide'], ''),
        ]
    );
}

$hidden              = Elements::add_hidden('submit', 'ArtistConfigSave');
$hidden .= Elements::add_hidden('redirect', $redirect_string);
$artist_main_html .= Render::html('config/artist/form_wrapper', [
    'HIDDEN'           => $hidden,
    'ARTIST_FORM_HTML' => $artist_row_html,
]);

Render::echo(
    'config/artist/main',
    [
        'ARTIST_MAIN_HTML' => $artist_main_html,
    ]
);

?>
</main>

<?php


define('__SHOW_PAGES__', 1);

 \Plex\Template\Layout\Footer::Display();
?>