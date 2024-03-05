<?php 
namespace Plex\Modules\Process\Traits;

trait DbWrapper
{
    public function query($sql)
    {
        return $this->db->query($sql);
    }

    public function getOne($tableName, $columns = '*')
    {
        return $this->db->getOne($tableName,$columns);
    }
    public function delete($tableName)
    {
        return $this->db->delete($tableName);
    }
    public function insert($table,$data)
    {
        return $this->db->insert($table,$data);
    }

    public function where($field,$value)
    {
        return $this->db->where($field,$value);
    }

    public function update($table,$data)
   {
       return $this->db->update($table,$data);
   }
   public function onDuplicate($data, $field){
    return $this->db->onDuplicate($data, $field);
   }

   public function rawQuery($sql){
    return $this->db->rawQuery($sql);
   }
   public function getLastQuery(){
    return $this->db->getLastQuery();
   }
public function getLastError(){
    return $this->db->getLastError();
}
}