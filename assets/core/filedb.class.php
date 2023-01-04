<?php

use Nette\Utils\FileSystem;
use Mhor\MediaInfo\MediaInfo;

class filedb
{


    public $filename;
    public $file;
    public $filepath;
    private $db;

    public $video_key_file;
    public $video_key;
    public $library;
    public $videoInfo=[];
    public $vidInfo;

    public function __construct($file)
    {
        global $db;
        global $_SESSION;
        $this->library = $_SESSION['library'];
        $this->db = $db;
        $this->file = $file;
        $this->filename = basename($file);
        $this->filepath = pathinfo($this->file,PATHINFO_DIRNAME)."/";

       
        

       $res= $this->getVideoKeyFile();
       
        $this->getKey();
    }

    private function initMediaClass()
    {
        $mediaInfo = new MediaInfo();
        $mediaInfoContainer = $mediaInfo->getInfo($this->file );
        $this->general = $mediaInfoContainer->getGeneral();
        
        
        $videoInfo = $mediaInfoContainer->getVideos();
        $this->vidInfo = $videoInfo[0];
    }

    public function addVideo()
    {
        if ($this->exists() == 1 )
        {
            return 0;
        }
        
        $data =[
            'video_key' => $this->video_key,
            'library' => $this->library,
            'added' => $this->db->now(),
            'filename' =>$this->filename,
            'fullpath'=>$this->filepath,
            'new' => 1
        ];

        $id = $this->db->insert (Db_TABLE_FILEDB, $data);
        return $id;
       // $this->getVideoInfo();

    }


    public function updateTag( array $data )
    {
        

        $this->db->where('video_key', $this->video_key);
        $this->db->update(Db_TABLE_FILEDB, $data);
    }

    private function getVideoKeyFile()
    {
        return $this->video_key_file = __METADB_HASH . "/" . $this->filename;
    }

    public function getKey()
    {

       if (file_exists($this->video_key_file)) {
            $this->video_key = FileSystem::read($this->video_key_file);
            if(strlen($this->video_key) == 33){
                return $this->video_key;
            }

            filesystem::delete($this->video_key_file);
       }
       
       
        
        $this->video_key =  md5($this->filename);
        $this->video_key = "x" . $this->video_key;
        FileSystem::write($this->video_key_file,$this->video_key );
        return $this->video_key;
    }

    public function exists()
    {
        $sql_query = "SELECT count(*) as cnt FROM ".Db_TABLE_FILEDB." WHERE video_key = '".$this->video_key."'";
        $state = $this->db->rawQueryOne($sql_query);
        return $state["cnt"];
        
    }


    public function getVideoInfo()
    {

        $this->initMediaClass();
        

        $this->videoInfo['bit_rate'] = $this->vidInfo->get('bit_rate')->getAbsoluteValue();
        $this->videoInfo['height'] = $this->vidInfo->get('height')->getAbsoluteValue();
        $this->videoInfo['width'] = $this->vidInfo->get('width')->getAbsoluteValue();
        $this->videoInfo['duration'] = $this->vidInfo->get('duration')->getMilliseconds();

        $this->videoInfo['filesize'] = $this->general->get('file_size')->getBit();
    }

    public function deleteVideo($id='')
    {
        if($id == '' ){
            
            $where = " video_key = '".$this->video_key."'";
        } else {
            $where = " id = ".$id."";
        }

        $sql_query = "DELETE FROM ".Db_TABLE_FILEDB." WHERE ".$where;
        $state = $this->db->rawQueryOne($sql_query);
        return $state;

    }
}
