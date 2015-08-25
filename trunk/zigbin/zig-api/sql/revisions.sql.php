<?php

class zig_revisions
{
	function revisions($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$data = $arg1 ;
			$id = $arg2 ;
		}
		else if(is_array($parameters))
		{
			$data = array_key_exists("data",$parameters) ? $parameters['data'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
		}

		$data = serialize($data) ;

		$zig_result['value'] = $data ;
		$zig_result['return'] = 1 ;
		return $zig_result ;
	}
}

?>