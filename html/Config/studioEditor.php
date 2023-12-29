<?php

require_once '../_config.inc.php';

define('TITLE', 'Home');

$pageObj          = new ConfigPagenate("library = 'Studios'", $currentPage, $urlPattern);

$sql              = 'SELECT * FROM '.Db_TABLE_STUDIO." WHERE library = 'Studios' ORDER BY studio,path,name";

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

include __LAYOUT_HEADER__;

?>

<main role="main" class="container">
	<a
		href="<?php echo '../'.__THIS_FILE__; ?>">back</a>
	<br>
	<br>
	<?php

    $hidden       = Render::add_hidden('library', 'Amateur');
$hidden .= Render::add_hidden('submit', 'addNewEntry');
$hidden .= Render::add_hidden('redirect', $redirect_string);
$lib_name         = 'Add Amateur entry';

$studio_add_entry = Template::GetHTML('config/studio/studio_row', ['STUDIO_ID' => 'studio', 'PATH_ID' => 'path', 'STUDIO_NAME' => '<input type="text" name="name">']);
$studio_add_entry = Template::GetHTML('config/studio/studios', ['STUDIO_LIBRARY' => $lib_name, 'STUDIO_ROWS' => $studio_add_entry]);
$studio_main_html = Template::GetHTML('config/studio/form_wrapper', ['HIDDEN' => $hidden, 'STUDIO_FORM_HTML' => $studio_add_entry]);

$hidden           = Render::add_hidden('library', 'Studios');
$hidden .= Render::add_hidden('submit', 'addNewEntry');
$hidden .= Render::add_hidden('redirect', $redirect_string);
$lib_name         = 'Add Studios entry';

$studio_add_entry = Template::GetHTML('config/studio/studio_row', ['STUDIO_ID' => 'studio', 'PATH_ID' => 'path', 'STUDIO_NAME' => '<input type="text" name="name">']);
$studio_add_entry = Template::GetHTML('config/studio/studios', ['STUDIO_LIBRARY' => $lib_name, 'STUDIO_ROWS' => $studio_add_entry]);
$studio_main_html .= Template::GetHTML('config/studio/form_wrapper', ['HIDDEN' => $hidden, 'STUDIO_FORM_HTML' => $studio_add_entry]);

foreach ($results as $key => $row) {
    $studio_rows[$row['studio']][] = $row;
}

foreach ($studio_rows as $library => $studios) {
    $studio_row_html = '';

    foreach ($studios as $k => $row) {
        $studio_row_html .= Template::GetHTML(
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

    $studio_list_html .= Template::GetHTML(
        'config/studio/studios',
        [
            'STUDIO_LIBRARY' => $library,
            'STUDIO_ROWS'    => $studio_row_html,
        ]
    );
}

$hidden           = Render::add_hidden('submit', 'StudioConfigSave');
$hidden .= Render::add_hidden('redirect', $redirect_string);
$studio_main_html .= Template::GetHTML('config/studio/form_wrapper', [
    'HIDDEN'           => $hidden,
    'STUDIO_FORM_HTML' => $studio_list_html,
]);

Template::echo(
    'config/studio/main',
    [
        'STUDIO_MAIN_HTML' => $studio_main_html,
    ]
);

?>
</main>

<?php
define('__SHOW_PAGES__', 1);

require __LAYOUT_FOOTER__;
?>