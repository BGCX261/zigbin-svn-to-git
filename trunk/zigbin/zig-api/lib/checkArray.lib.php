<?php
/*
 * methods:
 * arrayKeyExists
 * isArray
 * returnValue (default)
 */
class zig_checkArray
{
	function checkArray($parameters,$arg1='',$arg2='',$arg3='')
	{
		$index = false ;
		$valueCheckingMethod = "returnValue" ;
		if($arg1 or $arg2 or $arg3)
		{
			$variable = $arg1 ;
			$index = $arg2 ? $arg2 : $index ;
			$valueCheckingMethod = $arg3 ? $arg3 : $valueCheckingMethod ;
		}
		if(is_array($parameters))
		{
			$variable = array_key_exists("variable",$parameters) ? $parameters['variable'] : (isset($variable) ? $variable : false) ; 
			$index = array_key_exists("index",$parameters) ? $parameters['index'] : $index ;
			$valueCheckingMethod = array_key_exists("valueCheckingMethod",$parameters) ? $parameters['valueCheckingMethod'] : $valueCheckingMethod ;
		}
		$zig_result['value'] = false ;

		if(!($index===false))
		{
			switch(is_array($variable))
			{
				case true:
				{
					switch(array_key_exists($index,$variable))
					{
						case true:
						{
							switch($valueCheckingMethod)
							{
								case "arrayKeyExists":
								{
									$zig_result['value'] = true ;
									break ;
								}
								case "isArray":
								{
									$zig_result['value'] = is_array($variable[$index]) ? true : false ;
									break ;
								}
								default:
								{
									$zig_result['value'] = $variable[$index] ;
								}
							}
						}
					}
				}
			}
		}
		return $zig_result ;
	}	
}

?>