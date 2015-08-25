<?php

require_once("../zig-api/configs/default/filesPath.configs.php") ;
require_once("../zig-api/lib/cache.lib.php") ;
$zig_cache_object = new zig_cache ;

$file_exists = $zig_cache_object->cache("cache","file_exists","${filesPath}/zig-api/configs/".$_SERVER['HTTP_HOST']."/settings.configs.php") ;
if(!$file_exists['value'])
{
	$file_exists = $zig_cache_object->cache("cache","file_exists","${filesPath}/zig-api/configs/default/settings.configs.php") ;
	require_once("../zig-api/zigbin.php") ;
	if(!$file_exists['value'])
	{
		$buffer = zig("template","block","install","install") ;
		$buffer_applications = zig("template","file","applications") ;
		$buffer_applications = str_replace("{zig_title}","Zigbin",$buffer_applications) ;
		$buffer_applications = str_replace("{zig_icon}",zig("images","48x48/apps/zigbin.png"),$buffer_applications) ;
		$buffer_applications = str_replace("{name}","<td>Installation</td>",$buffer_applications) ;
		$buffer_applications = str_replace("{icon}","<td><img src='".zig("images","32x32/apps/package_administration.png")."' alt='Installation' /></td>",$buffer_applications) ;
		$buffer_header = zig("template","file","header") ;
		$buffer_header = str_replace("{header}","Installation",$buffer_header) ;
		$buffer_header = str_replace("{zig_module_icon}",zig("images","32x32/apps/package_administration.png"),$buffer_header) ;
		$buffer_applications = str_replace("{header}",$buffer_header,$buffer_applications) ;
		$buffer = str_replace("{zig_applications}",$buffer_applications,$buffer) ;

		$fileExists =$zig_cache_object->cache("cache","file_exists",$filesPath) ;
		switch($fileExists['value'])
		{
			case false:
			{
				$zig_cache_object->cache("cache","mkdir",$filesPath) ;
			}
		}

		if(is_writable($filesPath))
		{
			$connected = false ;
			switch($connected)
			{
				case true:
				{
					$buffer = str_replace("{zig_message}",zig("template","block","install","installing"),$buffer) ;
					break ;
				}
				default:
				{
					$buffer_zig_install_content = zig("template","block","install","database") ;
					$buffer = str_replace("{zig_install_content}",$buffer_zig_install_content,$buffer) ;
					$buffer_zig_advance_options = zig("fieldset",zig("template","block","install","advance"),"Advance Options",true) ;
					$buffer = str_replace("{zig_advance_options}",$buffer_zig_advance_options,$buffer) ;
					$buffer = str_replace("{zig_message}","",$buffer) ;
				}
			}
			$buffer = str_replace("{zig_hash}",zig("hash","encrypt","function=install,module=zig-admin,zigjax=1"),$buffer) ;
			$buffer = str_replace("{zig_hash_install_admin}",zig("hash","encrypt","function=install_admin,method=display,module=zig-admin,zigjax=1"),$buffer) ;
		}
		else
		{
			$zig_message = zig("template","block","install","error") ;
			$zig_message = str_replace("{webUser}",getenv("APACHE_RUN_USER"),$zig_message) ;
			$zig_message = str_replace("{webGroup}",getenv("APACHE_RUN_GROUP"),$zig_message) ;
			$zig_message = str_replace("{filesPath}",$filesPath,$zig_message) ;
			$buffer = str_replace("{zig_message}",$zig_message,$buffer) ;
			$buffer = str_replace("{zig_install_content}","",$buffer) ;
		}

		$buffer = str_replace("{zig_footer}",zig("footer"),$buffer) ;
		$jscript_parameters = array
		(
			"function"		=>	"jscripts",
			"jscripts"		=>	array("common","fieldset","install","listener"),
			"server_mode"	=>	"production"
		) ;
		$buffer = str_replace("{zig_jscripts}",zig($jscript_parameters),$buffer) ;
		print $buffer ;
	}
}
else
{
	header("Location: ../") ;
}

?>