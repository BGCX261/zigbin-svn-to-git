<?php

class zig_content
{
	function content($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$zig_return['buffer'] = $arg1 ;
			$zig_return['message'] = $arg2 ;
			$security = $arg3<>"" ? $arg3 : true ;
		}
		if(is_array($parameters))
		{
			$zig_return['buffer'] = array_key_exists("content",$parameters) ? $parameters['content'] : $arg1 ;
			$zig_return['message'] = array_key_exists("message",$parameters) ? $parameters['message'] : $arg2 ;
			$security = array_key_exists("security",$parameters) ? $parameters['security'] : $security ;
			if(array_key_exists("topmenu",$parameters))
			{
				$zig_return['topmenu'] = $parameters['topmenu'] ;
			}
			if(array_key_exists("applications",$parameters))
			{
				$zig_return['applications'] = $parameters['applications'] ;
			}
		}

		if($security)
		{
			zig("security") ;
		}
		$zig_return['messenger'] = $zig_return['message'] ;
		$zig_return['actions'] = false ;
		return $zig_return ;
	}
}

?>