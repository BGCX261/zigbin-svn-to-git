<?php

class zig_error
{
	function error($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$code = $arg1 ;
			$type = $type ;
			$vars = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$code = array_key_exists("code",$parameters) ? $parameters['code'] : NULL ;
			$type = array_key_exists("type",$parameters) ? $parameters['type'] : NULL ;
			$vars = array_key_exists("vars",$parameters) ? $parameters['vars'] : NULL ;
		}
		else
		{
			$code = $parameters ;
			$type = "error" ;
		}

		require_once("../zig-api/lib/cache.lib.php") ;
		$zig_cache_object = new zig_cache ;
		$file_exists = $zig_cache_object->cache("cache","file_exists","../zig-api/configs/".$_SERVER['HTTP_HOST']."/settings.configs.php") ;
		if(!$file_exists['value'])
		{
			$file_exists = $zig_cache_object->cache("cache","file_exists","../zig-api/configs/default/settings.configs.php") ;
		}
		if($file_exists['value'])
		{
			$pre = zig("config","pre") ;
			$zig_global_database = zig("config","global_database") ;
			$table = $zig_global_database.".".$pre."configs" ;
			$sql = "SELECT `description` FROM `$zig_global_database`.`${pre}categories` WHERE `parent_name`='Messages' AND `name`='${type}-${code}' LIMIT 1";
			$result = zig("query",$sql) ;
	
			if($result->RecordCount($result))
			{
				$fetch=$result->fetchRow() ;
    	        print "${type}-${code} : $fetch[description]" ;
			}
			else
			{
				print "${type}-${code} : Please contact your administrator... " ;
			}
		}
		else
		{
				print "${type}-${code} : Please contact your administrator... " ;
		}

		$backtrace = "<pre>".debug_print_backtrace()."</pre>" ;
		$zig_return['return'] = 1 ;
		$zig_return['value'] = $backtrace ;

		return $zig_return ;
	}	
}

?>