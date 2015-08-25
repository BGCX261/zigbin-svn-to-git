<?php

class zig_logit
{
	function logit($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$type = $arg1 ;
			$script = $arg2 ;
			$log_message = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$type = array_key_exists("type",$parameters) ? $parameters['type'] : NULL ;
			$script = array_key_exists("script",$parameters) ? $parameters['script'] : NULL ;
			$log_message = array_key_exists("log_message",$parameters) ? $parameters['log_message'] : NULL ;
		}
		$log_message = strtolower($log_message) ;
		$sql_query_type = NULL ;
		$record_action = NULL ;
		require_once("../zig-api/lib/info.lib.php") ;
		$info_object = new zig_info ;
		$user = $info_object->user() ;
		$pre = $GLOBALS['zig']['sql']['pre'] ;
		$zig_global_database = $GLOBALS['zig']['sql']['global_database'] ;
		$script = $GLOBALS['zig']['current']['script'] ;
		$module = $GLOBALS['zig']['current']['module'] ;
		$action = $GLOBALS['zig']['current']['action'] ;
		$sql = "SELECT `a`.`module`,`a`.`name`
				FROM `zig_tabs` `a`,`zig_applications` `b` 
				WHERE `a`.`module`=`b`.`name` AND `directory`='$module' AND `a`.`link`='$script' 
				LIMIT 1" ;
		$result = $GLOBALS['zig']['adodb']->Execute($sql) ;
		$fetch = $result->fetchRow() ;
		$module = $fetch['module'] ;
		$tab = $fetch['name'] ;
		
		// -- Start Record Action
		if(stripos($log_message,"select ")==0 and stripos($log_message,"select ")!==false)
		{
			$sql_query_type = "select" ;
			$record_action = "search" ;
		}
		else if(stripos($log_message,"update ")==0 and stripos($log_message,"update ")!==false)
		{
			$sql_query_type = "update" ;
			if(stripos(strtolower(str_replace("`","",$log_message)),"set zig_status='deleted'"))
			{
				$record_action = "delete" ;
				$splitted_log = explode(" where ",str_replace("`","",$log_message)) ;
				$splitted_where = explode("id",$splitted_log[1]) ;
			}
			else
			{
				$record_action = "edit" ;
			}
		}
		else if(stripos($log_message,"insert into ")==0 and stripos($log_message,"insert into ")!==false)
		{
			$sql_query_type = "insert" ;
			$record_action = "add" ;
		}
		else if(stripos($log_message,"show ")==0 and stripos($log_message,"show ")!==false)
		{
			$sql_query_type = "show" ;
		}
		// -- End Record Action
		
		$sql = "INSERT INTO `${zig_global_database}`.`${pre}logs` (zig_created,zig_user,log_type,module,tab,action,script,record_action,sql_query_type,log_message) VALUES(NOW(),'$user','$type','$module','$tab','$action','$script','$record_action','$sql_query_type',\"$log_message\") " ;
		$result = $GLOBALS['zig']['adodb']->Execute($sql) ;
		$error_number = $GLOBALS['zig']['adodb']->ErrorNo() ;
		
		if($error_number)
		{
			$zig_result['error'] = "Script: $script<br />" ;
			$zig_result['error'].= "SQL Statement: $sql<br />" ;
			$zig_result['error'].= "SQL Error: ".$GLOBALS['zig']['adodb']->ErrorMsg() ;
		}		

		$zig_result['return'] = 1 ;	
		return $zig_result ;		
	}
	
}

?>