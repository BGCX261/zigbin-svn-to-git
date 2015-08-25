<?php

class zig_select_defaults
{
	function select_defaults($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
		}
		if(is_array($parameters))
		{
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
		}
		$sql = "SELECT * FROM `${table}` WHERE `zig_status`='default'" ;

		$zig_return['return'] = 1 ;
		$zig_return['value'] = zig("query",$sql) ;

		return $zig_return ;
	}
}

?>