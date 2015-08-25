<?php
	function __autoload($className) {
		switch(substr($className, 0,4)) {
			case "zig_": {
				$className = substr($className, 4) ;
			}
		}
		include("../zig-api/lib/$className.lib.php") ;
	}

	function zig($parameters,$arg1='',$arg2='',$arg3='') {
		if(!session_id()) {
			session_start() ;
		}
		// -- Start load zig required classes ; This is in priority order, DO NOT SWAP!
		$httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "" ;
		require("../zig-api/configs/default/filesPath.configs.php") ;
		require_once("../zig-api/lib/cache.lib.php") ;
		$zig_cache_object = new zig_cache ;
		$file_exists = $zig_cache_object->cache("cache","file_exists","../zig-api/lib/db.lib.php") ;
		if($file_exists['value'])
		{
			$file_exists = $zig_cache_object->cache("cache","file_exists","${filesPath}/zig-api/configs/${httpHost}/settings.configs.php") ;
			if(!$file_exists['value'])
			{
				$file_exists = $zig_cache_object->cache("cache","file_exists","${filesPath}/zig-api/configs/default/settings.configs.php") ;
			}
			if($file_exists['value'])
			{
				require_once("../zig-api/lib/db.lib.php") ;
			}
		}
		require_once("../zig-api/lib/configs.lib.php") ;
		$zig_config_obj = null ;
		$file_exists = $zig_cache_object->cache("cache","file_exists","../zig-api/lib/config.lib.php") ;
		if($file_exists['value'])
		{
			$file_exists = $zig_cache_object->cache("cache","file_exists","${filesPath}/zig-api/configs/default/settings.configs.php") ;
			if(!$file_exists['value'])
			{
				$file_exists = $zig_cache_object->cache("cache","file_exists","${filesPath}/zig-api/configs/${httpHost}/settings.configs.php") ;
			}
			if($file_exists['value'])
			{
				require_once("../zig-api/lib/config.lib.php") ;
				$zig_config_obj = new zig_config ;
				$server_mode = $zig_config_obj->config("config","server mode") ;
				if($server_mode['value']=="development" and is_array($_SESSION))
				{
					if(array_key_exists("zig_cache",$_SESSION))
					{
						unset($_SESSION['zig_cache']) ;
					}
				}
			}
		}
		// -- End load zig required classes ; This is in priority order, DO NOT SWAP!

		// Start Load Required Classes
		require_once("../zig-api/lib/set_return.lib.php") ;
		require_once("../zig-api/lib/error.lib.php") ;
		$GLOBALS['zig']['obj']['error'] = new zig_error ;
		$set_return_obj = new zig_set_return ;
		// End Load Required Classes

		switch(is_array($parameters))
		{
			case true:
			{
				$function = array_key_exists("function",$parameters) ? $parameters['function'] : $parameters ;
				break ;
			}
			default:
			{
				$function = $parameters ;
			}
		}

		foreach($GLOBALS['zig']['dir'] as $value)
		{
			$module = "" ;
			if(is_array($parameters)) {
				$module = array_key_exists("module",$parameters) ? $parameters['module'] : NULL ;
				//$GLOBALS['zig']['current']['module'] = $module ? $module : $GLOBALS['zig']['current']['module'] ;
			}

			if($module) {
				$file_exists = $zig_cache_object->cache("cache","file_exists","../$module/$value/$function.$value.php") ;
				if(!$file_exists['value']) {
					$module = "" ;
				}
			}

			if(!$module and $GLOBALS['zig']['current']['module']<>$GLOBALS['zig']['path']['api']) {
				$file_exists = $zig_cache_object->cache("cache","file_exists","../".$GLOBALS['zig']['current']['module']."/$value/$function.$value.php") ;
				if($file_exists['value']) {
					$module = $GLOBALS['zig']['current']['module'] ;
				}
				else {
					$module = $GLOBALS['zig']['path']['api'] ;
				}
			}
			else if(!$module) {
				$module = $GLOBALS['zig']['path']['api'] ;
			}
			$file_exists = $zig_cache_object->cache("cache","file_exists","../$module/$value/$function.$value.php") ;

			if($file_exists['value']) {
				$class = "zig_".$function ;
				require_once("../$module/$value/$function.$value.php") ;
				$zig_object = new $class ;
				if(is_array($parameters))
				{
					$arg1 = $arg1 ? $arg1 : (array_key_exists("arg1",$parameters) ? $parameters['arg1'] : NULL) ;
					$arg2 = $arg2 ? $arg2 : (array_key_exists("arg2",$parameters) ? $parameters['arg2'] : NULL) ;
					$arg3 = $arg3 ? $arg3 : (array_key_exists("arg3",$parameters) ? $parameters['arg3'] : NULL) ;
				}
				$zig_result = $zig_object->$function($parameters,$arg1,$arg2,$arg3) ;

				if(is_array($zig_result))
				{
					// -- Start passing values
					$zig_result['function'] = $function ;
					$zig_result['print_view'] = array_key_exists("print_view",$zig_result) ? $zig_result['print_view'] : (is_array($parameters) ? (array_key_exists("print_view",$parameters) ? $parameters['print_view'] : false) : false) ;
					// -- end passing values

					if(array_key_exists("buffer",$zig_result) and $zig_result['buffer']<>"")
					{
						$action = $_SERVER['REQUEST_URI'] ;
						if(is_array($parameters)) {
							$action = array_key_exists("action",$parameters) ? $parameters['action'] : $_SERVER['REQUEST_URI'] ;
						}
						$zig_result['buffer'] = str_replace("{class}",$class,$zig_result['buffer']) ;
						$zig_result['buffer'] = str_replace("{form_action}",$action,$zig_result['buffer']) ;
					}
				}
				break;
			}
		}

		if(isset($zig_result['gui_error']))
		{
			$error_flag = $zig_result['gui_error'] ;
		}
		else if(is_object($zig_config_obj))
		{
			$error_flag = $zig_config_obj->config("config","display errors") ;
		}
		else
		{
			$error_flag = true ;
		}

		if(isset($zig_object))
		{
			if(!is_object($zig_object) and $error_flag)
			{
				$zig_result['error'] = $zig_result['error'] ? "<br />".$GLOBALS['zig']['obj']['error']->error(101) : $GLOBALS['zig']['obj']['error']->error(101) ;
			}
		}

		// -- Start set the gui flag
		if(isset($zig_result['gui_buffer']))
		{
			$display_flag = $zig_result['gui_buffer'] ;
		}
		else if(is_array($parameters) and array_key_exists("gui_buffer",$parameters) and isset($parameters['gui_buffer']))
		{
			$display_flag =	$parameters['gui_buffer'] ;
		}
		else
		{
			$zig_display_flag =	is_object($zig_config_obj) ? $zig_config_obj->config("config","display errors") : array("value"=>true) ;
			$display_flag =	$zig_display_flag['value'] ;
		}
		// -- End set the gui flag

		if(isset($zig_result))
		{
			if(is_array($zig_result))
			{
				$zig_result['buffer'] = array_key_exists("buffer",$zig_result) ? $zig_result['buffer'] : NULL ;
				$zig_result['error'] = array_key_exists("error",$zig_result) ? $zig_result['error'] : NULL ;
				$zig_result['warning'] = array_key_exists("warning",$zig_result) ? $zig_result['warning'] : NULL ;
				$zig_result['system'] = array_key_exists("system",$zig_result) ? $zig_result['system'] : NULL ;
				$zig_result['message'] = array_key_exists("message",$zig_result) ? $zig_result['message'] : NULL ;
			}
		}

		if($display_flag and isset($zig_result))
		{
			if(is_array($zig_result))
			{
			if(($zig_result['buffer'] or $zig_result['error'] or $zig_result['warning'] or $zig_result['system'] or $zig_result['message']))
			{
				$not_zigjax = true ;				if(is_array($parameters))
				{
					if(array_key_exists("zigjax",$parameters) and $parameters['zigjax'])
					{
						$zig_result['value'] = $zig_result['value'] ? $zig_result['value'] : $zig_result['error'].$zig_result['warning'].$zig_result['system'].$zig_result['message'] ;
						$not_zigjax = false ;
					}
				}

				if($not_zigjax)
				{
					require_once("../zig-api/lib/display_flags.lib.php") ;
					$display_flags_obj = new zig_display_flags ;
					$display_flags_value = $display_flags_obj->display_flags("display_flags",$zig_result,$parameters) ;
					$zig_result = $display_flags_value['value'] ;
					$zig_result['id'] = is_array($parameters) ? (array_key_exists("id",$parameters) ? $parameters['id'] : NULL) : NULL ;
					require_once("../zig-api/gui/display.gui.php") ;
					$display_obj = new zig_display ;
					$display_obj->display($zig_result) ;
				}
			}
			else if(array_key_exists("value",$zig_result) and array_key_exists("jscripts",$zig_result))
			{
				if($zig_result['value'] and $zig_result['jscripts'])
				{
					require_once("../zig-api/lib/jscripts.lib.php") ;
					$jscripts_obj = new zig_jscripts ;
					$zig_result['value'].= $jscripts_obj->jscripts("jscripts",$zig_result['value'],$zig_result['jscripts']) ;
				}
			}
			}
		}

		// -- Start set the return flag
		if(is_array($parameters) and isset($parameters['return']))
		{
			$return_config = $parameters['return'] ; 
		}
		else if(isset($zig_result['return']) and $zig_result['return'])
		{
			$return_config = $zig_result['return'] ;
		}
		else
		{
			if(is_object($zig_config_obj))
			{
				$zig_return_flag = $zig_config_obj->config("config","return method") ;
				$return_config = $zig_return_flag['value'] ;
			}
			else
			{
				$return_config = 1 ;
			}
		}
		// -- End set the return flag
		if(isset($zig_result))
		{
			$zig_return = $set_return_obj->set_return("return_config",$zig_result,$return_config) ;
			if(is_array($zig_result))
			{
				if(array_key_exists("error",$zig_result))
				{
					if($zig_result['error'])
					{
						exit() ;
					}
					else
					{
						return $zig_return ;
					}
				}
			}
			else
			{
				return $zig_return ;
			}
		}
		else
		{
			return false ;
		}
	}

?>