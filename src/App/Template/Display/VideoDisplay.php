<?php
namespace Plex\Template\Display;

class VideoDisplay
{
    public function __construct($display = 'List')
    {
        $this->class = 'Plex\\Template\\Display\\'.$display.'Display';
    }


    public function init($template_base = 'filelist')
    {
        return new $this->class($template_base);
    }

    public function fileThumbnail($row_id, $extra = '')
    {
        global $db;
        $query  = 'SELECT thumbnail FROM metatags_video_file WHERE id = '.$row_id;
        $result = $db->query($query);
        if (defined('NOTHUMBNAIL')) {
            return null;
        }

        return __URL_HOME__.$result[0]['thumbnail'];
        //  return __URL_HOME__.'/images/thumbnail.php?id='.$row_id;
    }

    
    public function filePreview($row_id, $extra = '')
    {
        global $db;
        $query  = 'SELECT preview FROM metatags_video_file WHERE id = '.$row_id;
        $result = $db->query($query);

        if (defined('NOTHUMBNAIL')) {
            return null;
        }
        if($result[0]['preview'] === null){
            return null;
        }

        return __URL_HOME__.$result[0]['preview'];
        //  return __URL_HOME__.'/images/thumbnail.php?id='.$row_id;
    }
    
}
