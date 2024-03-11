<?php
use Plex\Template\Render;

use Plex\Template\Pageinate\GenrePagenate;
use UTMTemplate\HTML\Elements;

require_once '../_config.inc.php';
define('TITLE', 'Home');
define('ALPHA_SORT', true);
 define('__SHOW_SORT__',true);

if (isset($_SESSION['direction'])) {
    $direction = $_SESSION['direction'];
}
if (isset($_REQUEST['alpha'])) {
    // $_REQUEST['alpha'] = '1';
    // } else {
    $uri['alpha'] = $_REQUEST['alpha'];
}
$_SESSION['sort'] = 'genre';
if (isset($_SESSION['sort'])) {
    switch ($_SESSION['sort']) {
        case 'Replacement': break;
            // case 'Genre': break;
        case 'Keep': break;
        default:
            $_SESSION['sort'] = 'genre';
            break;
    }
    $sort       = $_SESSION['sort'];

    $sort_query = $sort.' '.$direction;
}

$url_array          = [
    'url'          => $_SERVER['SCRIPT_NAME'],
    'query_string' => $query_string,
    'sortDefault' => 'genre',

    'current'      => $_SESSION['sort'],
    'direction'    => $_SESSION['direction'],
    'sort_types'   => [
        'Genre' => 'genre',
    ],
];


$pageObj            = new GenrePagenate($currentPage, $urlPattern);
$where              = '';

$query              = urlQuerystring($urlPattern, ['current', 'allfiles', 'sec'], true);

if (count($query) > 0) {
    $where = ' WHERE '.$query['sql'];
}

$sql                = 'SELECT * FROM '.Db_TABLE_GENRE.$where.' ORDER BY '.$sort_query;

$limit              = $pageObj->itemsPerPage;
$offset             = $pageObj->offset;

if (false != $limit && false == $offset) {
    $sql = $sql.' LIMIT '.$limit.'';
}
if (false != $limit && false != $offset) {
    $sql = $sql.'  LIMIT '.$offset.', '.$limit.'';
}
$results            = $db->query($sql);

$redirect_string    = 'Config/'.__THIS_FILE__.$request_string_query;

 Header::Display();

?>

<main role="main" class="container">
	<a href="<?php echo 'Config/'.__THIS_FILE__; ?>">back</a>
	<br>
	<br>
	<?php

    $genre_row_html = '';

foreach ($results as $key => $row) {
    $genre_row_html .= Render::html(
        'config/genre/genre_row',
        [
            'GENRE_ID'       => 'replacement_'.$row['id'],
            'GENRE_NAME'     => $row['genre'],
            'GENRE_REP'      => $row['replacement'],
            'GENRE_CHECKBOX' => Elements::draw_checkbox('keep_'.$row['id'], $row['keep'], ''),
        ]
    );
}

$hidden             = Elements::add_hidden('submit', 'GenreConfigSave');
$hidden .= Elements::add_hidden('redirect', $redirect_string);
$genre_main_html .= Render::html('config/genre/form_wrapper', [
    'HIDDEN'          => $hidden,
    'GENRE_FORM_HTML' => $genre_row_html,
]);

Render::echo(
    'config/genre/main',
    [
        'GENRE_MAIN_HTML' => $genre_main_html,
    ]
);

?>
</main>

<?php

 Footer::Display();
?>