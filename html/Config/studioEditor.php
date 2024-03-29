<?php

use Plex\Template\Render;

use UTMTemplate\HTML\Elements;
use Plex\Modules\Config\StudioConfigSave;
use Plex\Template\Pageinate\ConfigPagenate;

require_once '../_config.inc.php';


$pageObj          = new ConfigPagenate("library = 'Studios'", $currentPage, $urlPattern);

$sql              = 'SELECT * FROM '.Db_TABLE_STUDIO."
 WHERE library = 'Studios' ORDER BY studio,path,name";

$limit            = $pageObj->itemsPerPage;
$offset           = $pageObj->offset;

if (false != $limit && false == $offset) {
    $sql = $sql.' LIMIT '.$limit.'';
}
if (false != $limit && false != $offset) {
    $sql = $sql.'  LIMIT '.$offset.', '.$limit.'';
}

$results          = $db->query($sql);

$redirect_string  = __THIS_FILE__;

\Plex\Template\Layout\Header::Display();

?>

<main role="main" class="container">
	<a
		href="<?php echo '../'.__THIS_FILE__; ?>">back</a>
	<br>
	<br>
	<?php

$studio_main_html = StudioConfigSave::displayAddStudioForm('Amateur', 'Add Amateur entry',$redirect_string);
$studio_main_html .= StudioConfigSave::displayAddStudioForm('Models', 'Add Models entry',$redirect_string);
$studio_main_html .= StudioConfigSave::displayAddStudioForm('Animation', 'Add Animation entry',$redirect_string);

$studio_main_html .= StudioConfigSave::displayAddStudioForm('Studios', 'Add Studios entry',$redirect_string);

foreach ($results as $key => $row) {
    $studio_rows[$row['studio']][] = $row;
}

foreach ($studio_rows as $library => $studios) {
    $studio_row_html = '';

    foreach ($studios as $k => $row) {
        $studio_row_html .= Render::html(
            'config/studio/studio_row',
            [
                'STUDIO_ID'   => 'studio_'.$row['id'],
                'PATH_ID'     => 'path_'.$row['id'],

                'STUDIO_NAME' => $row['name'],

                'STUDIO_PH'   => $row['studio'],
                'PATH_PH'     => $row['path'],
            ]
        );
    }

    $studio_list_html .= Render::html(
        'config/studio/studios',
        [
            'STUDIO_LIBRARY' => $library,
            'STUDIO_ROWS'    => $studio_row_html,
        ]
    );
}

$hidden           = Elements::add_hidden('submit', 'StudioConfigSave');
$hidden .= Elements::add_hidden('redirect', $redirect_string);
$studio_main_html .= Render::html('config/studio/form_wrapper', [
    'HIDDEN'           => $hidden,
    'STUDIO_FORM_HTML' => $studio_list_html,
]);

Render::echo(
    'config/studio/main',
    [
        'STUDIO_MAIN_HTML' => $studio_main_html,
    ]
);

?>
</main>

<?php

 \Plex\Template\Layout\Footer::Display();
?>