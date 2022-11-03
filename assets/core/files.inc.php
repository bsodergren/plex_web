<?php



function chk_file($value,$command='delete',$options='')
{
    switch ($command)
    {
        case "rename":
            if (is_file($value))
            {
                if(is_file($options))
                {
                    chk_file($options,"delete");
                }
                logger("Renaming $value to $options");
                rename($value, $options );
            };
            break;
        case "delete":
            if (is_file($value))
            {
                logger("deleting $value");
                unlink($value);
            };
            break;
    }
}

    
function file_write_array($file='',$array=array())
{
	$string=var_export($array,1);
	file_write_file($file, $string,'w');
}

function file_write_file($file='', $string='', $mode='w',$backup=true)
{
	$file=basename($file);
	$file = __ERROR_LOG_DIR__ . "/" . $file;
	
	if ($mode == "w") 
	{
		if( file_exists($file) == true )
		{
			if($backup == true) 
			{
				$backup = $file .".bak";
				if( file_exists($backup) == true )
				{	
					chk_file($backup);
				}
				chk_file($file,"rename", $backup );
			} else {
				chk_file($file,"delete");
			}
		}		
	}

	$fp=fopen($file, $mode);		
	fwrite($fp, $string);
	fclose($fp);
		
}





?>