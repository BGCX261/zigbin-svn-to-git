<?php

require_once("../zig-api/zigbin.php") ;
$zig_return = zig("wizard","zig_users") ;

$id = $zig_return['id'] ;
$action = $zig_return['action'] ;

if($action=="add" or $action=="copy" and $id)
{
	$sql = "SELECT `username`,`email` FROM `zig_users` WHERE `id`='${id}' AND `send_welcome_email`=1 LIMIT 1" ;
	$result = zig("query",$sql) ;
	$fetch = $result->fetchRow() ;
	if($fetch['email'])
	{
		$domain = $_SERVER['HTTP_HOST'] ;
		$self = explode("/zig-admin/",$_SERVER['PHP_SELF']) ;
		$base_url = $self[0] ;
		$message = zig("template","file","email_welcome") ;
		$message = str_replace("{site_name}",zig("config","title"),$message) ;
		$message = str_replace("{site_link}",$domain.$base_url,$message) ;
		$message = str_replace("{username}",$fetch['username'],$message) ;
		$message = str_replace("{password}",$_POST['password'],$message) ;
		$parameters = array
		(
		 	"function"	=>	"mailer",
		 	"to"		=>	$fetch['email'],
			"subject"	=>	"Welcome to ".zig("config","title"),
			"message"	=>	$message,
		 	"from"		=>	"Administrator <noreply@${domain}${base_url}>",
		) ;
		zig($parameters) ;
	}
}

?>