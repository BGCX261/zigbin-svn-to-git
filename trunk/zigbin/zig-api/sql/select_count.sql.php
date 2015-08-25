<?php

class zig_select_count
{
	function select_count($parameters,$arg1,$arg2,$arg3)
	{
		$where = "" ;
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
			$field = $arg2 ? $arg2 : "*" ;
			$distinct = $arg3 ;
		}
		if(is_array($parameters))
		{
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$field = array_key_exists("field",$parameters) ? $parameters['field'] : "*" ;
			$distinct = array_key_exists("distinct",$parameters) ? $parameters['distinct'] : false ;
			$where = array_key_exists("where",$parameters) ? "WHERE ".$parameters['where'] : $where ;
		}
		switch($distinct)
		{
			case true:
			{
				$sql = "SELECT COUNT(${field}) AS `count` FROM ${table} ${where} GROUP BY (${field})" ;
			}
			default:
			{
				$sql = "SELECT COUNT(${field}) AS `count` FROM ${table} ${where}" ;
			}
		}

		$zig_return['return'] = 1 ;
		$result = zig("query",$sql) ;
		$fetch = $result->fetchRow() ;
		$zig_return['value'] = $fetch['count'] ;

		return $zig_return ;
	}
}

?>