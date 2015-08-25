<?php

class zig_dbTableRelationships
{
	function dbTableRelationships($parameters,$arg1='',$arg2='',$arg3='')
	{
		$value = "" ;
		if($arg1 or $arg2)
		{
			$method = $arg1 ;
			$value = $arg2 ;
		}
		if(is_array($parameters))
		{
			$method = array_key_exists("method ",$parameters) ? $parameters['method '] : false ;
			$value = array_key_exists("method ",$parameters) ? $parameters['method '] : $value ;
		}
		$zig_return['value'] = $method ? $this->$method($value) : false ;
		return $zig_return ;
	}

	function getTableRelationships($table)
	{
		$tables = array() ;
		$sql = "SELECT 
					`child_table` 
				FROM 
					`zig_relationships` 
				WHERE 
					(parent_table='${table}' OR parent_table='all tables') AND 
					`child_table`<>'' AND `child_table`<>'${table}' 
				ORDER BY 
					`zig_weight`, 
					`fieldset`, 
					`child_table`" ;
		$result = zig("query",$sql) ;
		while($fetch=$result->fetchRow())
		{
			$tables[] = $fetch['child_table'] ;
		}
		return $tables ;
	}
}

?>