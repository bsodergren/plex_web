<?php

namespace Plex\Template;

use Plex\Modules\Display\Layout;
use UTMTemplate\Render as UTMTemplateRender;

class Render extends UTMTemplateRender
{
    // public function __construct() {}

    public static function Display($array = '')
    {
        Layout::Header();
        self::echo('base/page', ['BODY' => $array]);
        Layout::Footer();
    }


	var $percentDone = 0;
	var $pbid;
	var $pbarid;
	var $tbarid;
	var $textid;
	var $decimals = 1;

	function __construct($percentDone = 0) {
		$this->pbid = 'pb';
		$this->pbarid = 'progress-bar';
		$this->tbarid = 'transparent-bar';
		$this->textid = 'pb_text';
		$this->percentDone = $percentDone;
	}

	function render() {
		//print ($GLOBALS['CONTENT']);
		//$GLOBALS['CONTENT'] = '';
		print($this->getContent());
		$this->flush();
		//$this->setProgressBarProgress(0);
	}

	function getContent() {
		$this->percentDone = floatval($this->percentDone);
		$percentDone = number_format($this->percentDone, $this->decimals, '.', '') ;

       $content .= Render::html(
            'elements/ProgressBar/progressbar',
            [
                'pbid' => $this->pbid,
                'textid' => $this->textid,
                'percentDone' => $percentDone.'%',
                'BootpercentDone' => $percentDone,
                'pbarid' => $this->pbarid,
                'tbarid' => $this->tbarid
            ]
        );

        return $content;

	}

    function setProgressBarHeader($text){
        print('<script type="text/javascript">');
        print('document.getElementById("downloadText").innerHTML = "'.htmlspecialchars($text).'";');
        	print("\n".'</script>'."\n");
		$this->flush();
    }
	function setProgressBarProgress($percentDone, $text = '') {

        // utmdump([__METHOD__,$percentDone]);
		$this->percentDone = $percentDone;
		$text = $text ? $text : number_format($this->percentDone, $this->decimals, '.', '');


        echo Render::html(
            'elements/ProgressBar/javascript',
            [
                'pbid' => $this->pbid,
                'textid' => $this->textid,
                'text' => $text."%",
                'percentNumber'=> $text,
                'percentDone' => $percentDone.'%',
                'stylepercentDone' => $percentDone.'%',
                'pbarid' => $this->pbarid,
                'tbarid' => $this->tbarid
            ]
        );



		$this->flush();
	}

	function flush() {
		print str_pad('', intval(ini_get('output_buffering')))."\n";
		//ob_end_flush();
		flush();
	}



}
