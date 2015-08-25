<?php

class zig_update_field
{
	function update_field($parameters,$arg1='',$arg2='',$arg3='')
	{
		$mode = "add" ;
		$parent_value = "" ;
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
			$field = $arg2 ;
			$parent_value = $arg3 ;
		}
		if(is_array($parameters))
		{
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : $table ;
			$field = array_key_exists("field",$parameters) ? $parameters['field'] : $field  ;
			$parent_value = array_key_exists("parent_value",$parameters) ? $parameters['parent_value'] : (array_key_exists("arg3",$parameters) ? $parameters['arg3'] : $parent_value) ;
			$uniqueString = array_key_exists("uniqueString",$parameters) ? $parameters['uniqueString'] : ""  ;
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : "zig-api"  ;
			$mode = array_key_exists("mode",$parameters) ? $parameters['mode'] : $mode ;
		}

		switch(array_key_exists("parent_value", $parameters)) {
			case false: {
				$parameters['parent_value'] = $parent_value ;
			}
		}
		$table_sql = "SHOW COLUMNS FROM `${table}` WHERE `Field`='${field}'" ;
		$table_result = zig("query",$table_sql) ;
		$dbDefinedField = $table_result->fetchRow() ;

		$customFieldParameters = $parameters ;
		$customFieldParameters['function'] = "customField" ;
		$customFieldParameters['method'] = $field ;
		$field_info = zig($customFieldParameters) ;

		$buffer = zig(array(
			"function"				=>	"field_element",
			"mode"					=>	$mode,
			"table"					=>	$table,
			"dbDefinedField"		=>	$dbDefinedField,
			"userDefinedField"		=>	$field_info,
			"parent_value"			=>	$parent_value
		)) ;

		$buffer = str_replace("{tableName}",$table,$buffer) ;
		$buffer = str_replace("{current_field_name}",$field,$buffer) ;
		$buffer = str_replace("{fieldValue}",zig("checkArray",$field_info,"defaultValue"),$buffer) ;
		$buffer = str_replace("{uniqueString}",$uniqueString,$buffer) ;

		$zig_result['value'] = $buffer ;
		$zig_result['return'] = 1 ;

		return $zig_result ;
	}
}

?>