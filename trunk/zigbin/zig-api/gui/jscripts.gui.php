<?php

class zig_jscripts
{
	function jscripts($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$jscripts = $arg1 ;
			$parse = $arg2===false ? false : true ;
			$minify = $arg3===false ? false : true ;
			$module = $GLOBALS['zig']['current']['module'] ;
			$tags = true ;
			$server_mode = zig("config","server mode") ;
		}
		if(is_array($parameters))
		{
			$jscripts = array_key_exists("jscripts",$parameters) ? $parameters['jscripts'] : (array_key_exists("arg1",$parameters) ? $parameters['arg1'] : NULL) ;
			$parse = array_key_exists("parse",$parameters) ? $parameters['parse'] : (array_key_exists("arg2",$parameters) ? $parameters['arg2'] : true) ;
			$minify = array_key_exists("minify",$parameters) ? $parameters['minify'] : (array_key_exists("arg3",$parameters) ? $parameters['arg3'] : true) ;
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : $GLOBALS['zig']['current']['module'] ;
			$tags = array_key_exists("tags",$parameters) ? $parameters['tags'] : true ;
			$server_mode = array_key_exists("server_mode",$parameters) ? $parameters['server_mode'] : zig("config","server mode") ;
		}

		$parse = $server_mode == "production" ? false : $parse ;
		$filenames = is_array($jscripts) ? $jscripts : explode(",",$jscripts) ;
		$jscripts_tag = NULL ;
		foreach($filenames as $jscript_name)
		{
			$jscripts_filename = NULL ;
			$jscript_name = str_replace(".js","",$jscript_name) ;
			if(zig("cache","file_exists","../".$module."/".$GLOBALS['zig']['path']['jscripts']."/${jscript_name}.js"))
			{
				$jscripts_filename = "../".$module."/".$GLOBALS['zig']['path']['jscripts']."/${jscript_name}.js" ;
			}
			else if(zig("cache","file_exists","../".$module."/".$GLOBALS['zig']['path']['jscripts']."/${jscript_name}/${jscript_name}.js"))
			{
				$jscripts_filename = "../".$module."/".$GLOBALS['zig']['path']['jscripts']."/${jscript_name}/${jscript_name}.js" ;
			}
			else if(zig("cache","file_exists","../zig-api/jscripts/${jscript_name}.js"))
			{
				$jscripts_filename = "../zig-api/jscripts/${jscript_name}.js" ;
			}
			else if(zig("cache","file_exists","../zig-api/jscripts/$jscript_name/${jscript_name}.js"))
			{
				$jscripts_filename = "../zig-api/jscripts/$jscript_name/${jscript_name}.js" ;
			}

			$jscripts_filename = $jscripts_filename ? $jscripts_filename : (zig("cache","file_exists",$jscript_name) ? $jscript_name : NULL) ;
			if($jscripts_filename)
			{
				$jscripts_filename_minified = substr($jscripts_filename,0,strlen($jscripts_filename)-2)."minified.js" ;
				if($server_mode=="development" and zig("cache","file_exists",$jscripts_filename_minified))
				{
					zig("cache","unlink",$jscripts_filename_minified) ;
				}
				if(!zig("cache","file_exists",$jscripts_filename_minified))
				{
					$this->jscripts_minify("jscript_minify",$jscripts_filename,$jscripts_filename_minified) ;
				}
				if($parse)
				{
					$filesize = zig("cache","filesize",$jscripts_filename_minified) ;
					
					if($filesize)
					{
						$jscripts_tag.= zig("cache","fread",$jscripts_filename_minified) ;
					}
				}
				else
				{
					$jscripts_tag.= str_replace("{jscripts_filename}",$jscripts_filename_minified,zig("template","block","jscripts","jscript link")) ;
				}
			}
		}
		$jscripts_tag = ($jscripts_tag and $parse and $tags) ? str_replace("{jscripts}",$jscripts_tag,zig("template","block","jscripts","jscript parse")) : $jscripts_tag ;

		$zig_result['value'] = $jscripts_tag ;
		$zig_result['return'] = 1 ;

		return $zig_result ;
	}
	
	function jscripts_minify($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL)
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