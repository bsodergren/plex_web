<?php

function process_template($template, $replacement_array='')
{
    return template::echo($template, $replacement_array);

}//end process_template()





function output($var)
{
    if (is_array($var)) {
        print_r2($var);
        return 0;
    }

    echo $var."<br>\n";
    // return 0;

}//end output()



function JavaRefresh($url, $timeout=0)
{
  roboloader::javaRefresh($url,$timeout);

}//end JavaRefresh()
