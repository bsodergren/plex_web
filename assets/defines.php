<?php

if (!defined('APP_NAME'))                       define('APP_NAME', 'Plex Media Import');
if (!defined('APP_ORGANIZATION'))               define('APP_ORGANIZATION', 'KLiK');
if (!defined('APP_OWNER'))                      define('APP_OWNER', 'bjorn');
if (!defined('APP_DESCRIPTION'))                define('APP_DESCRIPTION', 'Embeddable PHP Login System');
if (!defined('Db_TABLE_FILEDB'))                   	define('Db_TABLE_FILEDB', Db_TABLE_PREFIX."filedb");
if (!defined('Db_TABLE_STUDIO'))                   	define('Db_TABLE_STUDIO', Db_TABLE_PREFIX."studios");
if (!defined('Db_TABLE_ARTISTS'))                   define('Db_TABLE_ARTISTS', Db_TABLE_PREFIX."artists");
if (!defined('__SCRIPT_NAME__'))			    DEFINE("__SCRIPT_NAME__", "error");
if (!defined('__ERROR_LOG_DIR__'))			    DEFINE("__ERROR_LOG_DIR__", APP_PATH."/logs");


$logfile_name="plexweb.log";
if (__HTML_POPUP__ == true) $logfile_name="plexweb.html.log";

if (!defined('__ERROR_FILE_NAME__'))            DEFINE("__ERROR_FILE_NAME__", $logfile_name);
if (!defined('ERROR_LOG_FILE'))                 DEFINE("ERROR_LOG_FILE", __ERROR_LOG_DIR__."/".__ERROR_FILE_NAME__);

if (!defined('__PHP_INC_CORE_DIR__')) 			DEFINE("__PHP_INC_CORE_DIR__", __PHP_ASSETS_DIR__."/core");
if (!defined('__COMPOSER_LIB__')) 				DEFINE("__COMPOSER_LIB__", __PHP_ASSETS_DIR__."/lib/vendor");


if (!defined('__LAYOUT_PATH__'))				DEFINE("__LAYOUT_PATH__", __PHP_ASSETS_DIR__."/layouts/");

if (!defined('__LAYOUT_HEADER__'))				DEFINE("__LAYOUT_HEADER__", __LAYOUT_PATH__.'/header.php');
if (!defined('__LAYOUT_NAVBAR__'))				DEFINE("__LAYOUT_NAVBAR__", __LAYOUT_PATH__.'/navbar.php');
if (!defined('__LAYOUT_FOOTER__'))				DEFINE("__LAYOUT_FOOTER__", __LAYOUT_PATH__.'/footer.php');

if (!defined('__URL_PATH__'))				    DEFINE("__URL_PATH__",APP_HOME);
if (!defined('__URL_HOME__'))				    DEFINE("__URL_HOME__",$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].__URL_PATH__);
if (!defined('__LAYOUT_URL__'))					DEFINE("__LAYOUT_URL__", __URL_HOME__.'/assets/layouts/');

DEFINE('__THIS_PAGE__', str_replace(APP_HOME."/","",$_SERVER['SCRIPT_NAME']));


unset($logfile_name);

$navigation_link_array=array(

	"dropdown" => array(
		"View"=> array(
			"Artist" => "view/artist.php",
			"Studio" => "view/studio.php"

		),
		"Missing"=> array(
			"Titles" => "missing/title.php",
			"Artist" => "missing/artist.php",
			"Genre" => "missing/genre.php",
			"Studio" => "missing/studio.php"
		),
	),


"home" => array (
	"url" => "home.php",
	"text" => "home",
	"secure" => false,
	"js" => false
	),
	
"new" => array (
	"url" => "new.php",
	"text" => "New Videos",
	"secure" => false,
	"js" => false
	),
"test" => array (
	"url" => "test.php",
	"text" => "Test Page",
	"secure" => false,
	"js" => false
	),
	
"logout" => array (
	"url" => "#",
	"text" => "Log Out",
	"secure" => true,
	"js" => ' onclick="logout();"'
	)
	

);


$artistNameFixArray = array(
"Chloé" => "Chloe Lacourt",
"Chloe" => "Chloe Lacourt"
);


$namesArray=array(
"Filthy Rich",
"Chad Rockwell",
"Ricky Johnson",
"David Perry",
"Adam Black",
"Oliver Trunk",
"Alec Knight",
"Alex Moretti",
"Andrea Moranti",
"Johnny Fender",
"Angelo Godshack",
"Anthony Rosano",
"Barry Scott",
"Mario Rossi",
"Ben Kelly",
"Bill Bailey",
"Billy Glide",
"Frankie G",
"Bruce Venture",
"Matt Bird",
"Carlo Minaldi",
"Will Pounder",
"Willy Regal",
"Mark Crow",
"Chad Alva",
"Charles Dera",
"Chris Torres",
"Choky Ice",
"Danny Mountain",
"David Perry",
"Dean Van Damme",
"Denis Reed",
"Derrick Pierce",
"Emilio Ardana",
"Manuel Ferrara",
"Eric Masterson",
"Erik Everhard",
"Evan Stone",
"Franco Roccaforte",
"Frank Gun",
"Frank M.",
"George Lee",
"Jake Adams",
"James Brossman",
"James Deen",
"Joe Monti",
"John Strong",
"John Price",
"Johnny Pag",
"Juan Lucho",
"Kai Taylor",
"Kiara Lord",
"Kane Turna",
"Keni Styles",
"Kurt Lockwood",
"Lance Hardwood",
"Lauro Giotto",
"Long John",
"Luca Ferrero",
"Lucas Frost",
"Luke Hotrod",
"Marc Rose",
"Marco Banderas",
"Marcus London",
"Mark Wood",
"Mark Zane",
"Mark Zicha",
"Markus Dupree",
"Max Cortes",
"Max Deeds",
"Michael Fly",
"Michael Swayze",
"Mick Blue",
"Bradley Remington",
"Romeo Price",
"Flynt Dominic",
"Clover",
"Robby Echo",
"Peter North",
"Brad Knight",
"Dick Chibbles",
"Andrew Marshall",
"Rento",
"Zack",
"Renato",
"Vincent Vega",
"Zac Wild",
"Logan Long",
"Mike Angelo",
"Mike Mancini",
"Kristof Cale",
"Xander Corvus",
"Mr Longwood",
"Toby",
"Nick Lang",
"Nick Ross",
"Jessy Jones",
"Randy Reno",
"Alberto Blanco",
"Ste Axe",
"Georgie Lyall",
"John Stagliano",
"Ian Scott",
"Eros",
"Nathan Bronson",
"Tony Brooklyn",
"Vinny Star",
"Toni Ribas",
"Chris Diamond",
"Nikki Nuttz",
"Tyler Steel",
"Charlie Dean",
"Alberto Blanco",
"Jean Val Jean",
"Rocco Siffredi",
"Pablo Ferrari",
"Blaten Lee",
"Jon Jon",
"Peter Green",
"Sean Lawless",
"Ramon Nomar",
"Raul Costa",
"Ricky Mancini",
"Ryan Mclane",
"Ryan Ryder",
"Sam Bourne",
"Nicky Madisson",
"Seth Gamble",
"Steve Holmes",
"Antonio Ross",
"Kyle Mason",
"Jack Venice",
"Johnny Goodluck",
"Leny Ewil",
"Danny Wylde",
"Thomas Stone",
"Johnny The Kid",
"Ryan Driller",
"Eric John",
"Ricky Spanish",
"Jovan Jordan",
"Johnny Castle",
"Brad Newman",
"Damon Dice",
"Justin Hunt",
"Jay Rock",
"Zack",
"Adam Ocelot",
"Brett Rossi",
"Stirling Cooper",
"Jason Moody",
"Codey Steele",
"Tommy Gunn",
"Tyler Nixon",
"Jamie Stone",
"Jay Smooth",
"Talon",
"Alex Legend",
"Axel Aces",
"David Loso",
"Jack Vegas",
"Brad Tyler",
"Tony Martinez",
"Nick Manning",
"Montana Sky",
"Tommy Pistol",
"Vince Carter",
"Vince Karter",
"Will Powers",
"Yanick Shaft",
"Zenza Raggi",
"Lutro",
"Dean Van Damme",
"Matt Bird",
"Antonio",
"Chad",
"Charlie",
"Clark",
"Cristian",
"Frenky",
"George",
"J.J.",
"David",
"Totti",
"Steve Q",
"Tarzan",
"Teo",
"Rico",
"Sabby",
"Parker",
"MrPete",
"Mugur",
"Neeo",
"Nick",
"Moreno",
"Chanel Camryn",
"Robbin Banx",
"Alex Jett","Billy Boston","Rob Banks","Diego Perez","Jon Rogue"
);

foreach($namesArray as $name)
{
	$__namesArray[] = str_replace(" ","",strtolower($name));
}
