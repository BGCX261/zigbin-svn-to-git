<?php

class zig_view {
	function view($parameters,$arg1='',$arg2='',$arg3='') {
		if($arg1 or $arg2 or $arg3) {
			$table = $arg1 ;
			$id = $arg2 ;
		}
		if(is_array($parameters)) {
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : NULL ;
			$exclude = array_key_exists("exclude",$parameters) ? $parameters['exclude'] : zig("config","exclude") ;
			$permissions = array_key_exists("permissions",$parameters) ? $parameters['permissions'] : NULL ;
			$uniqueString = array_key_exists("uniqueString",$parameters) ? $parameters['uniqueString'] : NULL ;
			$passedSql = array_key_exists("passedSql",$parameters) ? zig("hash","stringEncode",$parameters['passedSql']) : NULL ;
		}

		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;

		// Start remove the database name on the table
		$semi_stripped_table = str_replace($zig_global_database.".","",$table) ;
		// End remove the database name on the table

		// Start stripped table name
		$stripped_table = str_replace($pre,"",$semi_stripped_table) ;
		// End stripped table name

		$sql = "SELECT * 
				FROM 
					`zig_relationships` 
				WHERE 
					(parent_table='$table' OR 
						parent_table='${semi_stripped_table}' OR 
						parent_table='${stripped_table}' OR 
						parent_table='all tables') AND 
					`child_table`<>'' AND 
					`child_table`<>'${stripped_table}' 
				ORDER BY 
					`zig_weight`,
					`fieldset`,
					`child_table`" ;
		$field_result = zig("query",$sql) ;

		$wizard_parameters = array(
			'function'		=>	'fields',
			'method'		=>	'parent',
			'module'		=>	$module,
			'table'			=>	$table,
			'exclude'		=>	$exclude,
			'id'			=>	$id,
			'mode'			=>	'view',
			'permissions'	=>	$permissions,
			'uniqueString'	=>	$uniqueString,
			'passedSql'		=>	$passedSql
		) ;
		$buffer = zig($wizard_parameters) ;

		// -- Start process child table
		while($field_fetch=$field_result->fetchRow()) {
			$childTable = $field_fetch['child_table'] ;
			$columnsResult = zig("show_full_columns",$field_fetch['child_table']) ;

			switch(in_array("id",$exclude)) {
				case true: {
					$fields = "id" ;
					break ;
				}
				default: {
					$fields = "" ;
				}
			}
			while($columnsFetch=$columnsResult->fetchRow()) {
				$isHidden = false ;
				$comment = zig("hash","queryStringDecode",$columnsFetch['Comment']) ;
				switch(is_array($comment)) {
					case true: {
						foreach($comment as $key => $value) {
							switch($key) {
								case "attribute": {
									$isHidden = $value=="hidden" ? true : false ;
								}
							}
							if($isHidden) {
								break ;
							}
						}
					}
				}
				switch(in_array($columnsFetch['Field'],$exclude) OR $isHidden) {
					case false: {
						$fields.= $fields ? ",".$columnsFetch['Field'] : $columnsFetch['Field'] ;
					}
				}
			}

			switch($field_fetch['sql_statement']) {
				case "": {
					$sql = "SELECT ${fields} FROM ${childTable} WHERE `zig_parent_id`='${id}'" ;
					break ;
				}
				default: {
					$table = $childTable ;
					$parent_table = $semi_stripped_table ;
					$parent_id = $id ;
					eval("\$sql = \"$field_fetch[sql_statement]\";") ;
					$sql = zig("extractor","extract_addField",$sql,"id") ;
				}
			}
			$wizard_parameters = array(
				'function'		=>	'listing',
				'sql'			=>	$sql,
				'table'			=>	$childTable,
				'parentTable'	=>	$semi_stripped_table,
				'parentId'		=>	$id,
				'print_view'	=> 	true
			) ;
			$recordCount = zig(array("function"=>"select_count","table"=>$childTable,"where"=>"`zig_parent_id`='${id}'")) ;
			$childHtml = zig($wizard_parameters) ;
			switch($field_fetch['fieldset']<>"") {
				case true: {
					$fieldsetName = $field_fetch['fieldset'] ;
					break ;
				}
				default: {
					$explodedTablename = explode("_",$field_fetch['child_table']) ;
					$fieldsetName = count($explodedTablename)>1 ? 
										str_replace($explodedTablename[0],"",$field_fetch['child_table']) : $field_fetch['child_table'] ;
				}
			}
			$fieldsetParameters = array(
				"content"		=>	"{content}", 
				"name"			=>	$fieldsetName." ($recordCount)",
				"collapsed"		=>	true, 
				"collapsible"	=>	$recordCount>0 ? true : false
			) ;
			$childFieldset = new zig_fieldset($fieldsetParameters) ;
			$buffer.= str_replace("{content}",$childHtml,$childFieldset->result['value']) ;
		}
		// -- End process child table

		$editBuffer = new zig_edit($parameters) ;
		$triggerBuffer = zig(array(
					"function"	=> "trigger",
					"action"	=> "view"
					)) ;
		$template = zig("template","file","view") ;
		$buffer = str_replace("{view}",$buffer,$template) ;
		$buffer = str_replace("{edit}",$editBuffer->result['value'],$buffer) ;
		$buffer = str_replace("{trigger}",$triggerBuffer,$buffer) ;
		$buffer = str_replace("{uniqueString}",$uniqueString,$buffer) ;
		$zig_result['value'] = str_replace("{enctype}","",$buffer) ;
		$zig_result['return'] = 1 ;
		return $zig_result ;
	}
}

?>