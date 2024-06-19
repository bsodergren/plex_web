<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Config;

use Plex\Template\Render;
use UTMTemplate\HTML\Elements;

class StudioConfigSave
{
    public static function displayAddStudioForm($name, $text, $redirect_string)
    {
        $hidden       = Elements::add_hidden('library', $name);
        $hidden .= Elements::add_hidden('submit', 'addNewEntry');
        $hidden .= Elements::add_hidden('redirect', $redirect_string);

        $studio_add_entry = Render::html('config/studio/studio_row', ['STUDIO_ID' => 'studio', 'PATH_ID' => 'path', 'STUDIO_NAME' => '<input type="text" name="name">']);
        $studio_add_entry = Render::html('config/studio/studios', ['STUDIO_LIBRARY' => $text, 'STUDIO_ROWS' => $studio_add_entry]);
        $studio_main_html = Render::html('config/studio/form_wrapper', ['HIDDEN' => $hidden, 'STUDIO_FORM_HTML' => $studio_add_entry]);

        return $studio_main_html;
    }
}
