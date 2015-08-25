<?php

require_once("../zig-api/zigbin.php") ;
$server_mode = zig("config","server mode") ;
$buffer = zig("template","block","updates","get") ;
switch(zig("config","server_mode"))
{
	case "development":
	{
		$buffer = str_replace("{push}",zig("template","block","updates","push"),$buffer) ;
		$buffer = str_replace("{pushHash}",zig("hash","encrypt","function=update_database,mode=push,module=zig-admin,zigjax=1"),$buffer) ;
		break ;
	}
	default:
	{
		$buffer = str_replace("{push}","",$buffer) ;
	}
}

$applicationsTemplate = zig("template","block","updates","application") ;
$applicationsBuffer = "" ;
$applicationNames = zig("dbTableApplications","getApplicationNames") ;
foreach($applicationNames as $applicationName)
{
	$applicationsBuffer.= str_replace("{applicationName}",$applicationName,$applicationsTemplate) ;
}

$applicationsBuffer = str_replace("{applications}",$applicationsBuffer,zig("fieldset","{applications}","Applications",false)) ;
$buffer = str_replace("{applications}",$applicationsBuffer,$buffer) ;
$buffer = str_replace("{zig_hash}",zig("hash","encrypt","function=updateApplications,module=zig-admin,zigjax=1"),$buffer) ;
print $buffer ;

?>