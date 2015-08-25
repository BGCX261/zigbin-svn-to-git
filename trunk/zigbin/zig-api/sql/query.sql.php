<?php

class zig_query
{
	function query($parameters,$arg1,$arg2,$arg3)
	{
		$connection = array_key_exists("adodb",$GLOBALS['zig']) ? $GLOBALS['zig']['adodb'] : NULL ;
		if($arg1 or $arg2 or $arg3)
		{
			$sql = $arg1 ;
			$log = isset($arg2) ? $arg2 : true ;
			$move = isset($arg3) ? $arg3 : true ;
		}
		if(is_array($parameters))
		{
			$sql = array_key_exists("sql",$parameters) ? $parameters['sql'] : NULL ;
			$log = array_key_exists("log",$parameters) ? $parameters['log'] : true ;
			$move = array_key_exists("move",$parameters) ? $parameters['move'] : true ;
			$connection = array_key_exists("connection",$parameters) ? ($parameters['connection'] ? $parameters['connection'] : $connection) : $connection ;
		}
		$zig_debug = false ;
		if(!is_object($connection))
		{
			print "SQL Error: Not Connected to Database" ;
			$debug_backtrace = debug_backtrace() ;
			$script_file = $debug_backtrace[1]['file'] ;
			$script_line = $debug_backtrace[1]['line'] ;
			print "<br />Script: Line ${script_line} @ $script_file" ;
			print "<br />sql=".$sql ;
			exit() ;
		}
		else if($sql)
		{
			$result = $connection->Execute($sql) ;
			$error_number = $connection->ErrorNo() ;
		}
		else
		{
			$error_number = "blank" ;
			$sql = "[blank]" ;
		}
		if($error_number)
		{
			$debug_backtrace = debug_backtrace() ;
			$script_file = $debug_backtrace[1]['file'] ;
			$script_line = $debug_backtrace[1]['line'] ;
			$zig_result['error'] = "Script: Line ${script_line} @ $script_file<br />" ;
			$zig_result['error'].= "SQL Statement: $sql<br />" ;
			$zig_result['error'].= $sql=="[blank]" ? "" : "SQL Error: ".$connection->ErrorMsg() ;
			print $zig_result['error'] ;
/*			print "<br />backtrace=" ;
			print "<pre>" ;
			print_r($debug_backtrace) ;
			print "</pre>" ;*/
			exit() ;
		}
		else
		{
			if($move)
			{
				$result->MoveFirst() ;
			}
			$zig_result['value'] = $result ;
		}

		$zig_result['value'] = substr($sql,0,11)=="INSERT INTO" ? $connection->Insert_ID() : $zig_result['value'] ;
		if(($error_number<>"blank" or ($error_number*1>0)) and $log)
		{
			require_once("../zig-api/sql/logit.sql.php") ;
			$logit_object = new zig_logit ;
			$sql = $error_number ? $sql." SQL Error #: ".$error_number : $sql ;
			$logit_object->logit("logit","sql","query.sql.php","${sql}") ;
		}

		if($zig_debug)
		{
			$debug_backtrace = debug_backtrace() ;
			$script_file = $debug_backtrace[1]['file'] ;
			$script_line = $debug_backtrace[1]['line'] ;
			print "<br /><br />Script: Line ${script_line} @ $script_file" ;
			print "<br />sql=".$sql ;
/*			print "<br />backtrace=" ;
			print "<pre>" ;
			print_r($debug_backtrace) ;
			print "</pre>" ;*/
		}

		$zig_result['return'] = 1 ;
		return $zig_result ;
	}
}

?>