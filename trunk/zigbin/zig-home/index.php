<?php

	require_once("../zig-api/zigbin.php") ;
	$title = zig("config","title") ;
	$zig_hash = isset($_GET['zig_hash']) ? $_GET['zig_hash'] : (isset($_POST['zig_hash']) ? $_POST['zig_hash'] : NULL) ;
	$zig_hash = zig("hash","vars_decode",$zig_hash) ;
	$message = is_array($zig_hash) ? (array_key_exists("message",$zig_hash) ? $zig_hash['message'] : NULL) : NULL ;
	zig("content","<br /><div align='center'><font size=+3>${title}</font></div>",$message) ;

?>