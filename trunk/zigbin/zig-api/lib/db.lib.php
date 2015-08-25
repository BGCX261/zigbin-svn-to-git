<?php

require_once("../zig-api/configs/default/filesPath.configs.php") ;
require_once("../zig-api/lib/cache.lib.php") ;
$zig_cache_object = new zig_cache ;

$httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "default" ;
$file_exists = file_exists("${filesPath}/zig-api/configs/${httpHost}/settings.configs.php") ;
if($file_exists['value'])
{
	require_once("${filesPath}/zig-api/configs/${httpHost}/settings.configs.php") ;
}
else
{
	require_once("${filesPath}/zig-api/configs/default/settings.configs.php") ;
}
require_once("../zig-api/plugins/adodb/adodb.inc.php") ;

$db_host = isset($db_host) ? $db_host : "localhost" ;
$db_pre = isset($db_pre) ? $db_pre : "zig_" ;
$db_name = isset($db_name) ? $db_name : "zigbin" ;
$db_username = isset($db_username) ? $db_username : "root" ;
$db_password = isset($db_password) ? $db_password : NULL ;

//-- Start zig's sql settings
$GLOBALS['zig']['sql'] = array
(
	'server'			=>	$db_host,
	'username'			=>	$db_username,
	'password'			=>	$db_password,
	'database'			=>	$db_name,
	'global_database'	=>	$db_name,
	'local_database'	=>	$db_name,
	'pre'				=>	$db_pre
) ;
//-- Start zig's sql settings

$GLOBALS['zig']['adodb'] = NewADOConnection('mysql') ;
$GLOBALS['zig']['adodb']->Connect($GLOBALS['zig']['sql']['server'], $GLOBALS['zig']['sql']['username'], $GLOBALS['zig']['sql']['password'], $GLOBALS['zig']['sql']['database']) ;

?>