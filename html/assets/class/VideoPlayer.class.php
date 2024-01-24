<?php
/**
 * plex web viewer
 */

include_once __PHP_INC_CLASS_DIR__.'/Render.class.php';
include_once __PHP_INC_CLASS_DIR__.'/Template.class.php';

class VideoPlayer
{
    private $container = '';
    public $html       = '';
    public $min        = '';

    public function __construct($container='', $useMin = false)
    {
        $this->container = $container;
        if (true == $useMin) {
            $this->min = '.min';
        }
    }

    private function getUrl($file,$type, $useMin)
    {
        $file = basename($file, $type);

        $min  = $this->min;
        if (true == $useMin) {
            $min = '.min';
        }

        return __URL_HOME__.'/node_modules/'.$this->container.'/dist/'.$file.$min.$type;

    }
    public function javascript($js_file, $useMin = false)
    {
        $url = $this->getUrl($js_file,".js",$useMin);
        $this->html .= Template::GetHTML('elements/script', ['JAVASCRIPT_FILE' => $url]);
    }

    public function stylesheet($css_file, $useMin = false)
    {
        $url = $this->getUrl($css_file,".css",$useMin);


        $this->html .= Template::GetHTML('elements/stylesheet', ['CSS_FILE' => $url]);
    }

    public function render()
    {
        return $this->html;
    }

    public static function playlistVideo($url,$title,$thumbnail)
    {

return Template::GetHTML('testVideo/playlist_item', ['VIDEO_URL' => $url,'TITLE' => $title, 'THUMBNAIL'=>$thumbnail]);


    }

}
