<?php 

function command_search($search_directory='',$search_pattern="mp4",$max='')
{
    global $options_arg;
    
    if ( !isset($search_directory)) {
    
        $search_directory =  getcwd();
    }
    
    logger("search_directory: ".$search_directory);
    logger("search_pattern: ".$search_pattern);
    
    $search_array = file_search($search_directory,$search_pattern, $options_arg,$max);
    $max_result=file_get_num_results($search_array, $options_arg);
    
    $result = array($max_result, $search_array);
    return $result;
    
}



function file_search($location='', $fileregex='', $class_options='', $maxdepth='')
{

	$matchedfiles = array();

    if (!$location or !is_dir($location) or !$fileregex) {
       return false;
    }

	if ( isset($class_options->options["file"]) ) 
	{
		// turn comma separeted list of files into array	
		$matchedfiles = explode(",",$class_options->options["file"]);
	} else
	{
		if($maxdepth == 1) {
			$my_DirectoryIterator="DirectoryIterator";
			$my_IteratorIterator="IteratorIterator";
		} else {
			$my_DirectoryIterator="RecursiveDirectoryIterator";
			$my_IteratorIterator="RecursiveIteratorIterator";
		}
		
		
		$Directory = new $my_DirectoryIterator($location);
		$Iterator = new $my_IteratorIterator($Directory);

		foreach ($Iterator as $info)
		{
			$__file_ext = $info->getExtension();
			if ( strtolower($fileregex) == strtolower($__file_ext) )
			{
				$matchedfiles[] = $info->getPathname();
			}
		}
	}
	
	if ( count($matchedfiles) >= 1 )
	{
		sort($matchedfiles);
		return $matchedfiles;
	} else {
			return array();

	}
}


function file_get_num_results($array, $options_arg)
{
    
	
	if ( isset($options_arg->options["max"])) {
    	verbose_output("Max number of results " . $options_arg->options["max"]);

		return $options_arg->options["max"];
	} else {
		return count($array);
	}	
}
?>