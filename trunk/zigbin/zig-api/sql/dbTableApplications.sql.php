<?php

class zig_dbTableApplications {
	function dbTableApplications($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL) {
		if($arg1)
		{
			$method = $arg1 ;
		}
		if(is_array($parameters))
		{
			$method = array_key_exists("method ",$parameters) ? $parameters['method '] : false ;
		}
		$zig_return['value'] = $method ? $this->$method() : false ;
		return $zig_return ;
	}

	function getApplicationDirectories() {
		switch(is_object(zig("checkArray",$GLOBALS['zig'],"adodb"))) {
			case false:
			{
				$directories[] = "zig-api" ;
				break ;
			}
			default: {
				$sql = "SELECT `directory` FROM `zig_applications`" ;
				$result = zig("query",$sql) ;
				while($fetch=$result->fetchRow())
				{
					$directories[] = $fetch['directory'] ;
				}
			}
		}
		return $directories ;
	}

	function getApplicationNames() {
		switch(is_object($GLOBALS['zig']['adodb'])) {
			case false: {
				$names[] = "zigbin" ;
				break ;
			}
			default: {
				$sql = "SELECT `name` FROM `zig_applications`" ;
				$result = zig("query",$sql) ;
				while($fetch=$result->fetchRow())
				{
					$names[] = $fetch['name'] ;
				}
			}
		}
		return $names ;
	}
}

?>