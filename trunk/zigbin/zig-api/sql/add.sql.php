<?php

class zig_add
{
	function add($parameters,$arg1='',$arg2='',$arg3='')
	{
		$uniqueString = uniqid() ;
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
			$exclude = $arg2 ;
			$permissions = $arg3 ;
		}
		if(is_array($parameters))
		{
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$exclude = array_key_exists("exclude",$parameters) ? $parameters['exclude'] : zig("config","exclude") ;
			$parentId = array_key_exists("parentId",$parameters) ? $parameters['parentId'] : NULL ;
			$parentTable = array_key_exists("parentTable",$parameters) ? $parameters['parentTable'] : NULL ;
			$permissions = array_key_exists("permissions",$parameters) ? $parameters['permissions'] : NULL ;
			$uniqueString = array_key_exists("uniqueString",$parameters) ? $parameters['uniqueString'] : $uniqueString ;
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : NULL ;
			$sql = array_key_exists("sql",$parameters) ? $parameters['sql'] : NULL ;
			$triggers = array_key_exists("triggers",$parameters) ? $parameters['triggers'] : "show" ;
		}

		$fieldsParameters = array (
			'function'			=>	'fields',
			'method'			=>	'parent',
			'table'				=>	$table,
			'mode'				=>	'add',
			'exclude'			=>	$exclude,
			'module'			=>	$module,
			'permissions'		=>	$permissions,
			'return_fields'		=>	true
		) ;
		$fields_return_values = zig($fieldsParameters) ;
		$buffer = $fields_return_values['html'] ;

		// -- Start process child table
		$childTables = zig("dbTableRelationships","getTableRelationships",$table) ;
		foreach($childTables as $childTable)
		{
			$wizard_parameters = array
			(
				'function'			=>	'fields',
				'method'			=>	'child',
				'table'				=>	$childTable,
				'parent_table'		=>	$table,
				'exclude'			=>	$exclude,
				'mode'				=>	'add',
				'module'			=>	$module,
				'permissions'		=>	$permissions,
				'return_fields'		=>	true
			) ;
			$childHtml = zig($wizard_parameters) ;
			$buffer.= $childHtml['html'] ;
		}
		// -- End process child table

		$template = zig("template","file","add") ;
		$triggerBuffer = zig(array(
					"function"	=> "trigger",
					"action"	=> "add"
					)) ;
		switch($triggers)
		{
			case "hide":
			{
				$triggerBuffer = str_replace("{class}","zig_invisible",$triggerBuffer) ;
				break ;
			}
			default:
			{
				$triggerBuffer = str_replace("{class}","",$triggerBuffer) ;
			}
		}
		$buffer = str_replace("{add}",$buffer,$template) ;
		$buffer = str_replace("{trigger}",$triggerBuffer,$buffer) ;
		$sql = zig("hash","stringEncode",$sql) ;
		$saveHash = zig("hash","encrypt","function=addRecord,module=${module},table=${table},parentTable=${parentTable},
						parentId=${parentId},sql=${sql},uniqueString=${uniqueString},zigjax=1") ;
		$buffer = str_replace("{saveHash}",$saveHash,$buffer) ;
		$buffer = str_replace("{uniqueString}",$uniqueString,$buffer) ;
		$zig_result['value'] = $buffer ;

		return $zig_result ;
	}
}

?>