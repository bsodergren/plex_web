<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");



$url = 'https://www.pornhub.com/view_video.php?viewkey=ph635027ac9ce44';


use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

$httpClient = new \GuzzleHttp\Client();
$response = $httpClient->get($url);
$htmlString = (string) $response->getBody();


//add this line to suppress any warnings
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($htmlString);
$xpath = new DOMXPath($doc);

$start = stripos($htmlString,"var flashvars_")+4;
$substr = substr($htmlString,$start);

$end = stripos($substr," =");
$flashvar_name = substr($substr,0,$end );

$end = stripos($substr,";");
$text = substr($substr,0,$end );
$text=str_replace($flashvar_name." = ","",$text);
 $flashobj = json_decode($text,true);
 
 
 
 $total = count($flashobj['mediaDefinitions']);
 
 foreach($flashobj as $key => $value )
 { 
		if(!is_array($value)){
			echo $key . " => " . $value . PHP_EOL;
		}
}

/* 
 
 for ($i=0; $i < $total; $i++)
 {
	$array = $flashobj['mediaDefinitions'][$i];
	if ( $array['format'] == "hls" ){
		break;
	}
 }*
 */
 
// var_export($r_array);
?>