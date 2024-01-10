<?php 


include_once __PHP_INC_CLASS_DIR__."/Render.class.php";
include_once __PHP_INC_CLASS_DIR__."/Template.class.php";

class AlphaSort extends Render
{
    public static function display_alphaSort($offset = 0, $len = 13)
    {
        global $url_array;
        global $tag_types;
        $sep         = '&';
        if ('' != $url_array['query_string']) {
            parse_str($url_array['query_string'], $query_parts);

            $current     = 'studio';

            if (isset($url_array['direction'])) {
                $query_parts['direction'] = $url_array['direction'];
            }

            if (!isset($query_parts['sort'])) {
                $query_parts['sort'] = 'm.title';
            }

            $sort        = $query_parts['sort'];

            $tag_string  = implode(',', $tag_types);
            $f           = explode('.', $sort);
            if (!str_contains($tag_string, $f[1])) {
                $sort = 'm.title';
            }

            // unset($query_parts['sort']);
            if (isset($query_parts['alpha'])) {
                $current = $query_parts['alpha'];
                unset($query_parts['alpha']);
            }
            $request_uri = http_build_query($query_parts);
          
        }


        if($sort == ''){
            $sort = $url_array['current'];

        }
        
        $request_string = $request_uri.'sort='.$sort;


        $i              = 0;

        $chars          = range('A', 'Z');
        $charrange      = array_merge(['#'], $chars, ['None', 'All']);

        $range          = array_slice($charrange, $offset, $len);
        $max            = count($range);

        // $params['NAME']    = 'alpha';

        // $params['OPTIONS'] = self::display_SelectOptions($range, $current);

        // return process_template('base/navbar/select/select_box', $params);

        foreach ($range as $char) {
            $bg    = 'btn-primary ';
            $pill  = '';
            if (0 == $i) {
                $pill = ' rounded-start-pill';
            }
            ++$i;
            if ($i == $max) {
                $pill = ' rounded-end-pill';
            }

            if ($current == $char) {
                $bg = ' btn-secondary ';
            }
            $class = 'btn btn-sm '.$bg.$pill;
            $url   = $url_array['url'].'?alpha='.urlencode($char).$sep;
            $html .=
            parent::display_directory_navlinks($url, $char, $request_string, $class, 'role="button" aria-pressed="true"  ');
        }

        return $html;
    }

    public static function display_AlphaRow()
    {
     

        $alpha_sort  = self::display_alphaSort(0, 15);
        $alpha_end   = self::display_alphaSort(15, 20);
        return process_template('elements/AlphaSort/row', [
            'ALPHA_BLOCK_START'                                          => $alpha_sort,
            'ALPHA_BLOCK_END'                                            => $alpha_end]);
       
    }

    public static function display_AlphaBlock()
    {
            if (defined("ALPHA_SORT") == true) {

            return  process_template('elements/AlphaSort/block',['ALPHA_BLOCK' => self::display_AlphaRow()]);
        }
        return '';
    }

}