<?php

class zig_select
{
	function select($parameters,$arg1,$arg2,$arg3)
	{
		$fields = "*" ;
		$table = "" ;
		$where = "" ;
		$limit = "" ;
		if($arg1 or $arg2 or $arg3)
		{
			$fields = $arg1 ? $arg1 : $fields ;
			$table = $arg2 ;
			$where = $arg3 ? "WHERE ".$arg3 : $where ;
		}
		if(is_array($parameters))
		{
			$fields = array_key_exists("field",$parameters) ? $parameters['field'] : $fields ;
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : $table ;
			$where = array_key_exists("where",$parameters) ? "WHERE ".$parameters['where'] : $where ;
			$limit = array_key_exists("limit",$parameters) ? "LIMIT ".$parameters['limit'] : $limit ;
		}

		$sql = "SELECT ${fields} FROM ${table} ${where} ${limit}" ;
		$zig_return['return'] = 1 ;
		$zig_return['value'] = zig("query",$sql) ;

		return $zig_return ;
	}
}

?>