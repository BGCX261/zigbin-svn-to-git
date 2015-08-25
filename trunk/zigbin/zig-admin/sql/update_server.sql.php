<?php

class zig_update_database
{
	function update_database($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$username = $arg1 ;
			$password = $arg2 ;
			$database = (isset($arg3) and $arg3) ? $arg3 : "zigbin" ;
			$host = "localhost" ;
		}
		else if(is_array($parameters))
		{
			$username = array_key_exists("username",$parameters) ? $parameters['username'] : NULL ;
			$password = array_key_exists("password",$parameters) ? $parameters['password'] : NULL ;
			$database = array_key_exists("database",$parameters) ? $parameters['database'] : "zigbin" ;
			$host = array_key_exists("host",$parameters) ? $parameters['host'] : "localhost" ;
		}

		require_once("../zig-api/plugins/adodb/adodb.inc.php") ;
		$zig_adodb = NewADOConnection('mysql') ;
		$zig_adodb->Connect($host, $username, $password, $database) ;
		$connected = is_object($zig_adodb) ? $zig_adodb->IsConnected() : false ;
		if($connected)
		{
			$sql = "SHOW TABLES FROM ${database}" ;
			$sql_parameters = array
			(
				"function"	=>	"query",
				"sql"		=>	$sql,
				"move"		=>	false,
				"log"		=>	false,
				"connection"=>	$zig_adodb
			) ;
			$result = zig($sql_parameters) ;
			if($result->RecordCount())
			{
				print "Database is not empty!" ;
			}
			else
			{
				$sql = NULL ;
				$file = "../zig-admin/sql/install.sql" ;
				$handle = fopen($file,"r") ;
				while(!feof($handle))
				{
					$file_line = fgets($handle) ;
					if(substr($file_line,0,2)<>"--")
					{
						$sql.= $file_line ;
						if(substr(trim($file_line),-1,1)==";")
						{
							$sql_parameters['sql'] = $sql ;
							zig($sql_parameters) ;
							$sql = NULL ;
						}
					}
				}
				fclose($handle) ;

				// -- Start Global Database on Config Table
/*				$zig_global_database = zig("config","global database") ;
				$pre = zig("config","pre") ;
				$result = zig("query","SELECT `id`,`name` FROM `${zig_global_database}`.`${pre}config` WHERE `name`='global database' LIMIT 1") ;
				if($result->RecordCount())
				{
					$fetch = $result->fetchRow() ;
					zig("query","UPDATE `${zig_global_database}`.`${pre}config` SET `value`='${database}' WHERE `id`='$fetch[id]' LIMIT 1") ;
				}
				else
				{
					zig("query","INSERT INTO `${zig_global_database}`.`${pre}config` ('zig_created','zig_user','value') VALUES(NOW(),'install.lib.php','${database}')") ;
				}*/
				// -- End Global Database on Config Table

				print "Installation successful and complete!" ;
			}
		}
		else
		{
			print "Check if '${database}' database exists & '${username}' user does have privilege on this database" ;
		}
	}
}

?>