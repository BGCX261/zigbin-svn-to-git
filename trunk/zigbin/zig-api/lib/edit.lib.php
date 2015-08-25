<?php

class zig_edit {
	public $result = array() ;

	function __construct($parameters=NULL,$arg1=NULL,$arg2=NULL,$arg3=NULL) {
		switch($parameters) {
			case NULL: {
				break;
			}
			default: {
				switch(is_array($parameters)) {
					case true: {
						$this->result = $this->edit($parameters,$arg1,$arg2,$arg3) ;
						break ;
					}
					default: {
						$this->result = $this->edit(false,$parameters,$arg1,$arg2) ;
					}
				}
			}
		}
	}

	function edit($parameters,$arg1='',$arg2='',$arg3='') {
		$id = 0 ;
		$detailsMode = "view" ;
		$uniqueString = uniqid() ;
		$childBuffer = $parentTable = $parentId = "" ;
		if($arg1 or $arg2 or $arg3) {
			$table = $arg1 ;
			$id = $arg2 ;
			$uniqueString = $arg3 ;
		}
		if(is_array($parameters)) {
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : $id ;
			$uniqueString = array_key_exists("uniqueString",$parameters) ? $parameters['uniqueString'] : $uniqueString ;
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : NULL ;
			$parentTable = array_key_exists("parentTable",$parameters) ? $parameters['parentTable'] : $parentTable ;
			$parentId = array_key_exists("parentId",$parameters) ? $parameters['parentId'] : $parentId ;
			$exclude = array_key_exists("exclude",$parameters) ? $parameters['exclude'] : zig("config","exclude") ;
			$permissions = array_key_exists("permissions",$parameters) ? $parameters['permissions'] : NULL ;
			$passedSql = array_key_exists("passedSql",$parameters) ? zig("hash","stringEncode",$parameters['passedSql']) : NULL ;
			$detailsMode = array_key_exists("detailsMode",$parameters) ? $parameters['detailsMode'] : $detailsMode ;
		}

		$sql = "SELECT * 
				FROM 
					`zig_relationships` 
				WHERE 
					(parent_table='${table}' OR 
						parent_table='all tables') AND 
					`child_table`<>'' AND 
					`child_table`<>'${table}' 
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
			'mode'			=>	'edit',
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
					$parent_table = $table ;
					$parent_id = $id ;
					$table = $childTable ;
					eval("\$sql = \"$field_fetch[sql_statement]\";") ;
					$sql = zig("extractor","extract_addField",$sql,"id") ;
					$table = $parent_table ;
				}
			}
			$wizard_parameters = array(
				'function'		=>	'listing',
				'module'		=>	$module,
				'sql'			=>	$sql,
				'table'			=>	$childTable,
				'parentTable'	=>	$table,
				'parentId'		=>	$id,
				'detailsMode'	=>	"edit"
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
				"collapsed"		=>	true
			) ;
			$childFieldset = new zig_fieldset($fieldsetParameters) ;
			$childFieldset = str_replace("{content}",$childHtml,$childFieldset->result['value']) ;
			$childBuffer.= $childFieldset ;
		}
		// -- End process child table

		switch($detailsMode) {
			case "edit": {
				$template = zig("template","block","edit","edit") ;
				break ;
			}
			case "view":
			default: {
				$template = zig("template","block","edit","view") ;
			}
		}

		$triggerBuffer = zig(array(
			"function"	=> "trigger",
			"action"	=> "add"
			)) ;
		$saveHash = zig("hash","encrypt","function=editRecord,module=${module},table=${table},id=${id},parentTable=${parentTable},
						parentId=${parentId},sql=${passedSql},uniqueString=${uniqueString},zigjax=1") ;
		$buffer = str_replace("{parent}",$buffer,$template) ;
		$buffer = str_replace("{child}",$childBuffer,$buffer) ;
		$buffer = str_replace("{trigger}",$triggerBuffer,$buffer) ;
		$buffer = str_replace("{uniqueString}",$uniqueString,$buffer) ;
		$buffer = str_replace("{saveHash}",$saveHash,$buffer) ;
		$buffer = strpos($buffer," type='file' ") ? 
					str_replace("{enctype}","enctype='multipart/form-data'",$buffer) : str_replace("{enctype}","",$buffer) ;
		$zig_result['value'] = $buffer ;
		return $zig_result ;
	}
}

?>