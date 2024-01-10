<?php
/**
 * plex web viewer
 */

class HTML_Func
{
    private function parseVars($matches)
    {
        $parts = explode(',', $matches[2]);
        foreach ($parts as $value)
        {
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

        dump($values);
        return $values;
    }

    public function breadcrumbs($match)
    {
        return Render::display_breadcrumbs();
    }

    public function AlphaBlock($match)
    {
        return AlphaSort::display_AlphaBlock();
    }
    public function videoPlayer($matches)
    {
        $var = $this->parseVars($matches);

        if (is_array($var['query'])) {
            $req = '?'.http_build_query($var['query']);
        }

        $url = __URL_HOME__.'/'.$var['href'].$req;

        return " onclick=\"popup('".$url."', 'videoplayer',1100,725)\"";
    }



    public function videoButton($matches)
    {
        $var = $this->parseVars($matches);
        if(array_key_exists('pl_id',$var))
        {
            if($var['pl_id'] == ''){
                return '';
            }
        }

        return process_template('video/buttons/'.$var['template'], []);
        
    }
}
