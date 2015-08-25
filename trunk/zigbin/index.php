<?php

require_once("zig-api/lib/cache.lib.php") ;
$zig_cache_object = new zig_cache ;

$file_exists = $zig_cache_object->cache("cache","file_exists","zig-api/configs/default/filesPath.configs.php") ;
switch($file_exists['value']) {
	case 0:
	case false: {
		print "No files path defined yet in zig-api/configs/default/filesPath.configs.php<br />" ;
		print "See zig-api/config/default/sample-filesPath.configs.php" ;
		exit() ;
	}
	default: {
		require_once("zig-api/configs/default/filesPath.configs.php") ;
	}
}

$file_exists = $zig_cache_object->cache("cache","file_exists","${filesPath}/zig-api/configs/".$_SERVER['HTTP_HOST']."/settings.configs.php") ;
if(!$file_exists['value'])
{
	$file_exists = $zig_cache_object->cache("cache","file_exists","${filesPath}/zig-api/configs/default/settings.configs.php") ;
	if(!$file_exists['value'])
	{
		header("Location: zig-admin/install.php") ;
		exit() ;
	}
}
header("Location: zig-api/") ;

?>