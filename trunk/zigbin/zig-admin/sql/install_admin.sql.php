<?php

class zig_install_admin {
	function install_admin($parameters,$arg1,$arg2,$arg3) {
		if($arg1 or $arg2 or $arg3) {
			$password = $arg1 ;
			$method = $arg2 ? $arg2 : "display" ;
		}
		else if(is_array($parameters)) {
			$password = array_key_exists("password",$parameters) ? $parameters['password'] : NULL ;
			$method = array_key_exists("method",$parameters) ? $parameters['method'] : "display" ;
		}

		if($method=="display") {
			$templateSettings = array(
					"function"	=> "template",
					"module"	=> "zig-admin",
					"method"	=> "file",
					"file"		=> "install_admin"
			) ;
			$buffer = zig($templateSettings) ;
			$buffer = str_replace("{zig_hash}",zig("hash","encrypt","function=install_admin,method=install,module=zig-admin,zigjax=1"),$buffer) ;
		}
		else if($method=="install") {
			// -- Start Install Admin Password
			$result = zig("query","SELECT `id`,`username` FROM `zig_users` WHERE `username`='admin' LIMIT 1") ;
			if($result->RecordCount()) {
				$fetch = $result->fetchRow() ;
				zig("query","UPDATE `zig_users` SET `password`=PASSWORD('${password}') WHERE `id`='$fetch[id]' LIMIT 1") ;
			}
			else {
				zig("query","INSERT INTO `zig_users` (`zig_created`,`zig_user`,`username`,`password`,`force_change_password`) VALUES(NOW(),'install_admin.sql.php','admin',PASSWORD('${password}'),'0')") ;
			}
			// -- End Install Admin Password

			// -- Start Install Admin Permission
			$result = zig("query","SELECT `id`,`users` FROM `zig_permissions` WHERE `users`='admin' LIMIT 1") ;
			if($result->RecordCount()) {
				$fetch = $result->fetchRow() ;
				zig("query","UPDATE `zig_permissions` SET `module`='all',`tab`='all',`action`='all',`fieldset`='all,`field_name`='all',`field_value`='all',`permissions`='allow' WHERE `id`='$fetch[id]' LIMIT 1") ;
			}
			else {
				zig("query","INSERT INTO `zig_permissions` (`zig_created`,`zig_user`,`users`) VALUES(NOW(),'install_admin.sql.php','admin')") ;
			}
			// -- End Install Admin Permission
			
			$buffer = "New admin password successfully installed!" ;
		}

		$zig_result['value'] = $buffer ;
		$zig_result['return'] = 1 ;
		return $zig_result ;
	}
}

?>