<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );

require_once("_config.inc.php");

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use SensioLabs\AnsiConverter\Theme\SolarizedTheme;

$theme = new SolarizedTheme();
$styles = $theme->asCss();
$converter = new AnsiToHtmlConverter($theme, false);

?>
<html>
<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script>

jQuery(document).ready(function(){
jQuery('body,html').animate({scrollTop: 100000}, 800);
})

</script>
</head>
<body>    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>
    <pre style="background-color: #FFFFFF;     padding: 10px 15px; font-family: monospace; font-size: 150%; ">
    
<?php 
    $log_file=ERROR_LOG_FILE;
    if (($handle = fopen($log_file, "r")) !== FALSE)
    {
		
		$pos = -2; // Skip final new line character (Set to -1 if not present)
		$idx=0;
		
	    while (($str_data = fgets($handle, 5000)) !== FALSE)
        {
			$idx++;
            $str_data = wordwrap($str_data, 80, "\n\t");
           // $str_data = $converter->convert($str_data);
            
            echo  $idx." ".$str_data;
        }
   
				
			
		/*
        while (($str_data = fgets($handle, 5000)) !== FALSE)
        {
            $str_data = wordwrap($str_data, 160, "\n");
            $str_data = $converter->convert($str_data);
            
            echo  $str_data;
        }
		*/
    }
    
      
    
    ?>
    </pre>
 </main>
