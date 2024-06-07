<?php

namespace Plex\Modules\Process\Traits;

trait VideoPlayer
{
    public function nextVideo()
    {
        $videoid = $this->postArray['videoid'];
        $this->db->where('Library', $this->library );
        $search_data = $this->db->get(Db_TABLE_VIDEO_FILE,null, ['id']);
        $next = false;
        foreach($search_data as $k => $row){
            if($next === true) {
                $nextVideoId = $row['id'];
                $next = false;
                continue;
            }
            if($row['id'] == $videoid){
                $next = true;
                continue;
            }
        }
        echo  __URL_HOME__.'/video.php?id='.$nextVideoId.'';
        exit;
    }

    public function nextVideoCard()
    {
        $videoid = $this->postArray['videoid'];

        $this->db->where ("video_list", '%'.$videoid.'%', 'like');

        $search_data = $this->db->get(Db_TABLE_SEARCH_DATA,null, ['video_list']);
        $search_array = explode(",",$search_data[0]['video_list']);
        $newArray = [];

        $test = $search_array;

        foreach ($test as $index => $row) {
            if ($row == $videoid) {
                break;
            }
            $last = array_shift($test);
            $newArray[] = $last;
        }

        $results = array_merge($test, $newArray);

        $nextVideoId = next($results);

        echo  __URL_HOME__.'/videoinfo.php?id='.$nextVideoId.'';
        exit;
    }
}
