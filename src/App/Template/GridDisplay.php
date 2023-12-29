<?php
namespace Plex\Template;

use Plex\Template\VideoDisplay;
/**
 * plex web viewer
 */

class GridDisplay extends VideoDisplay
{
    public function __construct($template_base = 'filelist')
    {
        $this->template_base = $template_base;
    }

    public function gridview($results, $totalRecords)
    {
        global $_SESSION,$_REQUEST;
        global $db,$sort_type_map;

        $total           = count($results);
        if (0 == $total) {
            return 'No results';
        }
        $sql             = 'select * from '.Db_TABLE_PLAYLIST_DATA.';';
        $playlists       = $db->query($sql);
        foreach ($playlists as $p) {
            $playlist_html .= Template::GetHTML(
                'grid/playlist_link',
                [
                    'PL_NAME'    => $p['name'],
                    'PL_ID'      => $p['id'],
                    'VIDEO_DATA' => 'VIDEO_ID',
                ]
            );
        }
        $r               = 0;

        for ($i = 0; $i < count($results); ++$i) {
            $file_info = [
                'title'     => $results[$i]['title'],
                'library'   => $results[$i]['library'],
                'studio'    => $results[$i]['studio'],
                'substudio' => $results[$i]['substudio'],
                'artist'    => $results[$i]['artist'],
                'genre'     => $results[$i]['genre'],
                // 'filename'  => $results[$i]['filename'],
                'duration'  => videoDuration($results[$i]['duration']),

                // 'Duration' => videoDuration($results[$i]['duration']),
            ];
            if ('' != $results[$i]['substudio']) {
                $substudio = $results[$i]['substudio'];
            }
            if ('' != $results[$i]['studio']) {
                $studio = $results[$i]['studio'];
            }
            $videoInfo = '';
            foreach ($file_info as $field => $value) {
                // if ($value != '') {
                $videoInfo .= Template::GetHTML(
                    'grid/video_data',
                    [
                        'FILE_FIELD' => ucfirst($field),
                        'FILE_INFO'  => $value,
                    ]
                );
                // }
            }

            // THUMB_EXTRA = ' alt="#" class="img-fluid" '
            $cell_html .= Template::GetHTML(
                'grid/cell',
                [
                    'PLAYLIST_LINKS' => str_replace('VIDEO_ID', $results[$i]['id'], $playlist_html),
                    'THUMBNAIL'      => $this->fileThumbnail($results[$i]['id']),
                    'ROW_ID'         => $results[$i]['id'],
                    'VIDEO_DATA'     => $videoInfo,
                    'ROWNUM'         => $results[$i]['rownum'],
                    'ROW_TOTAL'      => $totalRecords,
                ]
            );
        }

        $row_html        = Template::GetHTML('grid/row', ['ROW_CELLS' => $cell_html]);

        $table_body_html = Template::GetHTML('grid/table', [
            'HIDDEN_STUDIO_NAME' => Render::add_hidden('studio', $studio).Render::add_hidden('substudio', $substudio),
            'ROWS_HTML'          => $row_html,
            'INFO_NAME'          => $sort_type_map['map'][$_REQUEST['sort']],
        ]);

        return $table_body_html;
    }
}
