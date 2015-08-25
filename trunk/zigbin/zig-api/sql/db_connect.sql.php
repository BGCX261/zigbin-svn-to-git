<?php

class zig_db_connect
{
	function db_connect($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$host = $arg1 ? $arg1 : "localhost" ;
			$username = $arg2 ;
			$password = $arg3 ;
			$database = NULL ;
		}
		if(is_array($parameters))
		{
			$host = array_key_exists("host",$parameters) ? $parameters["host"] : $host ;
			$username = array_key_exists("username",$parameters) ? $parameters["username"] : $username ;
			$password = array_key_exists("password",$parameters) ? $parameters["password"] : $password ;
			$database = array_key_exists("database",$parameters) ? $parameters["database"] : $database ;
		}

		$zig_adodb = NewADOConnection("mysql") ;
		$zig_adodb->Connect($host, $username, $password, $database) ;

		$zig_return['return'] = 1 ;
		$zig_return['value'] = $zig_adodb ;
		return $zig_return ;
	}
}

?>