<?php


class VideoInfo
{
    public $tagValue;

    public function __construct()
    {

    }

    public function __call($method, $args)
    {
        $classMethod = "update".$method;
        $this->tagValue = $args[0];

        if(method_exists($this,$classMethod)){
            $this->$classMethod();
        }
    }

    public function updateTitle()
    {


    }

    public function save($tag,$data)
    {

        dump($tag,$data);

    }


}