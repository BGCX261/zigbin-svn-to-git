<?php

class zig_grant
{
	function grant($parameters,$arg1,$arg2,$arg3)
	{
		$database = "*" ;
		$password = uniqid() ;
		$host = "localhost" ;
		if($arg1 or $arg2 or $arg3)
		{
			$database = $arg1 ? $arg1 : $database ;
			$username = $arg2 ;
			$connection = $arg3 ? $arg3 : NULL ;
		}
		if(is_array($parameters))
		{
			$database = array_key_exists("database",$parameters) ? $parameters['database'] : $database ;
			$username = array_key_exists("username",$parameters) ? $parameters['username'] : (isset($username) ? $username : NULL) ;
			$password = array_key_exists("password",$parameters) ? $parameters['password'] : $password ;
			$host = array_key_exists("host",$parameters) ? $parameters['host'] : $host ;
		}
		$sqlParameters = array(
			"function"	=>	"query",
			"sql"		=>	"GRANT ALL PRIVILEGES ON ${database}.* TO ${username}@${host} IDENTIFIED BY '${password}'",
			"log"		=>	false,
			"move"		=>	false,
			"connection"=>	$connection
		) ;
		zig($sqlParameters) ;
		$sqlParameters['sql'] = "FLUSH PRIVILEGES" ;
		zig($sqlParameters) ;
		$zig_return['return'] = 1 ;
		$zig_return['value'] = $password ;

		return $zig_return ;
	}
}

?>