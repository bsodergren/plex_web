<?php 
namespace Plex\Template\Functions\Traits;

use Plex\Template\Render;


trait Icons 
{
    private $IconsDir = 'elements/Icons';

    public $iconList = [];

    private function returnMatch($key)
    {
        $var = $this->parseVars($this->IconMatches);
        if(array_key_exists($key,$var)){
            return $var[$key];
        } 
        return false;
    }

    private function getColor($key='color')
    {
        $color = $this->returnMatch($key);
        if($color === false){
            return 'Red';
        }
      
        return $color;
    }

    private function useCSS()
    {
        $key = 'UseCSS';
        $var = $this->parseVars($this->IconMatches);

        if(array_key_exists($key,$var)){
            return $var[$key];
        } 
        return false;
    }

    private function getIconClass($icon){
       
        $className = $this->returnMatch('className');
        if($className !== false)
        {
            $className = "_".$className;
        }
        return $icon."Class".$className ;
    }

    private function getIconStyle(){
        $color = $this->getColor();
        return "style='fill:".$color."'";
    }

    private function getIconCSS($icon){
        $class = $this->getIconClass($icon);
        $color = $this->getColor();
        return Render::html($this->IconsDir.'/css',['Color' => $color,'Class'=>$class]);
    }
    private function getIcon($icon, $matches){
        $this->IconMatches = $matches;

        if($this->useCSS() !== false){
                $class = $this->getIconClass($icon);
                $Style = $this->getIconStyle($icon);
                $CSS = $this->getIconCSS($icon);
        }
        // $this->IconMatches = [];
        return Render::html($this->IconsDir.'/'.$icon,['ICONSTYLE' => $Style,'Class'=>$class,'CSS'=>$CSS]);

    }
}