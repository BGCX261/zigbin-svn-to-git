<?php

class zig_actions
{
	function actions($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$function = $arg1 ;
			$id = $arg2 ;
			$allowed = $arg3 ? $arg3 : "all" ;
		}
		if(is_array($parameters))
		{
			$function = array_key_exists("function",$parameters) ? $parameters['function'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
			$allowed = array_key_exists("allowed",$parameters) ? $parameters['allowed'] : "all" ;
		}

		$function = $GLOBALS['zig']['current']['action'] ;
		$url = $_SERVER['PHP_SELF'] ;
		if(strpos($url,"?"))
		{
			$ripped_url = explode("?",$url) ;
			$url = $ripped_url[0] ;
		}
		if($function=="add" or $function=="search")
		{
			$exclude = array
			(
				"view",
				"edit",
				"copy"
			) ;
		}
		$allowed = $allowed=="all" ? $allowed : zig("to_array",$allowed) ;
		if($allowed=="all")
		{
			$operations = array
			(
				'view'	=> "32x32/mimetypes/bmp.png",
				'edit'	=> "32x32/actions/edit.png",
				'copy'	=> "32x32/actions/editcopy.png",
				'add'	=> "32x32/actions/add.png",
				'search'=> "32x32/actions/viewmag.png"
			) ;
		}
		else
		{
			
			if(in_array("view",$allowed))
			{
				$operations['view'] = "32x32/mimetypes/bmp.png" ;
			}
			if(in_array("edit",$allowed))
			{
				$operations['edit'] = "32x32/actions/edit.png" ;
			}
			if(in_array("copy",$allowed))
			{
				$operations['copy'] = "32x32/actions/editcopy.png" ;
			}
			if(in_array("add",$allowed))
			{
				$operations['add'] = "32x32/actions/add.png" ;
			}
			if(in_array("search",$allowed))
			{
				$operations['search'] = "32x32/actions/viewmag.png" ;
			}
		}

		foreach($operations as $key => $value)
		{
			if(!zig("permissions","","",$key))
			{
				unset($operations[$key]);
			}
		}

		$actions.= "<table>" ;
		$actions.= "<tr>" ;
		foreach($operations as $key => $icon)
		{
			if(@!in_array($key,$exclude))
			{
				$link = NULL ;
				if($key<>$function or $key=="search")
				{
					$zig_hash = ($key=="view" or $key=="edit" or $key=="copy") ? zig("hash","encrypt","action=".$key.",id=".$id) : zig("hash","encrypt","action=".$key) ;
					$link = $url."?zig_hash=".$zig_hash ;
				}
				if($key==$function)
				{
					$actions.= "<th align='center' style=\"border:1px; border-style:solid;\">" ;
					$actions.= "<a id='zig_actions_image_".$key."' href='${link}'>" ;
				}
				else
				{
					$actions.= "<th align='center' class='zig_actions_image' id='zig_actions_th_$key'>" ;
					$actions.= "<a id='zig_actions_image_".$key."' href='$link'>" ;
				}
				$actions.= "<img src='".zig("images",$icon)."' alt='$key' />" ;
				$actions.= "</a>" ;
				$actions.= "</th>\n" ;
			}
		}
		$actions.= "</tr>\n" ;
		$actions.= "<tr>" ;
		foreach($operations as $key => $icon)
		{
			if(@!in_array($key,$exclude))
			{
				$link = NULL ;
				if($key<>$function or $key=="search")
				{
					$zig_hash = ($key=="view" or $key=="edit" or $key=="copy") ? zig("hash","encrypt","action=".$key.",id=".$id) : zig("hash","encrypt","action=".$key) ;
					$link = $url."?zig_hash=".$zig_hash ;
				}
				$actions.= "<td align='center'>" ;
				if($key==$function)
				{
					$actions.= "<a id='zig_actions_label_".$key."' href='${link}'>" ;
					$actions.= "<b>".$key."</b>" ;
				}
				else
				{
					$actions.= "<a id='zig_actions_label_".$key."' href='${link}' onmouseover=\"document.getElementById('zig_actions_th_$key').className='zig_actions_image_hover';\" onmouseout=\"document.getElementById('zig_actions_th_$key').className='zig_actions_image';\">" ;
					$actions.= $key ;
				}
				$actions.= "</a>" ;
				$actions.= "</td>\n" ;
			}
		}
		$actions.= "</tr>\n" ;
		$actions.= "</table>\n" ;
		$buffer = zig("template","file","actions") ;
		$zig_result['value'] = str_replace("{actions}",$actions,$buffer) ;
		$zig_result['return'] = 1 ;
		return $zig_result ;
	}
}

?>