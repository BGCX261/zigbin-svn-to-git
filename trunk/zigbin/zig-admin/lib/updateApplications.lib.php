<?php

class zig_updateApplications
{
	function updateApplications($parameters,$arg1='',$arg2='',$arg3='')
	{
		$mode = "pull" ;
		if($arg1 or $arg2 or $arg3)
		{
			$mode = $arg1 ? $arg1 : $mode ;
		}
		if(is_array($parameters))
		{
			$mode = array_key_exists("mode",$parameters) ? $parameters['mode'] : $mode ;
		}
		$parameters['function'] = "updateCodes" ;
		$parameters['mode'] = $mode ;
		$message = zig($parameters) ;
		$parameters['function'] = "update_database" ;
		$message.= zig($parameters) ;
	}
}

?>