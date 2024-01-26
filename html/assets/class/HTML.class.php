<?php
/**
 * plex web viewer
 */

include_once __PHP_INC_CLASS_DIR__.'/Render.class.php';
include_once __PHP_INC_CLASS_DIR__.'/Template.class.php';

class HTML_Func extends Render
{
    public function __construct() {}

    public function hiddenSearch()
    {
        if(FileListing::$searchId === null){
            return '';
        }

        return add_hidden("search_id", FileListing::$searchId );
    }

    private function parseVars($matches)
    {
        $parts = explode(',', $matches[2]);
        foreach ($parts as $value) {
            if (str_contains($value, '=')) {
                $v_parts             = explode('=', $value);
                if (str_contains($v_parts[0], '?')) {
                    $q_parts                      = explode('?', $v_parts[0]);

                    $values['query'][$q_parts[1]] = $v_parts[1];
                    continue;
                }
                $values[$v_parts[0]] = $v_parts[1];
                continue;
            }
            $values['var'][] = $value;
        }

        return $values;
    }

    public function breadcrumbs($match)
    {
        return Render::display_breadcrumbs();
    }

    public function videoRating($matches)
    {
        $var = $this->parseVars($matches);

        return process_template('elements/Rating/rating', ['ROW_ID' => $var['id'], 'STAR_RATING' => $var['rating']]);
    }

    public function ratingInclude($matches)
    {
        return process_template('elements/Rating/header', []);
    }

    public function AlphaBlock($match)
    {
        return AlphaSort::display_AlphaBlock();
    }

    public function videoPlayer($matches)
    {
        $var    = $this->parseVars($matches);

        if (is_array($var['query'])) {
            $req = '?'.http_build_query($var['query']);
        }

        $window = basename($var['href'], '.php').'_popup_'.Display::$Random;
        $url    = __URL_HOME__.'/'.$var['href'].$req;

        return " onclick=\"popup('".$url."', '".$window."')\"";
    }

    public function videoButton($matches)
    {
        $var = $this->parseVars($matches);
        if (array_key_exists('pl_id', $var)) {
            if ('' == $var['pl_id']) {
                return '';
            }
        }

        return process_template('video/buttons/'.$var['template'], []);
    }

}
