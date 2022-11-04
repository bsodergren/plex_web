<?php

function display_fileinfo()
{
	
}

function display_filelist($results)
{
	
	$output='';
	foreach($results as $id => $row)
	{
		$row_key=$row['id'];
		$row_filename=$row['filename'];
		$array = array("FILE_NAME" => $row_filename);
		$output .= process_template("metadata_row_header",$array);
		$value_array =array();
		
		foreach($row as $key => $value )
		{
			if ($key == "id" ) {
				continue;
			}
			if ($key == "filename" ) {
				continue;
			}
			if ($key == "thumbnail" ) {
				$output .=  "<th rowspan=6><img src='".$value."'></th>";
				continue;
			}
			if ($key == "fullpath" ) {
				$output .=  "<td>$key </td><td>".$value."</td>";
				continue;
			}
			
			if ($key == "favorite" )
			{
				$yeschecked = "";
				$nochecked = " checked";


				if ($value == 1) {
					$yeschecked = " checked";
					$nochecked = "";
				}
				
				$array = array(
					"FIELD_KEY" => $key,
					"FIELD_NAME" =>$row_key."_".$key,
					"PLACEHOLDER" =>  $placeholder,
					"YESVALUE" => $yeschecked,
					"NOVALUE" => $nochecked
				);

				$output .=  process_template("metadata_favorite_row",$array);
				
				continue;
			}
			
			$placeholder = "placeholder=\"".$value."\"";

			if ($value == "" )
			{
				$placeholder = "";
				switch ($key)
				{
					case 'artist':
						$value_array = missingArtist($key, $row);
						break;
					case 'title':
						$value_array = missingTitle($key, $row);
						break;
				}
			}

			if( isset($value_array[$key][0]) && $value_array[$key][0] != "" )
			{
				$value = " value=\"".$value_array[$key][0]."\"";
			} else {
				$value = "" ;
			}

			$array = array(
				"FIELD_KEY" => $key,
				"FIELD_NAME" =>$row_key."_".$key,
				"PLACEHOLDER" =>  $placeholder,
				"VALUE" => $value
			);

			$output .=   process_template("metadata_row",$array);
			unset($value_array);
			unset($value);
			
		}
	}
	echo $output;
}

function display_navbar_links()
{
  
    global $navigation_link_array;
    global $_SERVER;
    $html='';
    $dropdown_html='';
	
    foreach($navigation_link_array as $name => $link_array)
    {
		if($name == "dropdown" )
		{
			$dropdown_html='';
			
			foreach($link_array as $dropdown_name => $dropdown_array)
			{
				$dropdown_link_html='';
				
				foreach ($dropdown_array as $d_name => $d_values)
				{
					$array = array("DROPDOWN_URL_TEXT" => $d_name,
						"DROPDOWN_URL" => $d_values);
					$dropdown_link_html .= process_template("menu_dropdown_link",$array);
				}
					
					
				$array = array("DROPDOWN_TEXT" => $dropdown_name,
				"DROPDOWN_LINKS" => $dropdown_link_html);
				
				$dropdown_html .= process_template("menu_dropdown",$array);
			}
		} else {
			
			$array = array(
				"MENULINK_URL" => $link_array["url"],
				"MENULINK_JS" => $link_array["js"],
				"MENULINK_TEXT" => $link_array["text"]);
			$url_text = process_template("menu_link",$array);
			
			if ($link_array["secure"] == true && $_SERVER['REMOTE_USER'] != "bjorn") {
				$html=$html.$url_text."\n";
			} else {
				$html=$html.$url_text."\n";
			}				
		}
    }
    
    return $html . $dropdown_html ;
}



function display_log($string)
{
    echo "<pre>".$string."</pre>\n";
}

