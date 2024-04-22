<?php

namespace Plex\Template\Functions\Traits;

use Plex\Template\Render;
use Plex\Modules\Database\PlexSql;

trait TagCloud
{


    private  function getKeywordSQL($table,$field,$where = '')
    {


       $sql = 'SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX('.$field;
       $sql .= ", ',', n.digit+1), ',', -1) val FROM ".$table;
       $sql .= ' INNER JOIN (SELECT 0 digit UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6) n';
       $sql .= ' ON LENGTH(REPLACE('.$field.", ',' , '')) <= LENGTH(".$field.')-n.digit '.$where.' ORDER BY `val` ASC';
                 return $sql;
    }
    private  function getKeywordList($field = 'keyword')
    {
         $db = PlexSql::$DB;
        $where = PlexSql::getLibrary();
        $where = str_replace('AND', 'WHERE', $where);
        $where = str_replace('m.library', 'library', $where);
        $sql_meta =$this->getKeywordSQL(Db_TABLE_VIDEO_TAGS,$field,$where);
        $sql_custom = $this->getKeywordSQL(Db_TABLE_VIDEO_CUSTOM,$field);

        // $sql = "SELECT DISTINCT m.genre,c.genre FROM '.Db_TABLE_VIDEO_CUSTOM.' c, '.Db_TABLE_VIDEO_TAGS.' m WHERE (m.genre is not null and c.genre is not null) and  m.Library = 'Studios'";
        $qlist_meta = $db->query($sql_meta);
        $qlist_custom = $db->query($sql_custom);
        return array_merge($qlist_custom, $qlist_meta);

    }
    public function tagCloud($matches)
    {

        $var = $this->parseVars($matches);
        $field = $var['field'];




        // foreach ($qlist as $k => $val) {
        //     $tagArray[] = $val['genre'];
        // }
        // $tagArray = array_unique($tagArray, \SORT_STRING);
        // $key = '';
        // utmdd([$tagArray]);

        // foreach ($tagArray as $v => $value) {
        //     $vArray = explode(',', $value);

        //     foreach ($vArray as $x => $val) {
        //         $val = trim($val);
        //         if ('' != $val) {
        //             if ($key == $val) {
        //                 // utmdd([$val,$key]);
        //                 continue;
        //             }
        //             $list[] = $val;
        //             $key = $val;
        //         }
        //     }
        // }

        $list = $this->getKeywordList($field);
        // utmdd(\count($list));
        $tag_links = '';
        if (0 == \count($list)) {
            return false;
        }

        if (\is_array($list)) {
            foreach ($list as $key => $keyword) {
                if ('' != $keyword['val']) {
                    $list_array[] = $keyword['val'];
                }
            }
        } else {
            $list_array = explode(',', $list);
        }
        $list_array = array_unique($list_array);

        foreach ($list_array as $k => $keyword) {
            $letter = substr($keyword, 0, 1);
            if (!isset($last_letter)) {
                $last_letter = $letter;
            }
            if ($letter != $last_letter) {
                $last_letter = $letter;
                // $link_array[] = '</div>    <div class="'.__TAG_CAT_CLASS__.' ">';
                // $index=0;
            }
            $keyword_array[$last_letter][] = $keyword;
            // if ($max <= $index) {
            //     $link_array[] = '</div>    <div class="">';
            //     $index=0;
            // }
            // $index++;
            // $link_array[] = Render::html(
            //     'cloud/tag',
            //     [
            //         'KEY'      => $field,
            //         'QUERY'    => urlencode($keyword),
            //         'URL_TEXT' => $keyword,
            //         // 'CLASS'    => ' badge fs-6 blueTable-thead ',
            //     ]
            // );
        }
        $max = 10;
        $keyword_box_class = '<div class="">';
        foreach ($keyword_array as $letter => $keywordArray) {
            $index = 0;
            $total = \count($keywordArray);
            if ($total >= $max) {
                $link_array[] = $keyword_box_class;
            }
            foreach ($keywordArray as $k => $keyword) {
                if ($max <= $index) {
                    $link_array[] = '</div>'.$keyword_box_class;
                    $index = 0;
                }
                ++$index;
                $link_array[] = Render::html(
                    'pages/Tags/tag',
                    [
                        'KEY' => $field,
                        'QUERY' => urlencode($keyword),
                        'URL_TEXT' => $keyword,
                        // 'CLASS'    => ' badge fs-6 blueTable-thead ',
                    ]
                );
            }
            if ($total >= $max) {
                $link_array[] = '</div>';
            }

            $link_array[] = '</div>    <div class="'.__TAG_CAT_CLASS__.' ">';
        }

        $tag_links = implode('  ', $link_array);
        //  return $value;

        return $tag_links;
    }
}
