<?php

class zig_security
{
	function security($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($_SESSION['zig_hash']=="")
		{
			$zig_hash = isset($_GET['zig_hash']) ? $_GET['zig_hash'] : (isset($_POST['zig_hash']) ? $_POST['zig_hash'] : '') ;
			$zig_hash = $zig_hash ? "?zig_hash=".$zig_hash : '' ;
			$current_url = $_SERVER['PHP_SELF'].$zig_hash ;
			$zig_hash = zig("hash","encrypt",$current_url) ;
			header("Location: ../zig-api/index.php?zig_hash=$zig_hash") ;
			exit() ;
		}
		else if(!session_id())
		{
			session_start() ;
		}
			
		$zig_hash_vars = zig("hash","vars_decode",$_SESSION['zig_hash']) ;
		if(session_id()<>$zig_hash_vars['session_id'])
		{
			header("Location: ../zig-api/index.php") ;
			exit() ;
		}
			
		$cookie = $GLOBALS['zig']['default']['cookie'] ;
		$host = $_SERVER['HTTP_HOST'] ;
		$url = $zig_hash_vars['url'] ;
		$zig_hash = $_SESSION['zig_hash'] ;

		// Start renew cookie expiration
		if(!headers_sent())
		{
			//setcookie("zig_hash",$zig_hash,$cookie,$url,$host) ;
			$_SESSION['zig_hash'] = $zig_hash ;
		}
		// Start extend cookie expiration

		// -- Start check if user needs to change password
		if(strpos($_SERVER['PHP_SELF'],"/zig-pref/")===false)
		{
			$username = zig("info","user") ;
			$zig_global_database = zig("config","global_database") ;
			$pre = zig("config","pre") ;
			$sql = "SELECT `force_change_password` FROM `${zig_global_database}`.`${pre}users` WHERE `force_change_password`=1 AND `username`='${username}' LIMIT 1" ;
			$result = zig("query",$sql) ;
			$record = $result->RecordCount() ;
			switch($result->RecordCount())
			{
				case "0":
				{
					break ;
				}
				default:
				{
					header("Location: ../zig-pref/") ;
					break ;
				}
			}
		}
		// -- End check if user needs to change password

		$zig_result['value'] = 1 ;
		$zig_result['return'] = 1 ;
		
		return $zig_result ;
	}
}

?>