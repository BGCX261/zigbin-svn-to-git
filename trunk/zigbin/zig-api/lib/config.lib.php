<?php

class zig_config
{
	function config($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$name = $arg1 ;
			$module = $arg2 ? $arg2 : NULL ;
			$config = $arg3 ? $arg3 : NULL ;
			$result = "all" ;
			$tab = NULL ;
			$action = isset($GLOBALS['zig']['current']['action']) ? $GLOBALS['zig']['current']['action'] : NULL ;
			$user = NULL ;
		}
		else if(is_array($parameters))
		{
			$name = array_key_exists("name",$parameters) ? $parameters['name'] : NULL ;
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : NULL ;
			$config = array_key_exists("config",$parameters) ? $parameters['config'] : NULL ;
			$result = array_key_exists("result",$parameters) ? $parameters['result'] : NULL ;
			$tab = array_key_exists("tab",$parameters) ? $parameters['tab'] : NULL ;
			$action = array_key_exists("action",$parameters) ? $parameters['action'] : (isset($GLOBALS['zig']['current']['action']) ? $GLOBALS['zig']['current']['action'] : NULL) ;
			$user = array_key_exists("user",$parameters) ? $parameters['user'] : NULL ;
		}

		if(!$module)
		{
			$fileBaseDirectory = dirname(__FILE__) ;
			$baseDirectory = explode("zig-api",$fileBaseDirectory) ;
			$baseDirectory = str_replace("\\","/",$baseDirectory[0]) ;
			$debug_backtrace = debug_backtrace() ;
			$module = $debug_backtrace[1]['file'] ;
			$module = str_replace("\\","/",$module) ;
			$module = str_replace($baseDirectory,"",$module) ;
			$splitted_module = explode("/",$module) ;
			$module = $splitted_module[0] ;
			$current_module = NULL ;
			if(array_key_exists("zig",$GLOBALS))
			{
				$current_module = array_key_exists("current",$GLOBALS['zig']) ? (array_key_exists("module",$GLOBALS['zig']['current']) ? $GLOBALS['zig']['current']['module'] : NULL) : NULL ;
			}
			$module = ($module=="zig-api" and $current_module<>"zig-api" and $current_module<>'') ? $module : ($module ? $module : "zig-api") ;
		}
		
		$pre = NULL ;
		$zig_global_database = NULL ;
		$value = NULL ;
		if(array_key_exists("zig",$GLOBALS))
		{
			if(array_key_exists("sql",$GLOBALS['zig']))
			{
				$pre = array_key_exists("pre",$GLOBALS['zig']['sql']) ? $GLOBALS['zig']['sql']['pre'] : NULL ;
				$zig_global_database = array_key_exists("global_database",$GLOBALS['zig']['sql']) ? $GLOBALS['zig']['sql']['global_database'] : NULL ;
			}
		}
		$original_config = $config ? $config : 0 ;
		$original_module = $module ? $module : 0 ;
		$original_tab = $tab ? $tab : 0 ;
		$original_action = $action ? $action : 0 ;
		$original_user = $user ? $user : 0 ;
		if($pre and $zig_global_database)
		{
		$record = 0 ;
		$value = NULL ;
		$value_check = $this->config_check($original_config,$original_module,$original_tab,$original_action,$original_user,$name) ;
		if($value_check)
		{
			$value = $value_check ;
		}
		else
		{
			if($module<>"all" and $module<>"zig-api")
			{
				$module_sql = "SELECT `name` FROM `${zig_global_database}`.`${pre}applications` WHERE `directory`='${module}' AND `zig_status`<>'deleted' LIMIT 1" ;
				$module_result = $GLOBALS['zig']['adodb']->Execute($module_sql) ;
				$module_fetch = $module_result->fetchRow() ;
				$module = $module_fetch['name'] ? $module_fetch['name'] : "all" ;
				$error_number = $GLOBALS['zig']['adodb']->ErrorNo() ;
			}
			if(!$user)
			{
				require_once("../zig-api/lib/info.lib.php") ;
				$zig_info_obj = new zig_info ;
				$user_return = $zig_info_obj->info("info","user") ;
				$user = array_key_exists("value",$user_return) ? $user_return['value'] : NULL ;
			}
			$script = isset($GLOBALS['zig']['current']['script']) ? $GLOBALS['zig']['current']['script'] : NULL ;
			$limit = is_numeric($result) ? "LIMIT ${result}" : NULL ;

			if(!$tab)
			{
				// -- Start set tab
				$tab_sql = "SELECT `name` FROM `${zig_global_database}`.`${pre}tabs` WHERE (`module`='$module' OR `module`='all') AND `link`='${script}' AND `zig_status`<>'deleted' LIMIT 1" ;
				$tab_result = $GLOBALS['zig']['adodb']->Execute($tab_sql) ;
				$tab_fetch = $tab_result->fetchRow() ;
				$tab = $tab_fetch['name'] ? $tab_fetch['name'] : "all" ;
				$error_number = $GLOBALS['zig']['adodb']->ErrorNo() ;
				// -- End set tab
			}

			if($config)
			{
				$config_names[] = $config ;
			}
			else
			{
				$value_check = $this->config_check($original_config,$module,$tab,$original_action,$original_user,"config") ;
				if($value_check)
				{
					$config_names = $value_check ;
				}
				else
				{
					$config_sql = "SELECT `value` FROM `${zig_global_database}`.`${pre}configs` WHERE (`module`='${module}' OR `module`='all') AND (`tab`='${tab}' OR `tab`='all') AND `config`='default' AND (`action`='${action}' OR `action`='all') AND (`users`='$user' OR `users`='all') AND `name`='config' AND `zig_status`<>'deleted' ORDER BY `priority` LIMIT 1" ;
					$config_result = $GLOBALS['zig']['adodb']->Execute($config_sql) ;
					$error_number = $GLOBALS['zig']['adodb']->ErrorNo() ;
					$config_count = $config_result->RecordCount() ;
					if($config_count and !$error_number)
					{
						while($config_fetch = $config_result->fetchRow())
						{
							if($config_fetch['value'])
							{
								$config_names[] = $config_fetch['value'] ;
							}
						}
					}
					$config_names[] = "default" ;
					$GLOBALS[$original_config][$module][$tab][$original_action][$original_user]['config'] = $config_names ;
					if($original_config)
					{
						$_SESSION[$original_config][$module][$tab][$original_action][$original_user]['config'] = $config_names ;
					}
				}
			}
			
			foreach($config_names as $config)
			{
				$value_check = $this->config_check($config,$module,$tab,$action,$user,$name) ;
				if($value_check)
				{
					$value = $value_check ;
					break ;
				}
				if($user and !$error_number) // -- User specific configuration
				{
					$sql = "SELECT `value` FROM `${zig_global_database}`.`${pre}configs` WHERE (`module`='${module}' OR `module`='all') AND (`tab`='${tab}' OR `tab`='all') AND `config`='${config}' AND (`action`='$action' OR `action`='all') AND `users`='$user' AND `name`='$name' AND `zig_status`<>'deleted' ORDER BY `priority` $limit" ;
					$result = $GLOBALS['zig']['adodb']->Execute($sql) ;
					$error_number = $GLOBALS['zig']['adodb']->ErrorNo() ;
					$record = $error_number ? 0 : $result->RecordCount() ;
				}
				if($error_number)
				{
					$zig_result['error'] = "Script: config.lib.php<br />" ;
					$zig_result['error'].= isset($sql) ? "SQL Statement: ${sql}<br />" : "SQL Statement: [blank]<br />" ;
					$zig_result['error'].= "SQL Error: ".$GLOBALS['zig']['adodb']->ErrorMsg() ;
				}
				else
				{
					if($record==0) // -- Tab specific configuration
					{
						$value_check = $this->config_check($config,$module,$tab,$action,$user,$name) ;
						if($value_check)
						{
							$value = $value_check ;
							break ;
						}
						$sql = "SELECT `value` FROM `${zig_global_database}`.`${pre}configs` WHERE (`module`='${module}' OR `module`='all') AND `tab`='$tab' AND (`config`='${config}' OR `config`='all') AND (`action`='${action}' OR `action`='all') AND `users`='all' AND `name`='${name}' AND `zig_status`<>'deleted' ORDER BY `priority` ${limit}" ;
						$result = $GLOBALS['zig']['adodb']->Execute($sql) ;
						$record = $result->RecordCount() ;
					}
					if($record==0)
					{
						$sql = "SELECT `value` FROM `${pre}configs` WHERE (`module`='${module}' OR `module`='all') AND (`tab`='$tab' OR `tab`='all') AND (`config`='${config}' OR `config`='all') AND (`action`='${action}' OR `action`='all') AND `users`='all' AND `name`='${name}' AND `zig_status`<>'deleted' ORDER BY `priority` ${limit}" ;
						$result = $GLOBALS['zig']['adodb']->Execute($sql) ;
						$record = $result->RecordCount() ;
					}
					if($record==0)
					{
						if($module<>"zig-api")
						{
							$module = "zig-api" ;
						}
						else if($config<>"default")
						{
							$config = "default" ;
						}
						if($record==0 and $module<>"zig-api" and $config<>"default")
						{
							$module = "zig-api" ;
							$config = "default" ;
						}
						$value_check = $this->config_check($config,$module,$tab,$action,$user,$name) ;
						if($value_check)
						{
							$value = $value_check ;
							break ;
						}
						$sql = "SELECT `value` FROM `${zig_global_database}`.`${pre}configs` WHERE (`module`='${module}' OR `module`='all') AND (`tab`='$tab' OR `tab`='all') AND (`config`='${config}' OR `config`='all') AND (`action`='${action}' OR `action`='all') AND `users`='${user}' AND `name`='${name}' AND `zig_status`<>'deleted' ORDER BY `priority` ${limit}" ;
						$result = $GLOBALS['zig']['adodb']->Execute($sql) ;
						$record = $result->RecordCount() ;
					
						if(!$record)
						{
							$value_check = $this->config_check($config,$module,$tab,$action,$user,$name) ;
							if($value_check)
							{
								$value = $value_check ;
								break ;
							}							
							$sql = "SELECT `value` FROM `${zig_global_database}`.`${pre}configs` WHERE (`module`='${module}' OR `module`='all') AND (`tab`='$tab' OR `tab`='all') AND (`config`='${config}' OR `config`='all') AND (`action`='${action}' OR `action`='all') AND `users`='all' AND `name`='${name}' AND `zig_status`<>'deleted' ORDER BY `priority` ${limit}" ;
							$result = $GLOBALS['zig']['adodb']->Execute($sql) ;
							$record = $result->RecordCount() ;
						}
					}

					if($record>1)
					{
						while($fetch=$result->fetchRow())
						{
							$value[] = $fetch['value'] ;
						}
					}
					else if($record)
					{
						$fetch = $result->fetchRow() ;
						$value = $fetch['value'] ;
					}
				}
				if($value)
				{
					break ;
				}
			}

			$name = str_replace(" ","_",$name) ;
			$config = $config ? $config : 0 ;
			$module = $module ? $module : 0 ;
			$tab = $tab ? $tab : 0 ;
			$action = $action ? $action : 0 ;
			$user = $user ? $user : 0 ;
			$GLOBALS[$config][$module][$tab][$action][$user][$name] = $value ;
			if($config)
			{
				$_SESSION[$config][$module][$tab][$action][$user][$name] = $value ;
			}
			$GLOBALS[$original_config][$original_module][$original_tab][$original_action][$original_user][$name] = $value ;
			if($original_config)
			{
				$_SESSION[$original_config][$original_module][$original_tab][$original_action][$original_user][$name] = $value ;
			}
		}
		}

		$zig_result['return'] = 1 ;
		$zig_result['value'] = $value ;
		
		return $zig_result ;
	}
	
	function config_check($config,$module,$tab,$action,$user,$name)
	{
		$name = str_replace(" ","_",$name) ;
		$value = NULL ;
		if(isset($GLOBALS[$config][$module][$tab][$action][$user][$name]))
		{
			$value = $GLOBALS[$config][$module][$tab][$action][$user][$name] ;
		}
		else if(isset($_SESSION[$config][$module][$tab][$action][$user][$name]))
		{
			$value = $_SESSION[$config][$module][$tab][$action][$user][$name] ;			
		}

		return $value ;
	}
}

?>