<?php

class zig_addon_validate
{
	function addon_validate($parameters,$arg1='',$arg2='',$arg3='')
	{
/*		if($arg1 or $arg2 or $arg3)
		{
			$column_info = $arg1 ;
			$field_value = $arg2 ;
			$table = $arg3 ;
		}
		if(is_array($parameters))
		{
			$column_info = array_key_exists("column_info",$parameters) ? $parameters['column_info'] : NULL ;
			$field_value = array_key_exists("field_value",$parameters) ? $parameters['field_value'] : NULL ;
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
		}

		$return_values['message'] = NULL ;
		$return_values['validation'] = $validation = true ;*/

		$return_values['validation'] = true ;
		$zig_return['value'] = $return_values ;
		$zig_return['return'] = 1 ;
		return $zig_return ;
	}
	
}

?>