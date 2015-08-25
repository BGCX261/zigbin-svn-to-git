<?php

class zig_wrapper
{
	function wrapper($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$buffer = $arg1 ;
			$function = $arg2 ;
			$jscript_events = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$buffer = array_key_exists("buffer",$parameters) ? $parameters['buffer'] : NULL ;
			$function = array_key_exists("function",$parameters) ? $parameters['function'] : NULL ;
			$jscript_events = array_key_exists("jscript_events",$parameters) ? $parameters['jscript_events'] : NULL ;
		}

		$development_mode = zig("config","server mode")=="development" ? true : false ;
		$buff = zig("template","file","wrapper") ;
		$buffer = zig("template",$buff,"{buffer}",$buffer) ;
		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$module = $GLOBALS['zig']['current']['module'] ;
		$script = $GLOBALS['zig']['current']['script'] ;
		$theme = $GLOBALS['zig']['default']['theme'] ;

		if($module<>"zig-api" and $module)
		{
			$sql = "SELECT `name` FROM ${pre}applications WHERE directory='${module}' LIMIT 1" ;
			$result = zig("query",$sql) ;
			$fetch = $result->fetchRow() ;
			$title = $fetch['name'] ? $fetch['name'] : $module ;
		}
		else
		{
			$title = zig("config","title") ;
		}

		$sql = "SELECT ${pre}tabs.name 
					FROM ${pre}tabs,${pre}applications 
					WHERE module=${pre}applications.name AND directory='$module' AND ${pre}tabs.link='$script' LIMIT 1" ;
		$result = zig("query",$sql) ;
		$fetch = $result->fetchRow() ;
		$splitted_script = explode(".",$script) ;
		$script = $splitted_script[0] ;
		$procedure = $fetch['name'] ? $fetch['name'] : ucwords(trim($script)) ;
		$procedure = ($title and $procedure) ? " | ".$procedure : $procedure ;
		$method = ($function and $function<>"wizard") ? $function : ($GLOBALS['zig']['current']['action'] ? $GLOBALS['zig']['current']['action'] : NULL) ;
		$method = ($method and ($title or $procedure)) ? " | ".ucwords(trim($method)) : ucwords(trim($method)) ;
		$zig_title = $title.$procedure.$method ;

		$zig_main_css_minified = "../".$GLOBALS['zig']['current']['module']."/gui/themes/".$GLOBALS['zig']['default']['theme']."/css/main.gui.minified.css" ;
		$zig_main_css_unminified = "../".$GLOBALS['zig']['current']['module']."/gui/themes/".$GLOBALS['zig']['default']['theme']."/css/main.gui.css" ;
		$zig_main_css_custom = $this->wrapper_css("wrapper_css",$zig_main_css_unminified,$zig_main_css_minified,$development_mode) ;

		$zig_main_css_minified = "../zig-api/gui/themes/${theme}/css/main.gui.minified.css" ;
		$zig_main_css_unminified = "../zig-api/gui/themes/${theme}/css/main.gui.css" ;
		$zig_main_css = $this->wrapper_css("wrapper_css",$zig_main_css_unminified,$zig_main_css_minified,$development_mode) ;

		$zig_additional_css_minified = "../".$GLOBALS['zig']['current']['module']."/gui/themes/".$GLOBALS['zig']['default']['theme']."/css/addon.gui.minified.css" ;
		$zig_additional_css_unminified = "../".$GLOBALS['zig']['current']['module']."/gui/themes/".$GLOBALS['zig']['default']['theme']."/css/addon.gui.css" ;
		$zig_additional_css = $this->wrapper_css("wrapper_css",$zig_additional_css_unminified,$zig_additional_css_minified,$development_mode) ;

		$zig_main_css = $zig_main_css_custom ? $zig_main_css_custom : $zig_main_css ;
		$zig_additional_css = $zig_additional_css ? "<link href='".$zig_additional_css."' rel='stylesheet' type='text/css' />" : NULL ;
		$zig_icon = zig("config","favicon") ;
		$zig_icon = zig("images",$zig_icon) ;

		$buffer = str_replace("{zig_main_css}",$zig_main_css,$buffer) ;
		$buffer = str_replace("{zig_additional_css}",$zig_additional_css,$buffer) ;
		$buffer = str_replace("{zig_title}",$zig_title,$buffer) ;
		$buffer = str_replace("{zig_icon}",$zig_icon,$buffer) ;

		// -- start javascript
		$directory = $GLOBALS['zig']['current']['module'] ;
		$development_mode = zig("config","server mode")=="development" ? true : false ;
		// -- Start parse head jscript
		$buffer = str_replace("{zig_head_jscripts}",$this->display_jscripts("display_jscripts","head_jscript","zig-api",$development_mode),$buffer) ;
		// -- End parse head jscript

		// -- Start parse custom head jscript
		$buffer = str_replace("{zig_custom_head_jscripts}",$this->display_jscripts("display_jscripts","custom_head_jscript",$directory,$development_mode),$buffer) ;
		// -- End parse custom head jscript

		// -- Start parse main body jscript
		$buffer = str_replace("{zig_body_jscripts}",$this->display_jscripts("display_jscripts","body_jscript","zig-api",$development_mode),$buffer) ;
		// -- End parse main body jscript

		// -- Start parse custom body jscript
		$buffer = str_replace("{zig_custom_body_jscripts}",$this->display_jscripts("display_jscripts","custom_body_jscript",$directory,$development_mode),$buffer) ;
		// -- End parse custom body jscript

		// -- Start parse jscript events
		$jscript_events = $jscript_events ? str_replace("{jscripts}",zig("minify",$jscript_events),zig("template","block","jscripts","jscript parse")) : NULL ;
		$buffer = str_replace("{zig_jscript_events}",$jscript_events,$buffer) ;
		// -- End parse jscript events

		// -- end javascript


		$zig_result['return'] = 1 ;
		$zig_result['value'] = $buffer ;
		return $zig_result ;
	}

	function wrapper_css($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$unminified_file = $arg1 ;
			$minified_file = $arg2 ;
			$development_mode = $arg3 ;
		}
		if(is_array($parameters))
		{
			$unminified_file = array_key_exists("unminified_file",$parameters) ? $parameters['unminified_file'] : (array_key_exists("arg1",$parameters) ? $parameters['arg1'] : NULL) ;
			$minified_file = array_key_exists("minified_file",$parameters) ? $parameters['minified_file'] : (array_key_exists("arg2",$parameters) ? $parameters['arg2'] : NULL ) ;
			$development_mode = array_key_exists("development_mode",$parameters) ? $parameters['development_mode'] : (array_key_exists("arg3",$parameters) ? $parameters['arg3'] : false ) ;
		}

		if($development_mode and zig("cache","file_exists",$minified_file))		
		{
			zig("cache","unlink",$minified_file) ;
		}
		if(zig("cache","file_exists",$minified_file))
		{
			return $minified_file ;
		}
		else if(zig("cache","file_exists",$unminified_file))
		{
			$this->wrapper_minify("minify",$unminified_file,$minified_file) ;
			return $minified_file ;
		}
		return NULL ;
	}

	function wrapper_minify($parameters,$arg1,$arg2,$arg3='')
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

		$minified_file = $minified_file ? $minified_file : substr($unminified_file,0,strlen($unminified_file)-2)."minified.css" ;
		$file_buffer = zig("cache","fread",$unminified_file) ;
		$file_buffer = zig("minify",$file_buffer) ;
		$file_buffer - trim($file_buffer) ;
		zig("cache","fwrite",$minified_file,$file_buffer) ;

		$zig_result['return'] = 1 ;
		return $zig_result ;
	}

	function display_jscripts($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$jscripts = $arg1 ;
			$directory = $arg2 ;
			$development_mode = $arg3 ;
		}
		else if(is_array($parameters))
		{
			
		}
		$pre = "zig_" ;
		$file_extension = ".js" ;
		$file_path = "../".$directory."/jscripts/" ;
		$jscript_filename = $pre.$jscripts.$file_extension ;
		$jscript_filename_minified = $pre.$jscripts.".minified".$file_extension ;

		if($development_mode)
		{
			// -- Start delete javascript if server is on development mode
			if(zig("cache","file_exists",$file_path.$jscript_filename))
			{
				zig("cache","unlink",$file_path.$jscript_filename) ;
			}
			if(zig("cache","file_exists",$file_path.$jscript_filename_minified))
			{
				zig("cache","unlink",$file_path.$jscript_filename_minified) ;
			}
			// -- End delete javascript if server is on development mode

			// -- Start build javascript
			if(!zig("cache","file_exists",$file_path.$jscript_filename))
			{
				$jscripts = zig("config",$jscripts) ;
				$jscripts_parameters = array
				(
					"function"	=>	"jscripts",
					"jscripts"	=>	$jscripts,
					"minify"	=>	false,
					"module"	=>	$directory,
					"tags"		=>	false
				) ;
				$file_buffer = trim(zig($jscripts_parameters)) ;
				if($file_buffer)
				{
					zig("cache","fwrite",$file_path.$jscript_filename,$file_buffer) ;
				}
			}
			// -- End build javascript
		}
		return zig("jscripts",$jscript_filename,$development_mode) ;
	}
}

?>