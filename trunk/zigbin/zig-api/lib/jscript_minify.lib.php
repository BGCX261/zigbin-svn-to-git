<?php

class zig_jscript_minify
{
	function jscript_minify($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$unminified_file = $arg1 ;
			$minified_file = $arg2 ;
		}
		if(is_array($parameters))
		{
			$unminified_file = array_key_exists("unminified_file",$parameters) ? $parameters['unminified_file'] : (array_key_exists("arg1",$parameters) ? $parameters['arg1'] : NULL) ;
			$minified_file = array_key_exists("minified_file",$parameters) ? $parameters['minified_file'] : (array_key_exists("arg2",$parameters) ? $parameters['arg2'] : NULL ) ;
		}

		$minified_file = $minified_file ? $minified_file : substr($unminified_file,0,strlen($unminified_file)-2)."minified.js" ;
		$file_buffer = zig("cache","fread",$unminified_file) ;
		$file_buffer = zig("minify",$file_buffer) ;
		$file_buffer - trim($file_buffer) ;
		zig("cache","fwrite",$minified_file,$file_buffer) ;

		$zig_result['return'] = 1 ;
		return $zig_result ;
	}
}

?>