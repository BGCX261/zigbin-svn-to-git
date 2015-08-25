<?php

class zig_jscript_event
{
	function jscript_event($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$element_id = $arg1 ;
			$event = $arg2 ;
			$expression = $arg3 ;
			$bubbling = "false" ;
		}
		if(is_array($parameters))
		{
			$element_id = array_key_exists("element_id",$parameters) ? $parameters['element_id'] : NULL ;
			$event = array_key_exists("event",$parameters) ? $parameters['event'] : NULL ;
			$expression = array_key_exists("expression",$parameters) ? $parameters['expression'] : NULL ;
			$bubbling = array_key_exists("bubbling",$parameters) ? ($parameters['bubbling'] ? "true" : "false") : "false" ;
		}

		$zig_result['value'] = "zig_listener(document.getElementById('${element_id}'),'${event}',${expression},${bubbling}) ;" ;
		$zig_result['return'] = 1 ;

		return $zig_result ;
	}
}

?>