<?php

class zig_permissions
{
	function permissions($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$module = $arg1 ? $arg1 : $GLOBALS['zig']['current']['module'] ;
			$script = $arg2 ? $arg2 : $GLOBALS['zig']['current']['script'] ;
			$action = $arg3 ? $arg3 : $GLOBALS['zig']['current']['action'] ;
			$field_name = "all" ;
			$field_value = "all" ;
		}
		else if(is_array($parameters))
		{
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : $GLOBALS['zig']['current']['module'] ;
			$script = array_key_exists("script",$parameters) ? $parameters['script'] : $GLOBALS['zig']['current']['script'] ;
			$action = array_key_exists("action",$parameters) ? $parameters['action'] : $GLOBALS['zig']['current']['action'] ;
			$tab = array_key_exists("tab",$parameters) ? $parameters['tab'] : NULL ;
			$field_name = array_key_exists("field_name",$parameters) ? $parameters['field_name'] : "all" ;
			$field_value = array_key_exists("field_value",$parameters) ? $parameters['field_value'] : "all" ;
		}

		$module = $module=="{any}" ? NULL : $module ;
		$directory = $module ;
		$script = $script=="{any}" ? NULL : $script ;
		$action = $action=="{any}" ? NULL : $action ;

		$zig_info_obj = new zig_info ;
		$user = zig("info","user") ;
		$user_id = zig("info","user_id") ;
		$group = $zig_info_obj->group() ;
		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;

			$script = $script ? $script : $GLOBALS['zig']['current']['script'] ;
			$script = addslashes($script) ;
			$sql = "SELECT `${pre}applications`.`name` AS module,`${pre}tabs`.`name` AS tab 
					FROM `${zig_global_database}`.`${pre}tabs`,`${zig_global_database}`.`${pre}applications` 
					WHERE 
						`directory`='$directory' 
					AND `${pre}tabs`.`module`=`${pre}applications`.`name` 
					AND `${pre}tabs`.`link`='${script}' LIMIT 1" ;

			$result = zig("query",$sql) ;
			$fetch = $result->fetchRow() ;
			$module = $fetch['module'] ;
			$tab = $fetch['tab'] ;

		$where_tab = $tab ? " AND (tab='$tab' OR tab='all') " : NULL ;
		$where_action = $tab ? " AND (action='$action' OR action='all') " : NULL ;
		
		$sql = "SELECT users 
				FROM `$zig_global_database`.`${pre}permissions` 
				WHERE 
					(zig_parent_id='$user_id' OR users='${user}' OR users='$group' OR users='all') 
				AND (module='$module' OR module='all') $where_tab $where_action 
				AND (field_name='$field_name' OR field_name='all') 
				AND (field_value='$field_value' OR field_value='all') 
				AND permission='allow' LIMIT 1" ;

		$result = zig("query",$sql,"permissions.lib.php",false) ;
		$permission = $result->RecordCount() ;
		
		if($permission == 1)
		{	
			$sql = "SELECT users 
					FROM $zig_global_database.${pre}permissions 
					WHERE 
						(zig_parent_id='$user_id' OR users='$user' OR users='$group' OR users='all') 
					AND (module='$module' OR module='all')  $where_tab $where_action 
					AND (field_name='$field_name' OR field_name='all') 
					AND (field_value='$field_value' OR field_value='all') 
					AND permission='deny' LIMIT 1" ;

			$result = zig("query",$sql) ;
			$permission = $result->RecordCount() ? false : true ;
/*			if($module=="zig-helpdesk")
			{
				print " m=".$module ;
				print " t=".$tab ;
				print " a=".$action ;
				print " u=".$user ;
				print " p=".$permission ;
				print " sql=".$sql ;
				print "<br /><br />" ;
//				exit() ;
			}*/
		}
		
		$zig_return['value'] = $permission ;
		$zig_return['return'] = 1 ;
		return $zig_return ;
	}
}


?>