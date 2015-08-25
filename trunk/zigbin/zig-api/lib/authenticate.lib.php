<?php

class zig_authenticate
{
	function authenticate($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$method = $arg1 ? $arg1 : zig("config","authentication") ;
			$username = $arg2 ;
			$password = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$method = array_key_exists("method",$parameters) ? $parameters['method'] : zig("config","authentication") ;
			$username = array_key_exists("username",$parameters) ? $parameters['username'] : NULL ;
			$password = array_key_exists("password",$parameters) ? $parameters['password'] : NULL ;
		}
		
		$method = zig("to_array",$method) ;	
		
		foreach($method as $type)
		{
			switch($type)
			{
				case "ldap":
				{
					$authenticated = $this->ldap($ldap, $ldap, $username, $password) ;
					break ;
				}
				case "database":
				default:
				{
					$authenticated = $this->database("database", "database", $username, $password) ;
					break ;
				}
			}
			if($authenticated)
			{
				break ;
			}
		}
		$zig_result['value'] = $authenticated ;
		$zig_result['return'] = 1 ;
		
		return $zig_result ;
	}

	function database($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1)
		{
			$method = $arg1 ;
			$username = $arg2 ;
			$password = $arg3 ;
		}
		else
		{
			$method = $parameters['method'] ;
			$username = $parameters['username'] ;
			$password = $parameters['password'] ;
		}

		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$sql = "SELECT id,expiration FROM $zig_global_database.${pre}users WHERE username='$username' AND password=PASSWORD('$password') LIMIT 1" ;
		$authenticate_result = zig("query",$sql) ;
		$record_count = $authenticate_result->RecordCount() ;
		if($record_count)
		{
			$authenticate_result->MoveFirst() ;
			$zig_fetch = $authenticate_result->fetchRow() ;
			$current_date = date("Y-m-d") ;
			if($zig_fetch['expiration'] < $current_date and $zig_fetch['expiration']<>"" and $zig_fetch['expiration']<>NULL)
			{
				$record_count = 0 ;
			}
		}
		return $record_count ;
	}
	
	function ldap($parameters,$arg1='',$arg2='',$arg3='')
	{
		
		if($arg1)
		{
			$method = $arg1 ;
			$username = $arg2 ;
			$password = $arg3 ;
		}
		else
		{
			$method = $parameters['method'] ;
			$username = $parameters['username'] ;
			$password = $parameters['password'] ;
		}

		$zig_result = zig("ldap","bind",$username,$password) ;
		return $zig_result ;
	}
}

?>