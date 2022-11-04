<?php

function display_fileinfo()
{
	
}

function display_filelist($results,$option='')
{
	if($option=='hide')
	{
		
	}
	$output='';
	foreach($results as $id => $row)
	{
		$row_key=$row['id'];
		$row_filename=$row['filename'];
		$hide_button='';
		$cols=3;
		if($option=='hide')
		{
			$hide_button='</td><td><input type=submit name="hide_'.$row_key.'" value="hide" id="submit">';
			$cols=2;
		}
				$array = array("FILE_NAME" => $row_filename,
				"DELETE_ID" => "delete_".$row_key,
				"HIDE_COL" => $cols,
				"HIDE_BUTTON" => $hide_button);

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
				$output .=  "<tr><td></td><td><img src='".$value."' onclick=\"popup('/plex_web/video.php?id=".$row_key."', 'video')\"></td><td></td></tr>";
				continue;
			}
			if ($key == "fullpath" ) {
				$video_text="<button onclick=\"popup('/plex_web/video.php?id=".$row_key."', 'video')\">Watch Video</button>";
				$output .=  "<td>".$video_text." </td><td>".$value." </td>";
				
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

