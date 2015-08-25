<?php

class zig_show_table_status
{
	function show_table_status($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$type = $arg1 ;
		}
		if(is_array($parameters))
		{
			$type = array_key_exists("type",$parameters) ? $parameters['type'] : NULL ;
		}
		switch($type)
		{
			case "zig":
			{
				$sql = "SHOW TABLE STATUS WHERE `comment`='zig'" ;
				break ;
			}
			default:
			{
				$sql = "SHOW TABLE STATUS" ;
				break ;
			}
		}

		$zig_return['return'] = 1 ;
		$zig_return['value'] = zig("query",$sql) ;

		return $zig_return ;
	}
}

?>