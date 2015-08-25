<?php

class zig_isPasswordField
{
	function isPasswordField($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$tableName = $arg1 ;
			$fieldName = $arg2 ;
		}
		if(is_array($parameters))
		{
			$tableName = array_key_exists("tableName",$parameters) ? $parameters['tableName'] : "" ;
			$fieldName = array_key_exists("fieldName",$parameters) ? $parameters['fieldName'] : "" ;
		}
		$pre = zig("config","pre") ;
		$globalDatabase = zig("config","global_database") ;
		$sql = "SELECT `id` FROM `${globalDatabase}`.`${pre}fields` WHERE `table_name`='${tableName}' AND `field`='${fieldName}' AND `field_type`='password' LIMIT 1" ;
		$result = zig("query",$sql) ;

		$zig_return['return'] = 1 ;
		$zig_return['value'] = $result->RecordCount() ? true : false ;

		return $zig_return ;
	}
}

?>