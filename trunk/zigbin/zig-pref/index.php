<?php
	require_once("../zig-api/zigbin.php") ;

	$zig_hash = isset($_GET['zig_hash']) ? $_GET['zig_hash'] : (isset($_POST['zig_hash']) ? $_POST['zig_hash'] : NULL) ;
	$zig_hash_vars = zig("hash","vars_decode",$zig_hash) ;
	$template = "password" ;
	$message = "" ;
	if(is_array($zig_hash_vars))
	{
		$template = array_key_exists("template",$zig_hash_vars) ? $zig_hash_vars['template'] : $template ;
	}

	if(zig("checkArray",$_POST,"change_password"))
	{
		$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ;
		$ripped_url = explode("/zig-pref/",$url) ;
		$url = $ripped_url[0]."/zig-pref/index.php" ;
		$_POST['old_password'] = $_POST['old_password'] ? $_POST['old_password'] : ($template=="reset" ? zig("hash","encrypt","reset") : NULL) ;
		$parameters = array
		(
			"function"				=>	"password",
			"old_password"			=>	$_POST['old_password'],
			"new_password"			=>	$_POST['new_password'],
			"confirmed_password"	=>	$_POST['confirmed_password'],
			"return_link"			=>	$url,
		) ;
		$message = zig($parameters) ;
	}

	$content = zig("template","block","password",$template) ;
	zig("content",$content,$message) ;

?>