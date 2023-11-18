<?php
namespace Plexweb\Utilities;
/**
 * Command like Metatag writer for video files.
 */

use Nette\Utils\Arrays;
use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Plexweb\Utilities\Colors;


class Display
{
    private $model_popup   = '';
    private $model_buttons = [];

    public static function echo($text, $var = '')
    {
        $colors = new Colors();
        /*
        if (defined('__DISPLAY_POPUP__')) {
            global $model_display;
            $model_display->model($text, $var);
            return 0;
        }
    */

        $pre_style = 'style="border: 1px solid #ddd;border-left: 3px solid #f36d33;color: #666;page-break-inside: avoid;font-family: monospace;font-size: 15px;line-height: 1.6;margin-bottom: 1.6em;max-width: 100%;overflow: auto;padding: 1em 1.5em;display: block;word-wrap: break-word;"';
        $div_style = 'style="display: inline-block;width: 100%;border: 1px solid #000;text-align: left;font-size:1.5rem;"';
        $is_array  = false;

        if (is_array($text)) {
            $var  = $text;
            $text = 'Array';
        }

        if (is_array($var)) {
            $var      = var_export($var, 1);
            $var      = $colors->getColoredHTML($var, 'green');
            $var      = "<pre $pre_style>".$var.'</pre>';
            $is_array = true;
        } else {
            $var = $colors->getColoredHTML($var, 'green');
        }

        $text      = $colors->getColoredHTML($text);

        echo "<div $div_style>".$text.' '.$var."</div><br>\n";
    }

    public function model($text, $var = '')
    {
        $pre_style             = 'style="border: 1px solid #ddd;border-left: 3px solid #f36d33;color: #666;page-break-inside: avoid;font-family: monospace;font-size: 15px;line-height: 1.6;margin-bottom: 1.6em;max-width: 100%;overflow: auto;padding: 1em 1.5em;display: block;word-wrap: break-word;"';

        $colors = new Colors();
        $is_array              = false;

        if (is_array($text)) {
            $var  = $text;
            $text = 'Array';
        }

        if (is_array($var)) {
            $var      = var_export($var, 1);
            // $var=$colors->getColoredHTML($var, "green");
            $var      = "<pre $pre_style>".$var.'</pre>';
            $is_array = true;
        }

        // else {
        //    $var = $colors->getColoredHTML($var, "green");
        // }
        // $text=$colors->getColoredHTML($text);

        $random_id             = 'Model_'.substr(md5(rand()), 0, 7);
        $this->model_popup .= process_template('popup_debug_model', ['MODEL_TITLE' => $text, 'MODEL_BODY' => $var, 'MODEL_ID' =>  $random_id]);

        $button_html           = process_template('popup_debug_button', ['MODEL_TITLE' => $text, 'MODEL_ID' =>  $random_id]);
        $this->model_buttons[] = $button_html;
    }

    public function writeModelHtml()
    {
        if (defined('__DISPLAY_POPUP__')) {
            echo $this->model_popup;
            echo '      <div class="btn-group-vertical">';

            foreach ($this->model_buttons as $k => $html_button) {
                echo $html_button;
            }

            echo '</div>';
        }
    }
}
