<?php
/**
 *  Plexweb
 */
require_once '_config.inc.php';

/**
 * Progress bar for a lengthy PHP process
 * http://spidgorny.blogspot.com/2012/02/progress-bar-for-lengthy-php-process.html.
 */
class ProgressBar
{
    public $percentDone = 0;
    public $pbid;
    public $pbarid;
    public $tbarid;
    public $textid;
    public $decimals = 1;

    public function __construct($percentDone = 0)
    {
        $this->pbid        = 'pb';
        $this->pbarid      = 'progress-bar';
        $this->tbarid      = 'transparent-bar';
        $this->textid      = 'pb_text';
        $this->percentDone = $percentDone;
        utminfo("test");
    }

    public function render()
    {
        // print ($GLOBALS['CONTENT']);
        // $GLOBALS['CONTENT'] = '';
        echo $this->getContent();
        $this->flush();
        // $this->setProgressBarProgress(0);
    }

    public function getContent()
    {
        $this->percentDone = (float) $this->percentDone;
        $percentDone       = number_format($this->percentDone, $this->decimals, '.', '').'%';
        $content = '<div id="'.$this->pbid.'" class="pb_container">
			<div id="'.$this->textid.'" class="'.$this->textid.'">'.$percentDone.'</div>
			<div class="pb_bar">
				<div id="'.$this->pbarid.'" class="pb_before"
				style="width: '.$percentDone.';"></div>
				<div id="'.$this->tbarid.'" class="pb_after"></div>
			</div>
			<br style="height: 1px; font-size: 1px;"/>
		</div>
		<style>
			.pb_container {
				position: relative;
			}
			.pb_bar {
				width: 100%;
				height: 1.3em;
				border: 1px solid silver;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				-moz-border-radius-bottomleft: 5px;
				-moz-border-radius-bottomright: 5px;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-webkit-border-bottom-left-radius: 5px;
				-webkit-border-bottom-right-radius: 5px;
			}
			.pb_before {
				float: left;
				height: 1.3em;
				background-color: #43b6df;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-bottomleft: 5px;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-bottom-left-radius: 5px;
			}
			.pb_after {
				float: left;
				background-color: #FEFEFE;
				-moz-border-radius-topright: 5px;
				-moz-border-radius-bottomright: 5px;
				-webkit-border-top-right-radius: 5px;
				-webkit-border-bottom-right-radius: 5px;
			}
			.pb_text {
				padding-top: 0.1em;
				position: absolute;
				left: 48%;
			}
		</style>'."\r\n";

        return $content;
    }

    public function setProgressBarProgress($percentDone, $text = '')
    {
        $this->percentDone = $percentDone;
        $text              = $text ?: number_format($this->percentDone, $this->decimals, '.', '').'%';
        echo '
		<script type="text/javascript">
		if (document.getElementById("'.$this->pbarid.'")) {
			document.getElementById("'.$this->pbarid.'").style.width = "'.$percentDone.'%";';
        if (100 == $percentDone) {
            echo 'document.getElementById("'.$this->pbid.'").style.display = "none";';
        } else {
            echo 'document.getElementById("'.$this->tbarid.'").style.width = "'.(100 - $percentDone).'%";';
        }
        if ($text) {
            echo 'document.getElementById("'.$this->textid.'").innerHTML = "'.htmlspecialchars($text).'";';
        }
        echo '}</script>'."\n";
        $this->flush();
    }

    public function flush()
    {
        echo str_pad('', (int) ini_get('output_buffering'))."\n";
        //  ob_end_flush();
        flush();
    }
}

echo 'Starting&hellip;<br />';

$p = new ProgressBar();
echo '<div style="width: 600px;">';
$p->render();
echo '</div>';
for ($i = 0; $i < ($size = 100); ++$i) {
    $p->setProgressBarProgress($i * 100 / $size);
    usleep(1000000 * 0.1);
}
$p->setProgressBarProgress(100);

echo 'Done.<br />';

// use Plex\Template\Render;

// require_once '_config.inc.php';

/*
foreach ($test_link as $name => $row) {
    if ('dropdown' != $name) {
        foreach ($row as $key => $value) {
            if (is_bool($value)) {
                $Checked = '';
                if (1 == $value) {
                    $Checked = ' checked ';
                }
                $checkboxes .= Render::return('Settings/form/Navigation/checkbox', ['Name' => $key, 'Checked' => $Checked]);
                continue;
            }
            $cardRows .= Render::return('Settings/form/Navigation/text_row', ['Name' => $key, 'Value' => $value]);
        }

        $cardRows .= Render::return('Settings/form/Navigation/checkbox_row', ['CardCheckbox' => $checkboxes]);
        $CardContent .= Render::return('Settings/form/Navigation/card_text', ['LinkName' => $name, 'CardFormContent' => $cardRows]);
        $cardRows = '';
        $checkboxes = '';
        // break;
    } else {
        foreach ($row as $ddName => $ddUrl) {
            dump($ddName, $ddUrl);
        }
    }
}

$card = Render::return('Settings/form/Navigation/card', ['CardContent' => $CardContent]);
$body = Render::html('Settings/page', ['html' => $card]);
*/

// Render::Display($body);
