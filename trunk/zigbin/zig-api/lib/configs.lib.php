<?php

require_once("../zig-api/configs/default/filesPath.configs.php") ;
require_once("../zig-api/lib/cache.lib.php") ;
$zig_cache_object = new zig_cache ;

$httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "default" ;
$default_action = NULL ;
$theme = NULL ;
$template = NULL ;
$file_exists = $zig_cache_object->cache("cache","file_exists","../zig-api/lib/db.lib.php") ;
if($file_exists['value'])
{
	$file_exists = $zig_cache_object->cache("cache","file_exists","../zig-api/lib/config.lib.php") ;
}
if($file_exists['value'])
{
	$file_exists = $zig_cache_object->cache("cache","file_exists","${filesPath}/zig-api/configs/default/settings.configs.php") ;
	if(!$file_exists['value'])
	{
		$file_exists = $zig_cache_object->cache("cache","file_exists","../zig-api/configs/${httpHost}/settings.configs.php") ;
	}
	if($file_exists['value'])
	{
		require_once("../zig-api/lib/config.lib.php") ;
		$zig_config_obj = new zig_config ;
		$config_value = $zig_config_obj->config("config","zig_action") ;
		$default_action = $config_value['value'] ;
		$config_theme = $zig_config_obj->config("config","theme") ;
		$config_template = $zig_config_obj->config("config","template") ;
		$config_timezone = $zig_config_obj->config("config","timezone") ;
		$theme = $config_theme['value'] ;
		$template = $config_template['value'] ;
		switch($config_timezone['value']<>"")
		{
			case true:
			{
				date_default_timezone_set($config_timezone['value']) ;
			}
		}
	}
}

require_once("../zig-api/lib/hash.lib.php") ;
$zig_hash_obj = new zig_hash ;

//-- zig's directory structure
$GLOBALS['zig']['dir'] = array
(
	'gui',
	'lib',
	'sql'
) ;
//-- zig's default settings
$GLOBALS['zig']['default']['template'] = $template ? $template : "default" ;	// template
$GLOBALS['zig']['default']['theme'] = $theme ? $theme : "default" ;				// theme
$GLOBALS['zig']['default']['value'] = 1 ;										// 0 = no return value, 2 = return 'return value'
$GLOBALS['zig']['default']['cookie'] = time()+1800 ;							// default cookie lifetime

//-- zig's current settings
$ripped_url = explode("/",$_SERVER['PHP_SELF']) ;
$url_size = sizeof($ripped_url) ;
$zig_hash = isset($_GET['zig_hash']) ? $_GET['zig_hash'] : (isset($_POST['zig_hash']) ? $_POST['zig_hash'] : NULL) ;
$zig_hash_result = $zig_hash ? $zig_hash_obj->hash_vars_decode($zig_hash) : $default_action ;

if(is_array($zig_hash_result))
{
	$action = array_key_exists("action",$zig_hash_result) ? $zig_hash_result['action'] : NULL ;
	$id = array_key_exists("id",$zig_hash_result) ? $zig_hash_result['id'] : NULL ;
}
else
{
	$action = $default_action ;
	$id = NULL ;
}	
$zig_action = isset($_GET['zig_action']) ? $_GET['zig_action'] : (isset($_POST['zig_action']) ? $_POST['zig_action'] : NULL) ;
$action = $zig_action ? $zig_hash_obj->hash_decrypt($zig_action) : $action ;
$GLOBALS['zig']['current']['id'] = $id ;
$GLOBALS['zig']['current']['action'] = $action ;
$GLOBALS['zig']['current']['script'] = $ripped_url[$url_size-1] ;
$GLOBALS['zig']['current']['module'] = isset($ripped_url[$url_size-2]) ? $ripped_url[$url_size-2] : NULL ;
$GLOBALS['zig']['current']['directory'] = $GLOBALS['zig']['current']['module'] ;
$GLOBALS['zig']['current']['template'] = isset($GLOBALS['zig']['current']['template']) ? $GLOBALS['zig']['current']['template'] : $GLOBALS['zig']['default']['template'] ;
$GLOBALS['zig']['current']['theme'] = isset($GLOBALS['zig']['current']['theme']) ? $GLOBALS['zig']['current']['theme'] : $GLOBALS['zig']['default']['theme'] ;

//-- Start Zig's Paths
$GLOBALS['zig']['path'] = array
(
	'root'		=>	'zigbin',														// root folder
	'api'		=>	'zig-api',														// API folder
	'jscripts'	=>	'jscripts',														// javascripts folder
	'template'	=>	'gui/templates',												// template folder
	'theme'		=>	'gui/themes',													// themes folder
	'image'		=>	'gui/themes/'.$GLOBALS['zig']['current']['theme'].'/img',		// image folder
	'css'		=>	'gui/themes/'.$GLOBALS['zig']['current']['theme'].'/css',		// image folder
	'icon'		=>	'48x48/actions/'												// icon path
) ;
//-- End Zig's Paths

?>