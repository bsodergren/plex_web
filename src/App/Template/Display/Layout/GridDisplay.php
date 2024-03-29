<?php
namespace Plex\Template\Display\Layout;

use Plex\Modules\VideoCard\VideoCard;
use Plex\Template\Render;
use UTMTemplate\HTML\Elements;
use Plex\Template\Display\VideoDisplay;
use Plex\Template\Functions\Traits\Video;

use Plex\Modules\Database\PlexSql;


/**
 * plex web viewer
 */

class GridDisplay extends VideoDisplay
{

    use Video;
    public $showVideoDetails = false;
    private $template_base   = '';

    public function __construct($template_base = 'filelist')
    {
        $this->template_base = $template_base;
    }


    public function display($results, $page_array = [])
    {
        global $_SESSION,$_REQUEST;
        global $sort_type_map;
$db = PlexSql::$DB;
        if (isset($page_array['total_files'])) {
            $totalRecords = $page_array['total_files'];
        }

        if (isset($page_array['redirect_string'])) {
            $redirect_string = $page_array['redirect_string'];
        }
        
        $total           = count($results);
        if (0 == $total) {
            return 'No results';
        }
        $sql             = 'select * from '.Db_TABLE_PLAYLIST_DATA.';';
        $playlists       = $db->query($sql);
        foreach ($playlists as $p) {
            $playlist_html .= Render::html(
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
            $full_filename = $results[$i]['fullpath'].\DIRECTORY_SEPARATOR.$results[$i]['filename'];

            // %%FILE_MISSING%%
            $class_missing = '';
            if (!file_exists($full_filename)) {
                $class_missing = 'bg-danger';
            }
            $file_info     = [
                'title'     => $results[$i]['title'],
                'library'   => $results[$i]['library'],
                'studio'    => $results[$i]['studio'],
                'substudio' => $results[$i]['substudio'],
                'artist'    => $results[$i]['artist'],
                'genre'     => $results[$i]['genre'],
                'added'     => $results[$i]['added'],
                // 'filename'  => $results[$i]['filename'],
                'duration'  => VideoCard::videoDuration($results[$i]['duration']),

                // 'Duration' => videoDuration($results[$i]['duration']),
            ];
            if ('' != $results[$i]['substudio']) {
                $substudio = $results[$i]['substudio'];
            }
            if ('' != $results[$i]['studio']) {
                $studio = $results[$i]['studio'];
            }
            $videoInfo     = '';
            foreach ($file_info as $field => $value) {
                // if ($value != '') {
                $videoInfo .= Render::html(
                    'grid/video_data',
                    [
                        'FILE_FIELD'   => ucfirst($field),
                        'FILE_INFO'    => $value,
                        'FILE_MISSING' => $class_missing,
                    ]
                );
                // }
            }
            $thumbnail     = '';
            if (OptionIsTrue(SHOW_THUMBNAILS)) {
                $thumbnail = Render::html(
                    'grid/thumbnail',
                    ['PLAYLIST_LINKS' => str_replace('VIDEO_ID', $results[$i]['id'], $playlist_html),
                        'THUMBNAIL'   => $this->fileThumbnail($results[$i]['id']),
                        'ROW_ID'      => $results[$i]['id'],
                        'VIDEO_DATA'  => $videoInfo,
                        'ROWNUM'      => $results[$i]['rownum'],
                        'ROW_TOTAL'   => $totalRecords, ]);
            }
            // THUMB_EXTRA = ' alt="#" class="img-fluid" '
            $cell_html .= Render::html(
                'grid/cell',
                [
                    'FILE_MISSING'   => $class_missing,

                    'PLAYLIST_LINKS' => str_replace('VIDEO_ID', $results[$i]['id'], $playlist_html),
                    'THUMBNAIL'      => $thumbnail,
                    'ROW_ID'         => $results[$i]['id'],
                    'VIDEO_DATA'     => $videoInfo,
                    'ROWNUM'         => $results[$i]['rownum'],
                    'ROW_TOTAL'      => $totalRecords,
                    'STAR_RATING'    => $results[$i]['rating'],
                ]
            );
        }

        $row_html        = Render::html('grid/row', ['ROW_CELLS' => $cell_html]);

        $table_body_html = Render::html('grid/table', [
            'HIDDEN_STUDIO_NAME' => Elements::add_hidden('studio', $studio).Elements::add_hidden('substudio', $substudio),
            'ROWS_HTML'          => $row_html,
            'INFO_NAME'          => $sort_type_map['map'][$_REQUEST['sort']],

        ]);

        return $table_body_html;
    }
}
