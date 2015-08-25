<?php

class zig_save {
	function save($parameters,$arg1='',$arg2='',$arg3='') {
		$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
		$exclude = array_key_exists("exclude",$parameters) ? $parameters['exclude'] : zig("config","exclude") ;
		$method = array_key_exists("method",$parameters) ? $parameters['method'] : "parent" ;
		$parent_id = array_key_exists("parentId",$parameters) ? $parameters['parentId'] : NULL ;
		$parent_table = array_key_exists("parentTable",$parameters) ? $parameters['parentTable'] : NULL ;
		$mode = array_key_exists("mode",$parameters) ? $parameters['mode'] : NULL ;
		$module = array_key_exists("module",$parameters) ? $parameters['module'] : NULL ;
		$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
		$uniqueString = array_key_exists("uniqueString",$parameters) ? $parameters['uniqueString'] : NULL ;

		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$validation = true ;
		$magic_quotes = get_magic_quotes_gpc() ;
		$old_kid = 0 ;
		$child_field_patch = $set = $message = "" ;
		$column_info = $field_value = array() ;

		// Start remove the database name on the table
		$semi_stripped_table = str_replace($zig_global_database.".","",$table) ;
		$semi_stripped_parent_table = str_replace($zig_global_database.".","",$parent_table) ;
		// End remove the database name on the table

		// Start stripped table name
		$stripped_table = str_replace($pre,"",$semi_stripped_table) ;
		$stripped_parent_table = str_replace($pre,"",$semi_stripped_parent_table) ;
		// End stripped table name

		switch(count($_FILES)>0 and count($_GET)>0 and count($_POST)==0) {
			case true: {
				$_POST = $_GET ;
			}
		}
		$fieldPrefix = "${semi_stripped_table}_" ;
		$children = 1 ;
		if($method=="child") {
			if($mode=="edit") {
				// -- Start get child SQL
				$sql = "SELECT `fieldset`,`sql_statement` 
						FROM `zig_relationships` 
						WHERE 
							(`parent_table`='$parent_table' OR `parent_table`='$semi_stripped_parent_table' OR `parent_table`='$stripped_parent_table' OR `parent_table`='all tables') AND 
							(`child_table`='$table' OR `child_table`='$semi_stripped_table' OR `child_table`='$stripped_table' OR `child_table`='all tables') AND 
							`child_table`<>'' 
						LIMIT 1" ;
				$child_result = zig("query",$sql) ;
				$child_fetch = $child_result->fetchRow() ;
				if(array_key_exists("sql_statement",$child_fetch) and $child_fetch['sql_statement'])
				{
					eval("\$sql = \"$child_fetch[sql_statement]\";") ;
				}
				else
				{
					$sql = "SELECT * 
							FROM `${pre}${stripped_table}` 
							WHERE `zig_parent_id`='${parent_id}'" ;
				}
				$data_result = zig("query",$sql) ;
				$orig_children = $data_result->RecordCount() ;
				// -- Start get child SQL
			}
			$children = isset($_POST[$semi_stripped_table.'_children']) ? $_POST[$semi_stripped_table.'_children'] : NULL ;
		}

		$customFieldParameters = array(
			"function"		=> "customField",
			"module"		=> $module,
			"table"			=> $table,
			"mode"			=> $mode,
			"fieldValues"	=> $_POST,
			"uniqueString"	=> $uniqueString
		) ;
		$field_info = zig($customFieldParameters) ;

		$fields = $values = $all_messages = "" ;
		$sql = "SHOW COLUMNS FROM $table" ;
		$result = zig("query",$sql,"",false) ;

		while($children) {
			$fields = $values = $set = "" ;
			$children-- ;
			if($method=="child") {
				$old_kid++ ;
				$child_field_patch = "_zig_child_row_count_".$old_kid ;
			}

			if($mode=="edit") {
				if($method=="child") {
					$data_fetch = $data_result->fetchRow() ;
					$id = $data_fetch['id'] ;
				}

				if($id) {
					$sql = "SELECT * 
							FROM `${semi_stripped_table}` 
							WHERE `id`='${id}' 
							LIMIT 1" ;
					$orig_data_result = zig("query",$sql) ;
					$orig_data_fetch = $orig_data_result->fetchRow() ;
				}
			}

			$result->MoveFirst() ;
			while($fetch=$result->fetchRow()) {
				if($mode=="edit" and !array_key_exists($fieldPrefix.$fetch['Field'],$_POST)) {
					continue ;
				}
				$field_fetch = zig("checkArray",$field_info,$fetch['Field']) ;
				$field_fetch = is_array($field_fetch) ? $field_fetch : array() ;
				if(in_array($fetch['Field'],$exclude) or 
					($id and $mode=="edit" and zig("checkArray",$field_fetch,"field_type")<>"computed" and 
						(zig("checkArray",$field_fetch,"attribute")=="hidden" or 
							zig("checkArray",$field_fetch,"attribute")=="readonly"))) {
					continue ;
				}
				$column_info[$fetch['Field']] = $fetch ;

				// -- Start Field Unit
				$field_units = array() ;
				$field_unit_value = isset($_POST[$fieldPrefix.$fetch['Field'].$child_field_patch."_field_unit"]) ? $_POST[$fieldPrefix.$fetch['Field'].$child_field_patch."_field_unit"] : "" ;
				if($field_unit_value) {
					if(($mode<>"add" and $mode<>"copy") and $id) {
						$unit_sql = "SELECT unit_short_name 
										FROM zig_units 
										WHERE 
											`zig_parent_table`='$stripped_table' AND 
											`zig_parent_id`='$id' AND 
											`parent_field`='$fetch[Field]' 
										LIMIT 1" ;
						$unit_result = zig("query",$unit_sql) ;
						$unit_fetch = $unit_result->fetchRow() ;
						$field_unit_value = $field_unit_value<>$unit_fetch['unit_short_name'] ? $unit_fetch['unit_short_name'] : NULL ;
					}
					if($field_unit_value) {
						$field_units[$fetch['Field']] = $field_unit_value ;
					}
				}
				// -- End Field Unit

				if(array_key_exists("defaultValue", $field_fetch) and 
					(zig("checkArray",$field_fetch,"field_type")=="computed" or 
						zig("checkArray",$field_fetch,"attribute")=="hidden" or 
						zig("checkArray",$field_fetch,"attribute")=="readonly")) {
					$field_value[$fetch['Field']] = $field_fetch['defaultValue'] ;
				}
				else {
					switch(zig("checkArray",$field_fetch,"field_type")) {
						case "file":
							if(zig("checkArray",$_FILES,$fetch['Field'].$child_field_patch)) {
								$splitted_filename = explode(".",$_FILES[$fieldPrefix.$fetch['Field'].$child_field_patch]['name']) ;
								$file_extension = (sizeof($splitted_filename)>1) ? ".".$splitted_filename[sizeof($splitted_filename)-1] : "" ;
								$filename = $_FILES[$fieldPrefix.$fetch['Field'].$child_field_patch]['name'] ;
								$files_path = zig("config","files path") ;
								$file_attachment = $files_path.$filename ;
								$filenameBase = substr($filename,0,strlen($filename)-3) ;
								$counter = 1 ;
								while($counter) {
									if(!zig("cache","file_exists",$file_attachment))
									{
										move_uploaded_file($_FILES[$fieldPrefix.$fetch['Field'].$child_field_patch]['tmp_name'],$file_attachment) ;
										break ;
									}
									else
									{
										$filename = $filenameBase.$counter.$file_extension ;
										$file_attachment = $files_path.$filename ;
										$counter++ ;
									}
								}
								$field_value[$fetch['Field']] = $filename ;
							}
							else
							{
								$field_value[$fetch['Field']] = isset($_POST[$fieldPrefix.$fetch['Field'].$child_field_patch]) ? $_POST[$fieldPrefix.$fetch['Field'].$child_field_patch] : "" ;
							}
							break ;
						case "checkbox":
							if($field_fetch['validation']=="unique+blank") {
								$field_value[$fetch['Field']] = isset($_POST[$fieldPrefix.$fetch['Field']]) ? $_POST[$fieldPrefix.$fetch['Field']] : "" ;
							}
							else {
								$field_value[$fetch['Field']] = isset($_POST[$fieldPrefix.$fetch['Field'].$child_field_patch]) ? $_POST[$fieldPrefix.$fetch['Field'].$child_field_patch] : "0" ;
							}
							break ;
						default: {
							switch($fetch['Type']) {
								case "date": {
									$field_value[$fetch['Field']] = isset($_POST[$fieldPrefix.$fetch['Field'].$child_field_patch]) ? $_POST[$fieldPrefix.$fetch['Field'].$child_field_patch] : NULL ;
									$field_value[$fetch['Field']] = zig("datetime",$field_value[$fetch['Field']],"Y-m-d") ;
									break ;
								}
								case "tinyint(1)": {
									$field_value[$fetch['Field']] = isset($_POST[$fieldPrefix.$fetch['Field'].$child_field_patch]) ? $_POST[$fieldPrefix.$fetch['Field'].$child_field_patch] : "0" ;
									break ;
								}
								default: {
									$field_value[$fetch['Field']] = isset($_POST[$fieldPrefix.$fetch['Field'].$child_field_patch]) ? $_POST[$fieldPrefix.$fetch['Field'].$child_field_patch] : NULL ;
									break ;
								}
							}
							break ;
						}
					}
				}

				if($field_value[$fetch['Field']]<>"" and $mode=="add") {
					if($fields and $values) {
						$fields.= "," ;
						$values.= "," ;
					}
					$fields.= "`".$fetch['Field']."`" ;
					$escaped_value = $magic_quotes ? $field_value[$fetch['Field']] : addslashes($field_value[$fetch['Field']]) ;
					$values.= zig("checkArray",$field_fetch,"attribute")=="password" ? "PASSWORD('".$escaped_value."')" 
								: "'".trim($escaped_value)."'" ;
				}
				else if($mode=="edit" and strcmp($orig_data_fetch[$fetch['Field']],$field_value[$fetch['Field']])) {
					if(zig("checkArray",$field_fetch,"attribute")=="password" and $field_value[$fetch['Field']]=="[unchanged]") {
						// -- Start restore password value if unchanged
						$field_value[$fetch['Field']] = $magic_quotes ? $orig_data_fetch[$fetch['Field']] : 
							addslashes($orig_data_fetch[$fetch['Field']]) ;
						// -- End restore password value if unchanged
					}
					else {
						$set = $set ? $set."," : $set ;
						switch($field_value[$fetch['Field']]) {
							case "": {
								$set.= "`".$fetch['Field']."`=NULL " ;
								break ;
							}
							default: {
								$escaped_value = $magic_quotes ? $field_value[$fetch['Field']] : addslashes($field_value[$fetch['Field']]) ;
								$set.= zig("checkArray",$field_fetch,"attribute")=="password" ? 
									"`".$fetch['Field']."`=PASSWORD('".$escaped_value."') " : 
										"`".$fetch['Field']."`='".trim($escaped_value)."' " ;
								break ;
							}
						}
					}
				}
			}

			$zig_validate_parameters = $parameters ;
			$zig_validate_parameters['function'] = "validate" ;
			$zig_validate_parameters['column_info'] = $column_info ;
			$zig_validate_parameters['field_info'] = $field_info ;
			$zig_validate_parameters['field_value'] = $field_value ;
			$zig_result = zig($zig_validate_parameters) ;
			if($zig_result['validation'])
			{
				$validation*= true ;
				if($mode=="add")
				{
					$fields.= ",`zig_created`" ;
					$values.= ",NOW()" ;
					$fields.= ",`zig_user`" ;
					$values.= ",'".zig("info","user")."'" ;
					if($parent_id)
					{
						$fields.= ",`zig_parent_table`" ;
						$values.= ",'$semi_stripped_parent_table'" ;
						$fields.= ",`zig_parent_id`" ;
						$values.= ",'$parent_id'" ;
					}
					$zig_array_result['id'][] = zig("insert",$table,$fields,$values) ;
					$message = "Data Added" ;

					// -- Start Field Unit Insert
					if(count($field_units))
					{
						$unit_fields = ",`zig_created`" ;
						$unit_values = ",NOW()" ;
						$unit_fields.= ",`zig_user`" ;
						$unit_values.= ",'".zig("info","user")."'" ;
						$unit_fields.= ",`zig_parent_table`" ;
						$unit_values.= ",'$semi_stripped_parent_table'" ;
						$unit_fields.= ",`zig_parent_id`" ;
						$unit_values.= ",'$zig_array_result[id]'" ;

						foreach($field_units as $parent_field => $unit_short_name)
						{
							$field_unit_sql = "SELECT `id` FROM zig_field_units 
												WHERE `zig_parent_id`='$field_fetch[id]' AND 
													`short_name`='$unit_short_name' 
												LIMIT 1" ;
							$field_unit_result = zig("query",$field_unit_sql) ;
							$field_unit_fetch = $field_unit_result->fetchRow() ;
							$unit_fields.= ",`parent_field`" ;
							$unit_values.= ",'$parent_field'" ;
							$unit_fields.= ",`unit_id`" ;
							$unit_values.= ",'$field_unit_sql[id]'" ;
							$unit_fields.= ",`unit_short_name`" ;
							$unit_values.= ",'$unit_short_name'" ;
							zig("insert",$pre."units",$unit_fields,$unit_values) ;
						}
					}
					// -- End Field Unit Insert
				}
				else if($set) {
					switch($id<>"") {
						case true: {
							$version = $orig_data_fetch['zig_version'] + 1 ;
							$set.= ",`zig_version`='".$version."' " ;
							$zig_array_result['revisions'][] = zig("update",$table,$set,"WHERE `id`='${id}' LIMIT 1") ;
							$message = "Data Updated" ;
						}
					}
					switch(zig("checkArray",$field_fetch,"field_type")) {
						case "file": {
							switch($field_fetch['attribute']) {
								case "thumbnail": {
									$fieldElementParameters = array(
										"function"			=>	"field_element",
										"mode"				=>	$mode,
										"field_type"		=>	$field_fetch['attribute'],
										"field_value"		=>	trim($escaped_value),
										"field_attribute"	=>	$field_fetch['attribute']
									) ;
									$zig_array_result['html'] = zig($fieldElementParameters) ;
									$zig_array_result['html'] = str_replace("{current_field_name}",$fetch['Field'],$zig_array_result['html']) ;
									$zig_array_result['html'] = str_replace("{uniqueString}",$uniqueString,$zig_array_result['html']) ;
									$zig_array_result['field_value'] = trim($escaped_value) ;
									break ;
								}
								default: {
									$zig_array_result['html'] = trim($escaped_value) ;
								}
							}
						}
					}
				}

				$all_messages.= ($message and strpos($all_messages,$message)===false and $all_messages) ? "<br />" : NULL ;
				$all_messages.= $message ? (strpos($all_messages,$message)===false ? $message : NULL) : NULL ;
				$message = "" ;
				zig("postCommit") ;
			}
			else {
				$validation*= false ;
				$all_messages = $zig_result['message'] ;
				if($method=="parent") {
					break ;
				}
			}
		}

		$zig_array_result['validation'] = $validation ;
		if($all_messages) {
			$zig_array_result['message'] = $all_messages ;
		}
		switch(array_key_exists("id",$zig_array_result)) {
			case true: {
				$zig_array_result['id'] = sizeof($zig_array_result['id']) == 1 ? $zig_array_result['id'][0] : $zig_array_result['id'] ;
			}
		}
		switch(array_key_exists("revisions",$zig_array_result)) {
			case true: {
				$zig_array_result['revisions'] = sizeof($zig_array_result['revisions']) == 1 ? $zig_array_result['revisions'][0] : 
					$zig_array_result['revisions'] ;
			}
		}
		$zig_return['return'] = 1 ;
		$zig_return['value'] = $zig_array_result ;
		//$zig_return['value'] = $all_messages ;

		return $zig_return ;
	}
}

?>