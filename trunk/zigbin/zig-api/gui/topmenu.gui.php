<?php

class zig_topmenu
{
	function topmenu($parameters,$arg1='',$arg2='',$arg3='')
	{
		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$username = zig("info","user") ;
		$username = $username ? $username : "Guest" ;
		$buffer = zig("template","block","topmenu","header") ;

		// -- Start Modules
		$sql = "SELECT `directory`,`name`,`permission` FROM `${zig_global_database}`.`${pre}applications` WHERE (`permission`='Top Left' OR `permission`='Top Right')  AND `zig_status`<>'deleted' ORDER BY `zig_weight`,`name`" ;
		$result = zig("query",$sql) ;
		$topLeft = $topRight = $flag_buffer = "" ;
		while($fetch=$result->fetchRow())
		{
			$permission = zig("permissions",$fetch['directory']) ;
			if($permission)
			{
				$module_buffer = zig("template","block","topmenu","modules") ;
				$module_buffer = $flag_buffer ? str_replace("{module_separator}","|",$module_buffer) : str_replace("{module_separator}","",$module_buffer) ;
				$module_buffer = $flag_buffer = str_replace("{module_link}","../".$fetch['directory'],$module_buffer) ;
				$module_buffer = str_replace("{module_name}",$fetch['name'],$module_buffer) ;
				$module_name_class = $GLOBALS['zig']['current']['module']==$fetch['directory'] ? "active" : "inactive" ;
				$module_buffer = str_replace("{class}",$module_name_class,$module_buffer) ;
				switch($fetch['permission'])
				{
					case "Top Right":
					{
						$topRight.= $module_buffer ;
						break ;
					}
					default:
					{
						$topLeft.= $module_buffer ;						
					}
				}

			}
		}
		//$buffer.= $module_buffer ? $module_buffer : "" ;
		// -- End Modules

		$buffer.= zig("template","block","topmenu","footer") ;
		$zig_hash = zig("hash","encrypt","gate,logout") ;
		if($username=="Guest")
		{
			$buffer = str_replace("{logout_link}","../zig-api/",$buffer) ;
			$buffer = str_replace("{logout_text}","Login",$buffer) ;
		}
		else
		{
			$buffer = str_replace("{logout_link}","../zig-api/decoder.php?zig_hash=".$zig_hash,$buffer) ;
			$buffer = str_replace("{logout_text}","Logout",$buffer) ;
		}
		$buffer = str_replace("{topLeft}",$topLeft,$buffer) ;
		$buffer = str_replace("{topRight}",$topRight,$buffer) ;
		$buffer = str_replace("{username}",$username,$buffer) ;
		$zig_result['value'] = $buffer ;
		$zig_result['return'] = 1 ;
		
		return $zig_result ;
	}
}

?>