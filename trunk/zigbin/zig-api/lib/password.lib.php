<?php

class zig_password
{
	function password($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$old_password = $arg1 ;
			$new_password = $arg2 ;
			$confirmed_password = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$old_password = array_key_exists("old_password",$parameters) ? $parameters['old_password'] : NULL ;
			$new_password = array_key_exists("new_password",$parameters) ? $parameters['new_password'] : NULL ;
			$confirmed_password = array_key_exists("confirmed_password",$parameters) ? $parameters['confirmed_password'] : NULL ;
			$return_link = array_key_exists("return_link",$parameters) ? $parameters['return_link'] : NULL ;
		}

		$zig_global_database = zig("config","global_database") ;
		$pre = zig("config","pre") ;
		$username = zig("info","user") ;
		$zig_authentication = zig("config","authentication") ;
		
		if($old_password=="" or $new_password=="" or $confirmed_password=="")
		{
			$zig_result['value'] = "Password should not be blank" ;
		}
		else if(!zig("authenticate",$zig_authentication,$username,$old_password))
		{
			$zig_result['value'] = "Incorrect Old Password" ;
		}
		else if(!strcmp($old_password,$new_password))
		{
			$zig_result['value'] = "Old Password and New Password are the same" ;
		}
		else if(strcmp($new_password,$confirmed_password))
		{
			$zig_result['value'] = "New Password and Confirmed Password are not the same" ;
		}
		else if($authentication=="ldap")
		{
			// Start set LDAPs' info
			$zig_ldap_return = zig("config","ldap") ;
			if(!is_array($zig_ldap_return))
			{
				$ldap_host = $zig_ldap_return ;
			}
			else
			{
				foreach($zig_ldap_return as $value)
				{
					$ldap_host = $ldap_host ? $ldap_host.";".$value : $value ;
				}
			}

			$zig_ldap_dn_return = zig("config","ldap_dn") ;
			if(!is_array($zig_ldap_dn_return))
			{
				$ldap_dn = $zig_ldap_dn_return ;
			}
			else
			{
				foreach($zig_ldap_dn_return as $value)
				{
					$ldap_dn = $ldap_dn ? $ldap_dn.";".$value : $value ;
				}
			}
			// End set LDAPs' info

			// Start change "=" comma into ":" and "," comma into "|" -- need to have escape of characters on hash_vars_encode
			$ldap_dn = str_replace("=",":",$ldap_dn) ;
			$ldap_dn = str_replace(",","|",$ldap_dn) ;
			// End change "=" comma into ":" and "," comma into "|" -- need to have escape of characters on hash_vars_encode

			$zig_variables['ldap_host'] = $ldap_host ;
			$zig_variables['ldap_dn'] = $ldap_dn ;
			$zig_variables['return_link'] = $return_link ;
			$zig_variables['old_password'] = $old_password ;
			$zig_variables['new_password'] = $new_password ;
			$zig_variables['confirmed_password'] = $confirmed_password ;
			$zig_variables['username'] = $username ;
			$zig_hash = zig("hash","vars_encode",$zig_variables) ;
//			$zig_link = "http://192.168.2.71/~merlinm/zig_shell.php?zig_hash=".$zig_hash ;
//			$zig_link = "http://proxy1.filmschool.cbu/change_pass/zig_shell.php?zig_hash=".$zig_hash ;
			print "<script>window.location='${zig_link}'</script>" ;
		}
		else
		{
			$sql = "UPDATE `${zig_global_database}`.`${pre}users` SET `password`=PASSWORD('$new_password'),`force_change_password`='0' WHERE `username`='${username}' LIMIT 1" ;
			zig("query",$sql,"password.lib.php",false) ;
			$zig_result['value'] = "Password successfully changed!" ;
		}

		$zig_result['return'] = 1 ;

		return $zig_result ;
	}
}

?>