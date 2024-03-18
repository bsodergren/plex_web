<?php

use Plex\Template\Render;
use Plex\Template\Display\Display;
use Plex\Template\Functions\Functions;

define('__SHOW_SORT__', true);
define('TITLE', 'Home');
define('NONAVBAR', true);
define('VIDEOINFO', true);
define('SHOW_RATING', true);
require_once '_config.inc.php';

use Spatie\Url\Url;
use Symfony\Component\Yaml\Yaml;

$url = Url::fromString($_SERVER['REQUEST_URI']);
$newLink = Url::create()
->withScheme($_SERVER['REQUEST_SCHEME'])
->withHost($_SERVER['SERVER_NAME'])
->withPath($url->getpath())
->withQuery($url->getQuery());

dump($url->getQuery());
// echo $url->getPath();

echo $newLink;
//$url->withoutQueryParameter('Studio'); // 'https://spatie.be/opensource?utm_source=github'
//echo $url->withQueryParameters(['utm_campaign' => 'packages']); 

$test_link =Yaml::parseFile(__ROUTE_NAV__,YAML::PARSE_CONSTANT);









foreach ($test_link as $name => $row) {
    if ('dropdown' != $name) {
        foreach ($row as $key => $value) {
            if (is_bool($value)) {
                $Checked = '';
                if (1 == $value) {
                    $Checked = ' checked ';
                }
                $checkboxes .= Render::return('Settings/form/Navigation/checkbox',['Name' => $key, 'Checked' => $Checked]);
                continue;
            }
            $cardRows .= Render::return('Settings/form/Navigation/text_row', ['Name' => $key, 'Value' => $value]);
        }

        $cardRows .= Render::return('Settings/form/Navigation/checkbox_row',['CardCheckbox' => $checkboxes]);
        $CardContent .= Render::return('Settings/form/Navigation/card_text',['LinkName' => $name, 'CardFormContent' => $cardRows]);
        $cardRows = '';
        $checkboxes = '';
        // break;
    } else {
        foreach($row as $ddName => $ddUrl){
            dump($ddName,$ddUrl);
        }
    }

}

$card = Render::return('Settings/form/Navigation/card',   ['CardContent' => $CardContent]);
$body = Render::html('Settings/page', ['html'=>$card]);

Render::Display($body);
