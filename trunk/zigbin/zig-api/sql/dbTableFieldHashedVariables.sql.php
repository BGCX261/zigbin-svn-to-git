<?php

class zig_dbTableFieldHashedVariables
{
	function dbTableFieldHashedVariables($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL)
	{
		$parentId = 0 ;
		if($arg1)
		{
			$method = $arg1 ;
			$parentId = $arg2 ;
		}
		if(is_array($parameters))
		{
			$method = array_key_exists("method ",$parameters) ? $parameters['method '] : false ;
			$parentId = array_key_exists("parentId ",$parameters) ? $parameters['parentId '] : $parentId ;
		}
		$zig_return['value'] = $method ? $this->$method($parentId) : false ;
		return $zig_return ;
	}

	function getVariablesByParentId($parentId)
	{
		$records = array() ;
		$sql = "SELECT 
					`variable`, 
					`hash` 
				FROM 
					`zig_field_hashed_variables` 
				WHERE 
					`zig_parent_id` = '${parentId}'" ;
		$result = zig("query",$sql) ;
		while($fetch=$result->fetchRow())
		{
			$records[] = $fetch ;
		}
		return $records ;
	}
}

?>