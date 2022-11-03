<?php

$studio_ignore=array("teamskeet_selects","teamskeet_extras");


$studio_pattern=array(
	"mommy_blows_best" => array(
		"title" => '/(([a-zA-Z0-9\-]+))\_s[0-9]{2,3}\_(.*)\_[0-9]{1,4}(p|k)\.mp4/i'
	),
	"blowpass" => array(
		"title" => '/(([a-zA-Z0-9\-]+))\_s[0-9]{2,3}\_(.*)\_[0-9]{1,4}(p|k)\.mp4/i',
		"artist" => array(
			"pattern" => "/(([a-zA-Z0-9\-]+))\_s[0-9]{2,3}\_(.*)\_[0-9]{1,4}(p|k)/i",
			"delimeter" => "_",
			"group" => 3),
	),
		
	"mom_teaches_sex" => array(
		"title" => '/(ns|mts|dg|tft|ssc|mts|mfp|net|phd)\_(.*)\_[0-9]{3,4}/'
	),
	"bad_milfs" => array(
		"artist" => array(
			"pattern" => '/[a-z]{1,}\_([a-zA-Z_]{1,})[0-9]?\_full.*[0-9]{1,4}.*\.mp4/',
			"delimeter" => "_and_",
			"group" => 1)
		),
	"perv_mom" => array(
		"artist" => array(
			"pattern" => '/[a-z]{1,}\_([a-zA-Z_]{1,})[0-9]?\_full.*[0-9]{1,4}.*\.mp4/',
			"delimeter" => "_and_",
			"group" => 1
		)
	),
	"step_siblings" => array(
		"artist" => array(
			"pattern" => '/[a-z]{1,}\_([a-zA-Z_]{1,})[0-9]?\_full.*[0-9]{1,4}.*\.mp4/',
			"delimeter" => "_and_",
			"group" => 1
		)
	),
	"teamskeet" => array(
		"artist" => array(
			"pattern" => '/[a-z]{1,}\_([a-zA-Z_]{1,})[0-9]?\_full.*[0-9]{1,4}.*\.mp4/',
			"delimeter" => "_and_",
			"group" => 1)
		),
	"brazzers" => array(
		"artist" => array(
			"pattern" => '/([a-zA-Z]{1,4})\_([a-zA-Z\_]*)\_[a-z]{2}[0-9]{1,10}/',
			"delimeter" => "_",
			"group" => 2
		)
	),
		
	"pornworld"=> array(
		"artist" => array(
			"pattern" => '/(([A-Z0-9]+))\_([a-zA-Z_]+)\_(HD|WEB|[0-9PK]+)/i',
			"delimeter" => "_and|",
			"group" => 3
		)
	),
	"ddf"=> array(
		"title" => array (
			"pattern" => '/.*\_-\_((.*))(\-[0-9]{3,5}?)\.mp4/i',
			"group" => 1
			),
	
		"artist" => array(
			"pattern" => '/([a-zA-Z_é\.]*)\_-\_(.*)(\-[0-9]{3,5}?)\.mp4/i',
			"delimeter" => "_and_",
			"group" => 1
		)
	),
	
	"brazzers"=> array(
		"title" => array (
			"pattern" => '/.*\_-\_((.*))(\-[0-9]{3,5}?)\.mp4/i',
			"group" => 1
			),
	
		"artist" => array(
			"pattern" => '/([a-zA-Z]{1,4})\_([a-zA-Z\_]*)\_[a-z]{2}[0-9]{1,10}/i',
			"delimeter" => "_",
			"group" => 2
		)
	)

	
	
);