<?php

class zig_info
{
	function info($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$info = $arg1 ;
		}
		if(is_array($parameters))
		{
			$info = array_key_exists("info",$parameters) ? $parameters['info'] : NULL ;
		}

		$zig_return['value'] = false ;
		switch(is_array($_SESSION))
		{
			case true:
			{
				switch(array_key_exists("zig_var_current_${info}",$_SESSION))
				{
					case true:
					{
						$zig_return['value'] = $_SESSION["zig_var_current_".$info];
						break ;
					}
					default:
					{
						break ;
					}
				}
				break ;
			}
			default:
			{
				break ;
			}
		}

		if(array_key_exists("zig_hash",$_SESSION) and !$zig_return['value'])
		{
			if($info and $_SESSION['zig_hash'])
			{
				$GLOBALS['zig']['current'][$info] = $zig_return['value'] = $this->$info() ;
				switch(headers_sent())
				{
					case false:
					{
						$host = $_SERVER['HTTP_HOST'] ;
						$url = "/" ;
						switch($info)
						{
							case "tab":
							{
								$url = str_replace($host,"",$_SERVER['PHP_SELF']) ;
								$splitted_url = explode("zig-api/",$url) ;
								$url = $splitted_url[0] ;
							}
							default:
							{
								$_SESSION["zig_var_current_${info}"] = $zig_return['value'] ;
								break ;
							}
						}
						break ;
					}
					default:
					{
						break ;
					}
				}
			}
		}

		$zig_return['return'] = 1 ;
		return $zig_return ;
	}

	function user()
	{
		return $this->username() ;
	}

	function username()
	{
		if(isset($_SESSION['zig_hash']))
		{
			require_once("../zig-api/lib/hash.lib.php") ;
			$zig_hash_obj = new zig_hash ;
			$zig_hash = $zig_hash_obj->hash_vars_decode($_SESSION['zig_hash']) ;
			$zig_return = $zig_hash['username'] ;
			return $zig_return ;
		}
	}

	function user_id()
	{
		if(isset($_SESSION['zig_hash']))
		{
			require_once("../zig-api/lib/hash.lib.php") ;
			$zig_hash_obj = new zig_hash ;
			$zig_hash = $zig_hash_obj->hash_vars_decode($_SESSION['zig_hash']) ;
			$zig_user = $zig_hash['username'] ;
			if($zig_user)
			{
				$sql = "SELECT `id` FROM `zig_users` WHERE `username`='${zig_user}' LIMIT 1" ;
				$result = zig("query",$sql) ;
				$fetch = $result->fetchRow() ;
				$zig_return = $fetch['id'] ;
			}
			return $zig_return ;
		}
	}

	function user_email()
	{
		if(isset($_SESSION['zig_hash']))
		{
			require_once("../zig-api/lib/hash.lib.php") ;
			$zig_hash_obj = new zig_hash ;
			$zig_hash = $zig_hash_obj->hash_vars_decode($_SESSION['zig_hash']) ;
			$zig_user = $zig_hash['username'] ;
			if($zig_user)
			{
				$sql = "SELECT `email` FROM `zig_users` WHERE `username`='${zig_user}' LIMIT 1" ;
				$result = zig("query",$sql) ;
				$fetch = $result->fetchRow() ;
				$zig_return = $fetch['email'] ;
			}
			return $zig_return ;
		}
	}

	function group()
	{
		if(isset($_SESSION['zig_hash']))
		{
			require_once("../zig-api/lib/hash.lib.php") ;
			$zig_hash_obj = new zig_hash ;
			$zig_hash = $zig_hash_obj->hash_vars_decode($_SESSION['zig_hash']) ;
			$zig_return = $zig_hash['username'] ;
			return $zig_return ;
		}
	}
	
	function group_id()
	{
		if(isset($_SESSION['zig_hash']))
		{
			require_once("../zig-api/lib/hash.lib.php") ;
			$zig_hash_obj = new zig_hash ;
			$zig_hash = $zig_hash_obj->hash_vars_decode($_SESSION['zig_hash']) ;
			$zig_group = $zig_hash['username'] ;
			if($zig_user)
			{
				$sql = "SELECT `id` FROM `zig_groups` WHERE `groupname`='${zig_group}' LIMIT 1" ;
				$result = zig("query",$sql) ;
				$fetch = $result->fetchRow() ;
				$zig_return = $fetch['id'] ;
			}
			return $zig_return ;
		}
	}

	function tab()
	{
		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$script = $GLOBALS['zig']['current']['script'] ;
		$module = $GLOBALS['zig']['current']['module'] ;
		$script = addslashes($script) ;
		$sql = "SELECT `${pre}tabs`.`name` 
				FROM `${zig_global_database}`.`${pre}tabs`,`${zig_global_database}`.`${pre}applications` 
				WHERE 
					`directory`='${module}' 
				AND `${pre}tabs`.`module`=`${pre}applications`.`name` 
				AND `${pre}tabs`.`link`='${script}' LIMIT 1" ;
		$result = zig("query",$sql) ;
		$fetch = $result->fetchRow() ;

		return $fetch['name'] ;
	}
}

?>