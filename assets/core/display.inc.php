<?php

function display_directory_navlinks($url,$text,$request=array())
{
	
	global $request_key;
	global $_SESSION;
	global $_REQUEST;
	
	$request_string = '';
	
	if(count($request) > 0 )
	{
		foreach($request as $req_key => $req_value )
		{
			$req_array[] = $req_key . "=".$req_value;
		}
		
		$request_string="?";

		foreach($req_array as $req_string)
		{
			$request_string .= $req_string."&";
		}
		
		//$request_string=rtrim($request_string, '?');
		$request_string=rtrim($request_string, '&');
		
	}
		
	
	//$link_url = $url . "?" . $request_key ."&genre=".$_REQUEST["genre"]."&". ;
	 $html = "<a href='".$url.$request_string."'>".$text."</a>";
	 
	 return $html;
}

function display_filelist($results,$option='')
{
	
	$output='';
	
$output .= '<div class="container">'."\n";


	foreach($results as $id => $row)
	{
		$output .= '<table class="blueTable" > '."\n";		
		$row_id=$row['id'];
		$row_filename=$row['filename'];
		$button=false;
		$extra_button='';
		
		if($option=='hide') $button="hide";		
		if($option=='filedelete') $button="filedelete";
		
		if($button == true) {
			$extra_button='<input type="submit" name="submit" value="'.$button.'" id="'.$button.'_'.$row_id.'" onclick="doSubmitValue(this.id);">';
		}
	
				$array = array("FILE_NAME" => $row_filename,
				"DELETE_ID" => "delete_".$row_id,
				"HIDE_BUTTON" => $extra_button);
		$output .= process_template("metadata_row_header",$array);
		$value_array =array();
	$output .= '<tbody> '."\n";

		foreach($row as $key => $value )
		{
			switch ($key)
			{
				case 'id':break;
				case 'filename':break;

				case 'thumbnail':
					$output .= process_template("metadata_thumbnail",["THUMBNAIL"=>$value,"FILE_ID"=>$row_id]);

					//$output .= process_template("metadata_thumbnail",["THUMBNAIL"=>$value,"FILE_ID"=>$row_id]);
					//$output .=  "<tr><td></td><td><img src='".$value."' onclick=\"popup('/plex_web/video.php?id=".$row_id."', 'video')\"></td><td></td></tr>";
					break;

				case 'duration':
					$seconds = round($value/1000);
					$duration_output = sprintf('%02d:%02d:%02d', ($seconds/ 3600),($seconds/ 60 % 60), $seconds% 60);
					//$output .=  "<tr><td></td><td>".$duration_output."</td><td></td></tr>";
					$output .= process_template("metadata_button",["DURATION"=>$duration_output]);

					break;


				case 'favorite':
					$yeschecked = ($value == '1') ? "checked" : "";
					$nochecked = ($value == '0') ? "checked" : "";

					$array = array(
					"FILE_ID" =>$row_id,
						"FIELD_KEY" => $key,
						"FIELD_NAME" =>$row_id."_".$key,
						//"PLACEHOLDER" =>  $placeholder,
						"YESCHECKED" => $yeschecked,
						"NOCHECKED" => $nochecked
					);

					$output .=  process_template("metadata_favorite_row",$array);
				break;		

				default:
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
						if( isset($value_array["style"][0]) && $value_array["style"][0] != "" )
						{
							$value = $value .' style="'.$value_array['style'][0].'"';
						}

					} else {
						$value = "" ;
					}

					$array = array(
						
						"FIELD_KEY" => $key,
						"FIELD_NAME" =>$row_id."_".$key,
						"PLACEHOLDER" =>  $placeholder,
						"VALUE" => $value
					);

					$output .=   process_template("metadata_row",$array);
					unset($value_array);
					unset($value);
			}
		}
		$output .= '</tbody></table><p> '."\n";

	}
			$output .= '</div> '."\n";

	echo $output;
}

function display_navbar_left_links($url,$text,$js='')
{

	global $_SESSION;
	$style='';
	
	if($text == $_SESSION['library'])
	{
		$style=" style=\"background:#778899\"";
	}
		$array = array(
				"MENULINK_URL" => $url,
				"MENULINK_JS" => $style,
				"MENULINK_TEXT" => $text);
			return process_template("menu_link",$array);
			
	
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

