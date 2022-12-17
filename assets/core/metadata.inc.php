<?php

function metadata_get_filedata($file='')
{
	$AtomicParsley=__ATOM__;
    
	$run_cmd=$AtomicParsley . " '".  realpath($file) . "' -t";
	$results=shell_exec ($run_cmd);
	return $results;
}

function metadata_get_value($file='',$tag='')
{

	$metadata=metadata_get_filedata($file);

	switch ($tag)
	{	
		case "studio": 
			$regex='/(alb).*\:\ (.*)/'; 
			break;
		case "genre": 
			$regex='/(gen).*\:\ (.*)/' ;
			break;
		case "title": 
			$regex='/(nam).*\:\ (.*)/i' ;
			break;
		case "artist": 
			$regex='/(\"©ART\").*\:\ (.*)/' ;
			break;
	}
	
	preg_match($regex, $metadata, $matches);

	if ( isset($matches[2]) )
	{
		$output=$matches[2];
		return strval($output);
	} else 
	{
		return FALSE;
	}

}

function metadata_write_filedata($file='',$value_array='')
{
	$AtomicParsley=__ATOM__;
	$options='';

	foreach ($value_array as $tag => $value) {
		
		if ($tag == "studio" || $tag == "substudio" )
		{
			$tag = "album";
		}

		$value=str_replace("\'","'",$value);
        
		$options .= "--".$tag."=\"".$value."\" ";
	}

	logger("writing options", $options);
	$run_cmd=$AtomicParsley . " '".  realpath($file) . "' " . $options . " -W";
    logger("writing run_cmd", $run_cmd);
	$results=shell_exec ($run_cmd);
	return $results;
}
