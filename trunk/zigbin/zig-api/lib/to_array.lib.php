<?php

class zig_to_array
{
	function to_array($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$variable = $arg1 ;
		}
		else if(is_array($parameters))
		{
			$variable = array_key_exists("variable",$parameters) ? $parameters['variable'] : NULL ;
		}

		if(!is_array($variable))
		{
			if($variable)
			{
				$variable_single_value = $variable ;
				unset($variable) ;
			}
			$variable = array() ;
			if($variable_single_value)
			{
				$variable[] = $variable_single_value ;
			}
		}

		$zig_result['value'] = $variable ;
		$zig_result['return'] = 1 ;
		
		return $zig_result ;
	}
}

?>