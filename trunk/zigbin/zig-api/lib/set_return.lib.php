<?php

class zig_set_return
{
	function set_return($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$zig_result = $arg1 ;
			$return_config = $arg2 ;
		}
		if(is_array($parameters))
		{
			$zig_result = array_key_exists("zig_result",$parameters) ? $parameters['zig_result'] : NULL ;
			$return_config = array_key_exists("return_config",$parameters) ? $parameters['return_config'] : NULL ;
		}

		switch($return_config)
		{
			case 0 :
				$zig_return = NULL ;
				break ;
			case 1 :
				$zig_return = is_array($zig_result) ? (array_key_exists("value",$zig_result) ? $zig_result['value'] : $zig_result) : $zig_result ;
				break ;
			case 2 :
				$zig_return = array
				(
					'value' 	=>	is_array($zig_result) ? (array_key_exists("value",$zig_result) ? $zig_result['value'] : NULL) : NULL ,
					'config'	=>	is_array($zig_result) ? (array_key_exists("config",$zig_result) ? $zig_result['config'] : NULL) : NULL
				);
				break ;
			case 3 :
				$zig_return = isset($zig_result) ? $zig_result : NULL ;
				break ;
			default:
				$zig_return = NULL ;
				break ;
		}
		return $zig_return ;
	}	
}
?>