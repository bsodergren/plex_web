<?php
/**
 * plex web viewer
 */

/**
 * Command like Metatag writer for video files.
 */
// function query_builder($fields = 'select', $where = false, $group = false, $order = false, $limit = false, $offset = false)
// {
//     global $_SESSION;
//     $conditional = false;
//     $field_list  = ' id, video_key,thumbnail,title,artist,genre,studio,keyword,substudio,duration,favorite,added,filename ,fullpath,library,filesize';

//     if ('select' == $fields) {
//         $conditional = true;
//         if (false != $limit || false != $offset) {
//             $sql = ' select (@row_num:=@row_num +1) AS result_number, '.$field_list;
//             $sql = $sql.' from ( select '.$field_list;
//             //  $sql = ' select ' . $field_list;
//         } else {
//             $sql =  'select '.$field_list;
//         }
//     } else {
//         if (!str_contains($fields, 'DISTINCT')) {
//             $conditional = true;
//         }
//         $sql = 'SELECT '.$fields;
//     }

//     $sql         = $sql.' from '.Db_TABLE_VIDEO_FILE.' ';

//     if ('library' == $where) {
//         $sql = $sql." WHERE library = '".$_SESSION['library']."' ";
//     } else {
//         if (true == $conditional || false != $where) {
//             $sql = $sql." WHERE library = '".$_SESSION['library']."' ";
//         }

//         if (false != $where) {
//             $sql = $sql.' AND  '.$where.'  ';
//         }
//     }
//     if (false != $group) {
//         $sql = $sql.' GROUP BY '.$group;
//     }

//     if (false != $order) {
//         $sql = $sql.' ORDER BY '.$order;
//     }

//     if (false != $limit && false == $offset) {
//         //   $sql = $sql . " LIMIT " . $limit.' ';

//         $sql = $sql.' LIMIT '.$limit.' ) t1 ';
//         $sql = $sql.', (select @row_num:='.$offset.') t2';
//     }

//     if (false != $limit && false != $offset) {
//         //     $sql = $sql . "  LIMIT " . $offset . ", " . $limit .'';

//         $sql = $sql.'  LIMIT '.$offset.', '.$limit.') t1,';
//         $sql = $sql.' (select @row_num:='.$offset.') t2';
//     }

//     // logger("SQL Builder", $sql);
//     // dump($sql);

//     dump($sql);
//     return $sql;
// }
// // class studios extends dbObject {
// //    protected $dbTable = "home_vid";
// // }
