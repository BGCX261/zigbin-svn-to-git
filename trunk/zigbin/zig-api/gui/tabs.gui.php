<?php

class zig_tabs
{
	function tabs($parameters,$arg1,$arg2,$arg3)
	{
		$script = $GLOBALS['zig']['current']['script'] ;
		$module = $GLOBALS['zig']['current']['module'] ;
		$permit = true ;
		$new_tab = true ;
		if($arg1 or $arg2 or $arg3)
		{
			$script = $arg1 ? $arg1 : $script ;
			$module = $arg2 ? $arg2 : $module ;
			$permit = isset($arg3) ? $arg3 : true ;
			$new_tab = true ;
		}
		if(is_array($parameters))
		{
			$script = array_key_exists("script",$parameters) ? $parameters['script'] : $script ;
			$module = array_key_exists("directory",$parameters) ? $parameters['directory'] : $module ;
			$permit = array_key_exists("permit",$parameters) ? $parameters['permit'] : $permit ;
			$new_tab = array_key_exists("new_tab",$parameters) ? $parameters['new_tab'] : $new_tab ;
		}

		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$sql = "SELECT 
					`a`.`module` AS `applicationName`,`a`.`name`,`a`.`link`,`directory` 
				FROM 
					`zig_tabs` `a`, `zig_applications` `b`
				WHERE 
					`a`.`module`=`b`.`name` AND `directory`='$module' AND `a`.`permission`='enabled' 
				ORDER BY 
					`a`.`zig_weight`,`a`.`id` ASC" ;
		$field_result = zig("query",$sql) ;

		// -- Start Check Permissions
		$abort = false ;
		$applicationName = "" ;
		while($field_fetch=$field_result->fetchRow())
		{
			switch($applicationName)
			{
				case "":
				{
					$applicationName = $field_fetch['applicationName'] ;
				}
			}
			$permission = $permit ? zig("permissions",$field_fetch['directory'],$field_fetch['link'],zig("config","zig_action")) : true ;
			if($permission)
			{
				$allowed_tabs[] = $field_fetch ;
			}
			$abort = (!$permission and $field_fetch['link']=="index.php" and $script=="index.php") ? true : false ;
			if($abort and sizeof($allowed_tabs))
			{
				break ;
			}
		}
		// -- End Check Permissions
		$tabs_size = isset($allowed_tabs) ? sizeof($allowed_tabs) : 0 ;

		if($abort)
		{
			if($tabs_size)
			{
				header("Location: ".$allowed_tabs[0]['link']) ;
			}
			else if($module<>"zig-api")
			{
				header("Location: ../") ;
			}
		}

		$sql = "SELECT `id` FROM `${pre}reports` WHERE `application`='${applicationName}' LIMIT 1" ;
		$result = zig("query",$sql) ;
		$reports = $result->RecordCount() ;
		$tabs_size = $reports>0 ? $tabs_size+1 : $tabs_size ;
		$buffer = "" ;
		if($tabs_size)
		{
			$tabs_size = $new_tab ? ($tabs_size + 1) : $tabs_size ;
			$table_width = $tabs_size * 10 ;
			$cell_width = (100 / $tabs_size)."%" ;
			$counter = 0 ;
			$tabs = NULL ;
			$sub_tabs_buffer = NULL ;
			
			foreach($allowed_tabs as $values) {
				if(strpos($values['link'],"/"))
				{
					$ripped_value = explode("/",$values['link']) ;
					$tab_script = end($ripped_value) ;
				}
				else
				{
					$tab_script = $values['link'] ;
				}

				$values['link'].= strpos($values['link'],"?") ? "&load_point=action&module=${module}" : "?load_point=actions&module=${module}" ;
				$sub_tabs = NULL ;
				if($tab_script == $script)
				{
					$tabs.= "<td class='zig_tab_active' width='$cell_width' onclick=\"zig_tabs('zig_tabs_table_id','${counter}','$values[link]') ;\">" ;
					$tabs.= "<div id='zig_tab_link_name_${counter}' class='zig_tab_div_active'>" ;
					//$tabs.= "<a href=\"javascript: return void(0) ;\" class='zig_anchor_tab_active_class'>$values[name]</a>" ;
					$tabs.= $values['name'] ;
					$tabs.= "</div>" ;
//					$sub_tabs = "<div class='zig_div_sub_tabs'><a href=''>LongTabs1</a> | <a href=''>LongTab2</a> | <div class='zig_div_sub_tab_active'><a href=''>LongTab3</a></div></div>" ;
					$sub_tabs_template = zig("template","block","tabs","sub tabs cell") ;
					$sub_tabs_template = str_replace("{class}","zig_tab_active",$sub_tabs_template) ;
					$sub_tabs_template = str_replace("{sub_tabs}",$sub_tabs,$sub_tabs_template) ;
					$sub_tabs_buffer.= $sub_tabs_template ;
				}
				else
				{
					$tabs.= "<td class='zig_td_tab' width='${cell_width}' onclick=\"zig_tabs('zig_tabs_table_id','${counter}','$values[link]') ;\">" ;
					$tabs.= "<div id='zig_tab_link_name_${counter}' class='zig_tab_div_inactive'>" ;
					//$tabs.= "<a href=\"javascript: return void(0) ;\">$values[name]</a>" ;
					$tabs.= $values['name'] ;
					$tabs.= "</div>" ;
					$sub_tabs_buffer.= zig("template","block","tabs","sub tabs cell") ;
				}
				$tabs.= "</td>" ;
				$counter++ ;
			}

			// -- start reports
			switch($reports>0)
			{
				case true:
				{
					$tabs.= zig("template","block","tabs","tab") ;
					$tabs = str_replace("{tabName}","Reports",$tabs) ;
					$tabs = str_replace("{class}","zig_tab_div_inactive",$tabs) ;
					$tabs = str_replace("{counter}",$counter,$tabs) ;
					$tabs = str_replace("{link}","../zig-api/decoder.php?zig_hash=".zig("hash","encrypt","function=reports,applicationName=${applicationName},zigjax=1"),$tabs) ;
					$counter++ ;
				}
			}
			// -- end reports

			// -- Start new tab
			if($new_tab)
			{
				$tabs.= "<td class='zig_td_tab' width='${cell_width}' onclick=\"window.open('$_SERVER[PHP_SELF]','_blank')\">" ;
				$tabs.= "New Tab --&gt;&gt;" ;
				$tabs.= "</td>" ;
			}
			// -- End new tab

			$buffer = str_replace("{cell_width}",$cell_width,$buffer) ;
			$buffer = zig("template","block","tabs","tabs") ;
			$buffer = str_replace("{tabs}",$tabs,$buffer) ;
			$buffer.= zig("template","block","tabs","tabs filler") ;
			if($sub_tabs)
			{
				$sub_tabs_buffer.= zig("template","block","tabs","sub tabs cell") ;
				$sub_tabs_buffer = str_replace("{class}","zig_td_sub_tab",$sub_tabs_buffer) ;
				$sub_tabs_buffer = str_replace("{sub_tabs}","&nbsp;",$sub_tabs_buffer) ;
				$sub_tabs_buffer = str_replace("{cell_width}",$cell_width,$sub_tabs_buffer) ;
				$buffer.= zig("template","block","tabs","tabs") ;
				$buffer = str_replace("{tabs}",$sub_tabs_buffer,$buffer) ;
			}
			$buffer = str_replace("{zig_tabs_table_width}",$table_width."%",$buffer) ;
		}
		
		$zig_result['value'] = $buffer ;
		$zig_result['return'] = 1 ;
		
		return $zig_result ;
	}
}

?>