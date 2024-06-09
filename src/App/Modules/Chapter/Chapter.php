<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Chapter;

use Plex\Modules\Database\PlexSql;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;

class Chapter
{
    public object $db;

    public $data;
    public $library;
    public $playlist_id;
    public $id;
    public $chapterIndex;
    private $displayVideo = "true";

    public function __construct($data)
    {
        global $_SESSION;
        utmdump($data);
        $this->data    = $data;
        $this->db      = PlexSql::$DB;
        $this->library = $_SESSION['library'];
        if (isset($data['playlist_id'])) {
            $this->playlist_id = $data['playlist_id'];
        }
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['video'])) {


            // if($data['video'] == "false" ) {
            //     $data['video'] = false;
            // }
            $this->displayVideo = $data['video'];

        }

        utmdump($data);
    }

    public function getChapterJson()
    {
        return json_encode($this->Chapters->getChapters());
    }

    public function getChapters()
    {
        if (null == $this->chapterIndex) {
            $this->db->where('video_id', $this->id);
            $this->db->orderBy('timeCode', 'ASC');
            $search_result = $this->db->get(Db_TABLE_VIDEO_CHAPTER);
            foreach ($search_result as $i => $row) {
                if (null === $row['name']) {
                    $row['name'] = 'Timestamp';
                }

                $this->chapterIndex[] = [
                    'time' => $row['timeCode'],
                    'label' => $row['name'],
                    'chapterId' => $row['id'],
                ];
            }
        }

        return $this->chapterIndex;
    }

    public function getChapterButtons()
    {
        $html  = '';
        $index = $this->getChapters();
        if (null === $index) {
            return '';
        }
        foreach ($index as $i => $row) {
            // $editableClass = 'edit'.$row['time'];
            // $functionName  = 'make'.$row['time'].'Editable';

            $row['ChapterId'] =  $row['chapterId'];
            $row['videoId'] =  $this->id;
            $row['javascript'] = '';

            $row['DisplayVideo'] = $this->displayVideo ;

            if( $this->displayVideo  == "true")
            {
                $row['javascript'] = ' onclick="seektoTime('. $row['time'].')" ';

            }
            // $row['VIDEOINFO_EDIT_JS'] = Render::javascript(
            //     Functions::$ChapterDir.'/chapter',
            //     [
            //         'ChapterId'   => $row['chapterId'],
            //         'EDITABLE'  => $editableClass,
            //         'FUNCTION'  => $functionName,
            //         'VIDEO_KEY' => $this->id,
            //     ]
            // );
            $html .= Render::html(Functions::$ChapterDir.'/chapterButton', $row);
        }

        $buttonHtml = Render::html(Functions::$ChapterDir.'/chapterButtons', [ 'ChapterButtons' => $html]);
        return $buttonHtml;

    }
    public function displayChapters()
    {
        $html = $this->getChapterButtons();
        return Render::html(Functions::$ChapterDir.'/chapter', [ 'ChapterButton' => $html]);
    }
}
