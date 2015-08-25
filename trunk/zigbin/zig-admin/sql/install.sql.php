<?php

class zig_install {
	function install($parameters,$arg1,$arg2,$arg3) {
		$default_database = true ;
		$host = "localhost" ;
		$pre = "zig_" ;
		$database = "zigbin" ;
		if($arg1 or $arg2 or $arg3) {
			$username = $arg1 ;
			$password = $arg2 ;
			$database = (isset($arg3) and $arg3) ? $arg3 : $database ;
		}
		if(is_array($parameters)) {
			$username = array_key_exists("username",$parameters) ? $parameters['username'] : (isset($username) ? $username : NULL) ;
			$password = array_key_exists("password",$parameters) ? $parameters['password'] : (isset($password) ? $password : NULL) ;
			$database = array_key_exists("database",$parameters) ? $parameters['database'] : $database ;
			$default_database = array_key_exists("default_database",$parameters) ? $parameters['default_database'] : $default_database ;
			$host = array_key_exists("host",$parameters) ? $parameters['host'] : $host ;
			$pre = array_key_exists("pre",$parameters) ? $parameters['pre'] : $pre ;
			$empty_database = array_key_exists("empty_database",$parameters) ? $parameters['empty_database'] : false ;
		}

		require("../zig-api/configs/default/filesPath.configs.php") ;
		if(is_writable("${filesPath}")) {
			require_once("../zig-api/plugins/adodb/adodb.inc.php") ;
			$zig_global_database = $database ;
			$zig_adodb = NewADOConnection("mysql") ;
			$zig_adodb->Connect($host, $username, $password, $database) ;
			$connected = is_object($zig_adodb) ? $zig_adodb->IsConnected() : false ;
			if(!$connected) {
				$zig_adodb->Connect($host, $username, $password, "mysql") ;
				$connected = is_object($zig_adodb) ? $zig_adodb->IsConnected() : false ;
				if(!$connected)
				{
					$zig_adodb->Connect($host, "root","","test") ;
					$connected = is_object($zig_adodb) ? $zig_adodb->IsConnected() : false ;
					if(!$connected and $host<>"localhost")
					{
						$zig_adodb->Connect("localhost", "root") ;
						$connected = is_object($zig_adodb) ? $zig_adodb->IsConnected() : false ;
					}
				}
				if($connected)
				{
					$sql = "CREATE DATABASE IF NOT EXISTS `${database}`" ;
					$sql_parameters = array
					(
						"function"	=>	"query",
						"sql"		=>	$sql,
						"move"		=>	false,
						"log"		=>	false,
						"connection"=>	$zig_adodb
					) ;
					zig($sql_parameters) ;
					$zig_adodb->Connect($host, $username, $password, $database) ;
					$connected = is_object($zig_adodb) ? $zig_adodb->IsConnected() : false ;
				}
			}

			if($connected) {
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
				$record_count = $result->RecordCount() ;
				if($record_count and !$empty_database) {
					print zig("template","file","install_empty_database") ;
				}
				else {
					// -- start empty database
					if($record_count)
					{
						while($fetch=$result->fetchRow())
						{
							$tables.= $tables ? ",".$fetch["Tables_in_${database}"] : $fetch["Tables_in_${database}"] ;
						}
						$sql_parameters['sql'] = "DROP TABLE ${tables}" ;
						zig($sql_parameters) ;
					}
					// -- end empty database
	
					// -- start online install
	
					if(zig("cache","file_exists","${filesPath}/zig-api/configs/".$_SERVER['HTTP_HOST']."/updates.configs.php"))
					{
						require_once("${filesPath}/zig-api/configs/".$_SERVER['HTTP_HOST']."/updates.configs.php") ;
					}
					else if(zig("cache","file_exists","${filesPath}/zig-api/configs/default/updates.configs.php"))
					{
						require_once("${filesPath}/zig-api/configs/default/updates.configs.php") ;
					}
					else if(zig("cache","file_exists","../zig-api/configs/".$_SERVER['HTTP_HOST']."/updates.configs.php"))
					{
						require("../zig-api/configs/".$_SERVER['HTTP_HOST']."/updates.configs.php") ;
					}
					else
					{
						require("../zig-api/configs/default/updates.configs.php") ;
					}
					$parameters = array
					(
						"function"	=>	"db_connect",
						"host"		=>	$updatesDbConfig['dbHost'],
						"database"	=>	$updatesDbConfig['dbName'],
						"username"	=>	$updatesDbConfig['dbUsername'],
						"password"	=>	$updatesDbConfig['dbPassword']
					) ;
					$updates_adodb = zig($parameters) ;
					$connected = is_object($updates_adodb) ? $updates_adodb->IsConnected() : false ;
					if($connected) {
						set_time_limit(180) ;
						$update_result = zig(array("function"=>"update_database","connection"=>$zig_adodb,"type"=>"commit","module"=>"zig-admin")) ;
					// -- end online install
					}
					else {
						// -- start local install
						print "Unable to update!<br />" ;
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
						// -- end local install
					}

					// -- start create database username and password
					$counter = 0 ;
					$username = $newUsername = "zigbin" ;
					while(true) {
						$counter++ ;
						$sql = "SELECT user FROM mysql.user WHERE user = '${newUsername}' LIMIT 1" ;
						$parameters = array(
							"function"		=>	"query",
							"sql"			=>	$sql,
							"connection"	=>	$zig_adodb
						) ;
						$result = zig($parameters) ;
						if($result->RecordCount()>0)
						{
							$newUsername = $username.$counter ;
							continue ;
						}
						else
						{
							$password = zig("grant",$database,$newUsername,$zig_adodb) ;
							break ;
						}
					}
					$username = $newUsername ;
					// -- end create database username and password

					// -- start insert global configurations on config table
					$sql_parameters['sql'] = "SELECT `id`,`name` 
												FROM `${pre}configs` 
												WHERE 
													`name`='global_database' AND `config`='${database}' 
												LIMIT 1" ;
					$result = zig($sql_parameters) ;
					if($result->RecordCount())
					{
						$fetch = $result->fetchRow() ;
						$sql_parameters['sql'] = "UPDATE `${pre}configs` SET `value`='${database}' WHERE `id`='$fetch[id]' LIMIT 1" ;
					}
					else
					{
						$sql_parameters['sql'] = "INSERT INTO `${pre}configs`(`zig_created`,`zig_user`,`config`,`name`,`value`,`description`) VALUES(NOW(),'install.lib.php','zigbin','global_database','${database}','Global database')" ;
					}
					zig($sql_parameters) ;
					// -- end insert global configurations on config table

					// -- start write configuration file
					$settings_parameters = array (
						"function"			=>	"install_settings",
						"host"				=>	$host,
						"pre"				=>	$pre,
						"default_database"	=>	$default_database,
						"database"			=>	$database,
						"username"			=>	$username,
						"password"			=>	$password,
						"filesPath"			=>	$filesPath
					) ;
					$settings_result = $this->install_settings($settings_parameters,NULL,NULL,NULL) ;
					// -- end write configuration file

					if($settings_result) {
						print "Installation successful and complete!" ;	
					}
				}
			}
			else
			{
				print "Check if '${database}' database exists & '${username}' user does have privilege on this database" ;
			}
		}
		else
		{
			print "${filesPath} directory does not exists or is not writable.<br />Create it and make it writable then refresh this page." ;
		}
	}

	function install_settings($parameters,$arg1,$arg2,$arg3) {
		if($arg1 or $arg2 or $arg3) {
			$host = "localhost" ;
			$pre = "zig_" ;
			$default_database = true ;
			$database = $arg1 ? $arg1 : "zigbin" ;
			$username = $arg2 ? $arg2 : "zigbin" ;
			$password = $arg3 ? $arg3 : NULL ;
		}
		if(is_array($parameters)) {
			$host = array_key_exists("host",$parameters) ? $parameters['host'] : $host ;
			$pre = array_key_exists("pre",$parameters) ? $parameters['pre'] : $pre ;
			$default_database = array_key_exists("default_database",$parameters) ? $parameters['default_database'] : $default_database ;
			$database = array_key_exists("database",$parameters) ? $parameters['database'] : $database ;
			$username = array_key_exists("username",$parameters) ? $parameters['username'] : $username ;
			$password = array_key_exists("password",$parameters) ? $parameters['password'] : $password ;
			$filesPath = array_key_exists("filesPath",$parameters) ? $parameters['filesPath'] : NULL ;
		}
		$templateSettings = array(
			"function"	=> "template", 
			"module"	=> "zig-admin", 
			"method"	=> "file", 
			"file"		=> "install_settings"
		) ;
		$buffer = zig($templateSettings) ;
		$buffer = str_replace("{host}",$host,$buffer) ;
		$buffer = $host=="localhost" ? str_replace("{host_comment}","//",$buffer) : str_replace("{host_comment}","",$buffer) ;
		$buffer = str_replace("{pre}",$pre,$buffer) ;
		$buffer = $pre=="zig_" ? str_replace("{pre_comment}","//",$buffer) : str_replace("{pre_comment}","",$buffer) ;
		$buffer = str_replace("{database}",$database,$buffer) ;
		$buffer = str_replace("{username}",$username,$buffer) ;
		$buffer = str_replace("{password}",$password,$buffer) ;
		if(is_writable("${filesPath}/")) {
			$directory = $default_database ? "default" : $_SERVER['HTTP_HOST'] ;
			if(!zig("cache","file_exists","${filesPath}/zig-api/configs/".$directory."/")) {
				zig("cache","mkdir","${filesPath}/zig-api/configs/".$directory."/") ;
			}
			if(is_writable("${filesPath}/zig-api/configs/".$directory."/")) {
				zig("cache","fwrite","${filesPath}/zig-api/configs/".$directory."/settings.configs.php",$buffer) ;
				return true ;
			}
			else {
				print "${filesPath}/zig-api/configs/${directory}/ directory does not exists or is not writable.<br />Create it and make it writable then refresh this page." ;
				return false ;
			}
		}
		else {
			print "${filesPath} directory does not exists or is not writable.<br />Create it and make it writable then refresh this page." ;
			return false ;
		}
	}
}

?>