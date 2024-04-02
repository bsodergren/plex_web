<?php

namespace Plex\Modules\Display\Layout;

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Playlist\Playlist;
use Plex\Modules\VideoCard\VideoCard;
use Plex\Modules\Display\VideoDisplay;
use Plex\Template\Functions\Traits\Video;
use Plex\Template\Render;
use UTMTemplate\HTML\Elements;

/**
 * plex web viewer.
 */
class GridDisplay extends VideoDisplay
{
    use Video;
    public $showVideoDetails = false;
    private $template_base = '';
    public $VideoPlaylists = [];

    public function __construct($template_base = 'filelist')
    {
        $db = PlexSql::$DB;

        $this->template_base = $template_base;
        $this->VideoPlaylists = (new Playlist())->showPlaylists(true);
    }

    public function gridPlaylistDropdown($playlists, $video_id)
    {
        $current = [];
        $ids = Playlist::getVideoPlaylists($video_id);
        if (\count($ids) > 0) {
            foreach ($ids as $k => $pl) {
                $current[$pl['playlist_id']] = true;
            }
        }

        foreach ($playlists as $p) {
            if (\array_key_exists($p['id'], $current)) {
                continue;
            }
            $playlist_html .= Render::html(
                'grid/playlist_link',
                [
                    'PL_NAME' => $p['name'],
                    'PL_ID' => $p['id'],
                    'VIDEO_DATA' => 'VIDEO_ID',
                ]
            );
        }

        return $playlist_html;
    }

    public function videoCell($videoInfo)
    {
        $playlist_html = $this->gridPlaylistDropdown($this->VideoPlaylists, $videoInfo['id']);
        $full_filename = $videoInfo['fullpath'].\DIRECTORY_SEPARATOR.$videoInfo['filename'];

        // %%FILE_MISSING%%
        $class_missing = '';
        if (!file_exists($full_filename)) {
            $class_missing = 'bg-danger';
        }

        $file_info = [
            'title' => $videoInfo['title'],
            'library' => $videoInfo['library'],
            'studio' => $videoInfo['studio'],
            'substudio' => $videoInfo['substudio'],
            'artist' => $videoInfo['artist'],
            'genre' => $videoInfo['genre'],
            'added' => $videoInfo['added'],
            // 'filename'  => $videoInfo['filename'],
            'duration' => VideoCard::videoDuration($videoInfo['duration']),

            // 'Duration' => videoDuration($videoInfo['duration']),
        ];
        if ('' != $videoInfo['substudio']) {
            $substudio = $videoInfo['substudio'];
        }
        if ('' != $videoInfo['studio']) {
            $studio = $videoInfo['studio'];
        }
        $videoFields = '';
        foreach ($file_info as $field => $value) {
            // if ($value != '') {
            $videoFields .= Render::html(
                'grid/cell/video_data',
                [
                    'FILE_FIELD' => ucfirst($field),
                    'FILE_INFO' => $value,
                    'FILE_MISSING' => $class_missing,
                ]
            );
            // }
        }
        $thumbnail = '';
        if (OptionIsTrue(SHOW_THUMBNAILS)) {
            $thumbnail = Render::html(
                'grid/cell/thumbnail',
                ['PLAYLIST_LINKS' => str_replace('VIDEO_ID', $videoInfo['id'], $playlist_html),
                    'THUMBNAIL' => $this->fileThumbnail($videoInfo['id']),
                    'ROW_ID' => $videoInfo['id'],
                    'VIDEO_DATA' => $videoFields,
                    'ROWNUM' => $videoInfo['rownum'],
                    'ROW_TOTAL' => $this->totalRecords, ]);
        }

        $params = [
            'FILE_MISSING' => $class_missing,

            'PLAYLIST_LINKS' => str_replace('VIDEO_ID', $videoInfo['id'], $playlist_html),
            'THUMBNAIL' => $thumbnail,
            'ROW_ID' => $videoInfo['id'],
            'VIDEO_DATA' => $videoFields,
            'ROWNUM' => $videoInfo['rownum'],
            'ROW_TOTAL' => $this->totalRecords,
            'STAR_RATING' => $videoInfo['rating'],
        ];

        // THUMB_EXTRA = ' alt="#" class="img-fluid" '
        return Render::html(
            'grid/cell/video', $params
        );
    }

    public function display($results, $page_array = [])
    {
        global $_SESSION,$_REQUEST;
        global $sort_type_map;
        $db = PlexSql::$DB;
        if (isset($page_array['total_files'])) {
            $this->totalRecords = $page_array['total_files'];
        }

        if (isset($page_array['redirect_string'])) {
            $redirect_string = $page_array['redirect_string'];
        }

        $total = \count($results);
        if (0 == $total) {
            return 'No results';
        }

        foreach ($results as $k => $videoRow) {
            $cell_html .= Render::html('grid/cell', [
                'VIDEO_CELL' => $this->videoCell($videoRow),
                'ROW_ID' => $videoRow['id']]
            );
        }

        $table_body_html = Render::html('grid/table', [
            'HIDDEN_STUDIO_NAME' => Elements::add_hidden('studio', $studio).Elements::add_hidden('substudio', $substudio),
            'ROWS_HTML' => $cell_html,
            'INFO_NAME' => $sort_type_map['map'][$_REQUEST['sort']],
        ]);

        return $table_body_html;
    }
}
