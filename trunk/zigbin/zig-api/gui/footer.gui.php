<?php

class zig_footer
{
	function footer($parameters,$arg1='',$arg2='',$arg3='')
	{
		$footer = NULL ;
		if($arg1)
		{
			$footer = $arg1 ? $arg1 : NULL ;
		}
		if(is_array($parameters))
		{
			$footer = array_key_exists("footer",$parameters) ? $parameters['footer'] : NULL ;
		}

		$footer = $footer ? $footer : zig("config","footer text") ;
		$buffer = zig("template","file","footer") ;
		$zig_result['value'] = str_replace("{footer}",$footer,$buffer) ;
		$zig_result['return'] = 1 ;
		
		return $zig_result ;
	}
}

?>