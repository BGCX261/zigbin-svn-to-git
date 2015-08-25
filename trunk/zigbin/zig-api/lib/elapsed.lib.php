<?php

class zig_elapsed
{
	function elapsed($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$timestamp = $arg1 ;
		}
		else if(is_array($parameters))
		{
			$timestamp = array_key_exists("timestamp",$parameters) ? $parameters['timestamp'] : NULL ;
		}

		$zig_result['value'] = $this->elapsed_time($timestamp) ;
		$zig_result['return'] = 1 ;
		return $zig_result ;
	}

	function elapsed_time($timestamp)
	{
		$t = time() - strtotime($timestamp) ;
		if ($t<60)		
			if($t <> 1)
			{
				return $t . " seconds ago" ;
			}
			else
			{
				return $t . " seconds ago" ;
			}
		$t = round($t/60) ;
		if ($t<60)
			if($t <> 1)
			{
				return $t . " minutes ago" ;
			}
			else
			{
				return $t . " minute ago" ;
			}		
		$t = round($t/60);	
		if ($t<24)
		{
			if($t <> 1)
			{
				return $t . " hours ago" ;
			}
			else
			{
				return $t . " hour ago" ;
			}
		}		
		$t = round($t/24);
		if ($t<7)
			if($t <> 1)
			{
				return $t . " days ago" ;
			}
			else
			{
				return $t . " day ago" ;
			}
		$t = round($t/7) ;
		if ($t<4)
			if($t <> 1)
			{
				return $t . " weeks ago" ;
			}
			else
			{
				return $t . " week ago" ;
			}
		$t = round(($t*7)/30) ;
		if ($t<12)
			if($t <> 1)
			{
				return $t . " months ago" ;
			}
			else
			{
				return $t . " month ago" ;
			}
		$t = round($t/12) ;
//		if ($t<10)
			if($t <> 1)
			{
				return $t . " years ago" ;
			}
			else
			{
				return $t . " year ago" ;
			}
		return date("F j, Y", strtotime($date)) ;
	}

}

?>