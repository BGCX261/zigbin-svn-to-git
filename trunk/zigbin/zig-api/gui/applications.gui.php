<?php

class zig_applications {
	function applications($parameters,$arg1='',$arg2='',$arg3='') {
		$buffer = zig("template","file","applications") ;
		$sql = "SELECT * 
				FROM `zig_applications` 
				WHERE `permission`='Active' 
				ORDER BY `zig_weight`,`name`" ;
		$result = zig("query",$sql) ;
		$icon = "" ;
		$name = "" ;
		while($fetch=$result->fetchRow())
		{
			$permission = zig("permissions",$fetch['directory'],"{any}") ;
			if($permission)
			{
				// -- start application image
				$icon.= ($GLOBALS['zig']['current']['module']==$fetch['directory']) ? "<th id='zig_apps_$fetch[name]_image' style=\"border:1px; border-style:solid;\">" : "<th id='zig_apps_$fetch[name]_image' class='zig_actions_image'>" ;
				/*$target = NULL ;
				if($fetch['target']=="_link") {
					$target = ($fetch['target']<>"popup") ? "target = '".$fetch['target']."'" : "onclick=\"window.open('$fetch[link]','$fetch[name]','height=700,width=800,resizable=yes') ; return false ;\" " ;
				}
				$icon.= ($fetch['target']<>"popup") ? "<a $target href='../$fetch[directory]'>" : "<a $target href=''>" ;*/
				//$icon.= "<a $target href='../$fetch[directory]'>" ;
				$icon.= "<a href='../$fetch[directory]'>" ;
				$icon.= "<img src='".zig("images",$fetch['icon'],"../".$fetch['directory']."/".$GLOBALS['zig']['path']['image'])."' alt='$fetch[name]' />" ;
				$icon.= "</a>" ;
				$icon.= "</th>\n" ;
				// -- end application image
			
				// -- start application label
				$name.= "<td id='zig_apps_$fetch[name]' onmouseover=\"document.getElementById(this.id + '_image').className='zig_actions_image_hover';\" onmouseout=\"document.getElementById(this.id + '_image').className='zig_actions_image';\">" ;
				//$name.= ($fetch['target']<>"popup") ? "<a $target href='../$fetch[directory]'>" : "<a $target href='' >" ;
				//$name.= "<a $target href='../$fetch[directory]'>" ;
				$name.= "<a href='../$fetch[directory]'>" ;
				$name.= $fetch['name'] ;
				$name.= "</a>" ;
				$name.= "</td>\n" ;
				// -- end application label
			}
		}

		$show_title = zig("config","show title") ;
		$zig_title = $show_title ? zig("config","title") : NULL ;
		$zig_icon = zig("config","icon") ;
		$zig_icon = zig("images",$zig_icon) ;
		$buffer = str_replace("{zig_icon}",$zig_icon,$buffer) ;
		$buffer = str_replace("{zig_title}",$zig_title,$buffer) ;
		$buffer = str_replace("{icon}",$icon,$buffer) ;
		$buffer = str_replace("{name}",$name,$buffer) ;
		$buffer = str_replace("{header}",zig("display_header"),$buffer) ;
		$zig_result['value'] = $buffer ;
		$zig_result['return'] = 1 ;

		return $zig_result ;
	}
}

?>