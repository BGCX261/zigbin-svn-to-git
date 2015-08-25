<?php

class zig_fields {
	function fields($parameters,$arg1='',$arg2='',$arg3='') {
		$parent_value = "" ;
		$uniqueString = uniqid() ;
		if($arg1 or $arg2 or $arg3) {
			$method = $arg1 ;
			$table = $arg2 ;
			$exclude = $arg3 ;
		}
		if(is_array($parameters)) {
			$method = array_key_exists("method",$parameters) ? $parameters['method'] : NULL ;
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$exclude = array_key_exists("exclude",$parameters) ? $parameters['exclude'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : NULL ;
			$parent_table = array_key_exists("parent_table",$parameters) ? $parameters['parent_table'] : NULL ;
			$parent_id = array_key_exists("parent_id",$parameters) ? $parameters['parent_id'] : NULL ;
			$mode = array_key_exists("mode",$parameters) ? $parameters['mode'] : NULL ;
			$data_saved = array_key_exists("data_saved",$parameters) ? $parameters['data_saved'] : NULL ;
			$invalid_fields = array_key_exists("invalid_fields",$parameters) ? $parameters['invalid_fields'] : NULL ;
			$permissions = array_key_exists("permissions",$parameters) ? $parameters['permissions'] : NULL ;
			$details = array_key_exists("details",$parameters) ? $parameters['details'] : NULL ;
			$revisions = array_key_exists("revisions",$parameters) ? $parameters['revisions'] : NULL ;
			$return_fields = array_key_exists("return_fields",$parameters) ? $parameters['return_fields'] : false ;
			$parent_value = array_key_exists("parent_value",$parameters) ? $parameters['parent_value'] : $parent_value ;
			$passedUniqueString = array_key_exists("uniqueString",$parameters) ? $parameters['uniqueString'] : $uniqueString ;
			$passedSql = array_key_exists("passedSql",$parameters) ? $parameters['passedSql'] : NULL ;
		}

		$date_format_help = zig("config","date format help") ;
		$date_format = zig("config","date format") ;
		$nowrap = $mode=="view" ? NULL : "nowrap='nowrap'" ;
		$zig_hash = "action=view,id=" ;
		$field_columns = zig("config","field_columns") ;
		$child_columns = zig("config","child_columns") ;
		$minimum_columns = zig("config","minimum_columns") ;
		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;

		// Start remove the database name on the table
		$semi_stripped_table = str_replace($zig_global_database.".","",$table) ;
		$semi_stripped_parent_table = str_replace($zig_global_database.".","",$parent_table) ;
		// End remove the database name on the table

		// Start stripped table name
		$stripped_table = str_replace($pre,"",$semi_stripped_table) ;
		$stripped_parent_table = str_replace($pre,"",$semi_stripped_parent_table) ;
		// End stripped table name

		$fieldsets[] = $where_fieldset = $child_row_model_buffer = $buffer = $header_fields = $zig_row_color = "" ;
		$child_row_model_flag = false ;
		$field_info = array() ;

		// -- Start get field info
		$customFieldParameters = array(
			"function"		=> "customField",
			"module"		=> $module,
			"table"			=> $table,
			"id"			=> $id, 
			"mode"			=> $mode,
			"uniqueString"	=> $passedUniqueString
		) ;
		$field_info = zig($customFieldParameters) ;

		foreach($field_info as $fieldset_fetch) {
			switch(array_key_exists("fieldset", $fieldset_fetch)) {
				case true: {
					$fieldset_fetch['fieldset'] = $fieldset_fetch['fieldset'] ;
					$where_fieldset.= $where_fieldset ? 
										" OR `fieldset`='".addslashes($fieldset_fetch['fieldset'])."'" : 
										"`fieldset`='".addslashes($fieldset_fetch['fieldset'])."'" ;
					$fieldsets[] = $fieldset_fetch['fieldset'] ;
				}
			}
		}

		if($where_fieldset) {
			$fieldset_info_sql = "SELECT 
										`fieldset`,
										`collapsed`,
										`collapsible`,
										`description` 
									FROM 
										`zig_relationships` 
									WHERE 
										( $where_fieldset ) AND 
										`parent_table` = '${semi_stripped_table}' AND 
										(`child_table`='' OR `child_table` IS NULL) 
									ORDER BY 
										`zig_weight`,`fieldset`" ;
			$fieldset_info_result = zig("query",$fieldset_info_sql) ;
			while($fieldset_info_fetch=$fieldset_info_result->fetchRow())
			{
				$fieldset_info[$fieldset_info_fetch['fieldset']]['fieldset'] = $fieldset_info_fetch['fieldset'] ;
				$fieldset_info[$fieldset_info_fetch['fieldset']]['collapsed'] = $fieldset_info_fetch['collapsed'] ;
				$fieldset_info[$fieldset_info_fetch['fieldset']]['collapsible'] = $fieldset_info_fetch['collapsible'] ;
				$fieldset_info[$fieldset_info_fetch['fieldset']]['description'] = $fieldset_info_fetch['description'] ;
			}
		}

		foreach($fieldsets as $fieldset)
		{
			if(isset($fieldset_info)) {
				if(!zig("checkArray",$fieldset_info,$fieldset,"arrayKeyExists"))
				{
					$fieldset_no_info[$fieldset]['fieldset'] = $fieldset ;
					$fieldset_no_info[$fieldset]['collapsed'] = false ;
					$fieldset_no_info[$fieldset]['collapsible'] = false ;
				}
			}
			else
			{
				$fieldset_no_info[$fieldset]['fieldset'] = $fieldset ;
				$fieldset_no_info[$fieldset]['collapsed'] = false ;
				$fieldset_no_info[$fieldset]['collapsible'] = false ;
			}
		}

		if(isset($fieldset_info))
		{
			$fieldset_info = array_merge($fieldset_no_info,$fieldset_info) ;
		}
		else
		{
			$fieldset_info = $fieldset_no_info ;
		}
		// -- End get fieldsets

		// -- Start of fieldset loop
		foreach($fieldset_info as $fieldset => $fieldset_info_array) {
			$sql = "SHOW COLUMNS FROM ${table}" ;
			$column_result = zig("query",$sql) ;

			$weight_parameters = array (
				'function'		=>	"weight",
				'column_result'	=>	$column_result,
				'exclude'		=>	$exclude,
				'table'			=>	$table,
				'field_info'	=>	$field_info
			) ;
			$sorted_fields = zig($weight_parameters) ;
			$field_count = $child_count = 0 ;
			$fields = "" ;
			foreach($sorted_fields as $fetch)
			{
				switch(zig("checkArray",$field_info,$fetch['Field'],"arrayKeyExists"))
				{
					case true:
					{
						$fieldInfoAttribute = zig("checkArray",$field_info[$fetch['Field']],"attribute") ;
						$fieldInfoFieldset = zig("checkArray",$field_info[$fetch['Field']],"fieldset") ;
						break ;
					}
					default:
					{
						$fieldInfoAttribute = "" ;
						$fieldInfoFieldset = "" ;
					}
				}
				if($fieldInfoAttribute=="virtual") {
					continue ;
				}
				$field_count+= (in_array($fetch['Field'],$exclude) or $fieldInfoAttribute=="hidden" or $fieldInfoFieldset<>$fieldset) ? 0 : 1 ;
				$fields.= $fields ? ",`".$fetch['Field']."`" : "`".$fetch['Field']."`" ;
			}
			$titlecase_table = str_replace("_"," ",$stripped_table) ;

			if($method=="child")
			{
				if($mode<>"add")
				{
					// -- Start add id on fields 
					$fields = ($mode=="edit" and !strpos($fields,"/id/")) ? "`id`,".$fields : $fields ;
					// -- End add id on fields
				
					// Start get parent data
					$sql = "SHOW COLUMNS FROM `${pre}$stripped_parent_table`" ;
					$parent_column_result = zig("query",$sql) ;
					$sql = "SELECT * FROM `${pre}$stripped_parent_table` WHERE `id`='$parent_id' LIMIT 1" ;
					$parent_result = zig("query",$sql) ;
					$parent_fetch = $parent_result->fetchRow() ;

					// -- Start create parent field variables
					while($parent_column_fetch = $parent_column_result->fetchRow())
					{
						$parent_field_variable = "parent_".$parent_column_fetch['Field'] ;
						$$parent_field_variable = $parent_fetch[$parent_column_fetch['Field']] ;
					}
					// -- End create parent field variable
				
					// End get parent data
				}
				
				// -- Start get child SQL
				$sql = "SELECT 
							`fieldset`, 
							`collapsed`, 
							`collapsible`, 
							`description`, 
							`sql_statement` 
						FROM 
							`zig_relationships` 
						WHERE (`parent_table`='$parent_table' OR `parent_table`='$semi_stripped_parent_table' OR `parent_table`='$stripped_parent_table' OR `parent_table`='all tables') AND (`child_table`='${table}' OR `child_table`='${semi_stripped_table}' OR `child_table`='${stripped_table}' OR `child_table`='all tables') AND 
							`child_table`<>'' 
						LIMIT 1" ;
				$child_result = zig("query",$sql) ;
				$sql = NULL ;
				$children = 0 ;
				$child_fetch = array() ;
				if($child_result->RecordCount())
				{
					$child_fetch = $child_result->fetchRow() ;
					if($mode<>"add")
					{
						if(array_key_exists("sql_statement",$child_fetch) and $child_fetch['sql_statement'])
						{
							eval("\$sql = \"$child_fetch[sql_statement]\";") ;
						}
						else
						{
							$sql = "SELECT ${fields} 
									FROM `${pre}${stripped_table}` 
									WHERE `zig_parent_id`='${parent_id}' AND 
									ORDER BY `id` DESC" ;
						}
						$data_result = zig("query",$sql) ;
						$children = $data_result->RecordCount() ;
					}
				}
				// -- Start get child SQL
				
				$loop = $mode<>"view" ? ($children + 1) : $children ;
				$children = isset($_GET[$semi_stripped_table.'_children']) ? $_GET[$semi_stripped_table.'_children'] : (isset($_POST[$semi_stripped_table.'_children']) ? $_POST[$semi_stripped_table.'_children'] : $children) ;
				$add = isset($_GET['add_'.$semi_stripped_table]) ? $_GET['add_'.$semi_stripped_table] : (isset($_POST['add_'.$semi_stripped_table]) ? $_POST['add_'.$semi_stripped_table] : '') ;

				$children = $add ? ($children + 1) : $children ;
				
				// -- Start Child Field Header
				if($mode<>"view")
				{
					$kid = $children ;
					$kids = $children ;
					while($kid)
					{
						$remove_kid = $semi_stripped_table."_remove_".$kid ;
						$remove = isset($_GET[$remove_kid]) ? $_GET[$remove_kid] : (isset($_POST[$remove_kid]) ? $_POST[$remove_kid] : '') ;
						if($remove)
						{
							$kids-- ;
							break ;
						}
						$kid-- ;
					}
				}

				$kids_value = "value='$kids'" ;
				$buffer_header = $mode=="view" ? zig("template","block","fields","fields header view") : zig("template","block","fields","fields header") ;
				$buffer_header = str_replace("{semi_stripped_table}",$semi_stripped_table,$buffer_header) ;
				$buffer_header = str_replace("{kids_value}",$kids_value,$buffer_header) ;

				if($loop)
				{
					if($mode<>"view" and $method=="child")
					{
						$child_row_model_flag = true ;
						$kids-- ;
					}

					if(count($sorted_fields)<=$child_columns)
					{
						foreach($sorted_fields as $fetch)
						{
							switch(zig("checkArray",$field_info,$fetch['Field'],"arrayKeyExists"))
							{
								case true:
								{
									$fieldInfoAttribute = $field_info[$fetch['Field']]['attribute'] ;
									$fieldInfoFieldset = $field_info[$fetch['Field']]['fieldset'] ;
									$fieldInfoFieldLabel = $field_info[$fetch['Field']]['field_label'] ;
									break ;
								}
								default:
								{
									$fieldInfoAttribute = "" ;
									$fieldInfoFieldset = "" ;
									$fieldInfoFieldLabel = "" ;
								}
							}
							if(in_array($fetch['Field'],$exclude) or 
								$fieldInfoAttribute=="hidden" or 
								$fieldInfoFieldset<>$fieldset) {
								continue ;
							}

							$header_fields.= zig("template","block","fields","fields header cell") ;
							$field_name = str_replace("_"," ",$fetch['Field']) ;
							$field_name = $fieldInfoFieldLabel<>"" ? $fieldInfoFieldLabel : "[".ucwords(trim($field_name))."]" ;
							$header_fields = str_replace("{field_name}",$field_name,$header_fields) ;
						}
					}
					else
					{
						$header_fields.= zig("template","block","fields","fields header cell") ;
						$header_fields = str_replace("{field_name}","[Fields]",$header_fields) ;
					}
				}
				$buffer_header = str_replace("{header_fields}",$header_fields,$buffer_header) ;
				unset($kid) ;
				// -- End Child Field Header
			}
			// --  End If "child" condition

			if($method=="parent" and $mode<>"add")
			{
				// -- Start get data
				$sql = "SELECT ${fields} FROM ${table} WHERE `id`='${id}' LIMIT 1" ;
				$data_result = zig("query",$sql) ;
				// -- End get data
			}
			if($method=="parent")
			{
				$loop = 1 ;
				$buffer_header = "<table id='zig_fields_child_${table}' class='zig_table_fields'>\n" ;
			}

		// -- Start while loop on fields
		$buffer = $zig_remove_positions = "" ;
		$kid = -1 ;
		$fields_computed_totals = array() ;
		while($loop)
		{
			$column = 0 ;
			$row = 0 ;
			$data_fetch = ($mode<>"add" and !$child_row_model_flag) ? $data_result->fetchRow() : NULL ;
			$loop-- ;
			$kid++ ;
			if($method=="child")
			{
				$zig_old_remove_positions = isset($_GET['zig_remove_positions_${semi_stripped_table}']) ? $_GET['zig_remove_positions_${semi_stripped_table}'] : (isset($_POST['zig_remove_positions_${semi_stripped_table}']) ? $_POST['zig_remove_positions_${semi_stripped_table}'] : NULL) ;
				$zig_remove_positions = $zig_remove_positions ? $zig_remove_positions.",$data_fetch[id]=$kid" : "$data_fetch[id]=$kid" ;
				$zig_hash_id = zig("hash","encrypt",$data_fetch['id']) ;
				$zig_remove_button_name_patch = $child_row_model_flag ? "{row_count}" : "$child_count" ;
				$child_count = $child_row_model_flag ? $child_count : $child_count + 1 ;
				$zig_row_color = ($zig_row_color=="zig_row_color2") ? "zig_row_color3" : "zig_row_color2" ;
				$class = "class='$zig_row_color'" ;
				$buffer.= "<tr>\n" ;
				if(!$child_row_model_flag or !$child_row_model_buffer)
				{
					$buffer.= "<td align='center'>" ;
					$buffer.= "<strong>" ;
					$buffer.= $child_row_model_flag ? "{row_count}" : "$child_count" ;
					$buffer.= "</strong>" ;
					$buffer.= "</td>\n" ;
				}
				$buffer.= $mode=="view" ? NULL : "<td align='center'><a href=\"javascript:void(0) ;\" onclick=\"javascript:deleteCurrentRow(this,'${semi_stripped_table}','${zig_hash_id}') ; {zig_total_update_on_remove} \">remove</a></td>\n" ;			
			}

			foreach($sorted_fields as $fetch){
				// -- Start get field information
				$field_fetch = zig("checkArray",$field_info,$fetch['Field']) ? $field_info[$fetch['Field']] : array() ;
				// -- End get field information

				// -- Start check field exclusion
				if(in_array($fetch['Field'],$exclude) or 
					zig("checkArray",$field_fetch,"attribute")=="hidden" or 
					zig("checkArray",zig("checkArray",$field_info,$fetch['Field']),"fieldset")<>$fieldset) {
					continue ;
				}
				// -- End check field exclusion

				// -- Start change mode due to permissions
				$original_mode = $mode ;
				if(zig("checkArray",zig("checkArray",$permissions,$fetch['Field']),"view")=="allow" and $mode<>"view")
				{
					switch($mode)
					{
						case "add":
						{
							$mode = $permissions[$fetch['Field']]['add']=="deny" ? "view" : $mode ;
							break ;
						}

						case "edit":
						{
							$mode = $permissions[$fetch['Field']]['edit']=="deny" ? "view" : $mode ;
							break ;
						}

						case "copy":
						{
							$mode = $permissions[$fetch['Field']]['copy']=="deny" ? "view" : $mode ;
						}
					}
				}
				// -- End change mode due to permissions

				// -- Start get attribute script
				$attribute_script = zig("checkArray",$field_fetch,"elementAttributes") ? $field_fetch['elementAttributes'] : "" ;
				$zig_total_update_on_remove = "" ;
				if($fetch['Type']=="double" and $method=="child")
				{
					switch(zig("checkArray",$field_fetch,"field_type"))
					{
						case "input" or "":
						{
							if($attribute_script=="")
							{
								$attribute_script = "onkeyup='total_update(\"zig_field_total_".$fetch['Field']."\",this.id) ;'" ;
							}
							else if(strpos($attribute_script,"onkeyup=")===false)
							{
								$attribute_script.= " onkeyup='total_update(\"zig_field_total_".$fetch['Field']."\",this.id) ;'" ;
							}
							else
							{
								$attribute_script = str_replace('onkeyup="','onkeyup="total_update(\'zig_field_total_'.$fetch['Field'].'\',this.id) ; ',$attribute_script) ;
							}
							break ;
						}
						case "select":
						{
							if($attribute_script=="")
							{
								$attribute_script = "onchange='total_update(\"zig_field_total_".$fetch['Field']."\",this.id) ;'" ;
							}
							else if(strpos($attribute_script,"onchange=")===false)
							{
								$attribute_script.= " onchange='total_update(\"zig_field_total_".$fetch['Field']."\",this.id) ;'" ;
							}
							else
							{
								$attribute_script = str_replace('onchange="','onchange="total_update(\'zig_field_total_'.$fetch['Field'].'\',this.id) ; ',$attribute_script) ;
							}
							break ;
						}
					}
					$zig_total_update_on_remove.= " total_update('zig_field_total_".$fetch['Field']."','".$fetch['Field']."_zig_child_row_count_{row_count}_id') ; " ;
				}
				// -- End get attribute script

				// -- Start field name
				if($method=="child")
				{
					$old_kid = 0 ;
					$current_field_name = $child_row_model_flag ? $fetch['Field']."_zig_child_row_count_{row_count}" : $fetch['Field']."_zig_child_row_count_".$kid ;
					if($zig_old_remove_positions)
					{
						$splitted_zig_old_remove_positions = explode(",",$zig_old_remove_positions) ;
						foreach($splitted_zig_old_remove_positions as $zig_key_position)
						{
							$splitted_zig_key_position = explode("=",$zig_key_position) ;
							if($splitted_zig_key_position[0]==$data_fetch['id'])
							{
								$old_kid = $splitted_zig_key_position[1] ;
								break ;
							}
						}
					}
					$old_kid = $old_kid ? $old_kid : $kid ;
					$old_field_name = $fetch['Field']."_zig_child_row_count_".$old_kid ;
					if(!$column and count($sorted_fields)>$child_columns)
					{
						$buffer = $row ? $buffer."\n<tr>\n" : $buffer."<td>\n<table>\n<tr>\n" ;
					}
				}
				if($method=="parent")
				{
					$current_field_name = $old_field_name = $fetch['Field'] ;
					$buffer = $column ? $buffer : $buffer."<tr>\n" ;
				}
				// -- End field name

				$alignment = "" ;
				if($method=="child" and count($sorted_fields)<=$child_columns)
				{
					// -- Start alignment set on child cell
					if($fetch['Type']=="double" or $fetch['Type']=="float")
					{
						$alignment = "align='right'" ;
					}
					else if($fetch['Type']=="tinyint(1)")
					{
						$alignment = "align='center'" ;
					}
					// -- End alignment set on child cell
				}
				else
				{
					$field_name = str_replace("_"," ",$fetch['Field']) ;
					if(zig("checkArray",$field_fetch,"balloon_tip"))
					{
						$field_fetch['balloon_tip'] = str_replace("\n","",$field_fetch['balloon_tip']) ;
						$field_fetch['balloon_tip'] = htmlspecialchars($field_fetch['balloon_tip']) ;
						$balloon_tip = "onmouseover=\"Tip('$field_fetch[balloon_tip]', BALLOON, true, ABOVE, true, OFFSETX, -17, FADEIN, 600, FADEOUT, 600, PADDING, 8)\" onmouseout=\"UnTip()\"" ;
					}
					else
					{
						$balloon_tip = "" ;
					}
					$buffer.= "<td class='zig_fields_label' ${balloon_tip} >" ;
					$buffer.= zig("checkArray",$field_fetch,"field_label") ? "[".$field_fetch['field_label']."]" : "[".ucwords(trim($field_name))."]" ;
					$buffer.= "</td>\n" ;
					$buffer.= "<td>" ;
					$buffer.= ":" ;
					$buffer.= "</td>\n" ;
				}

				$column++ ;
				$buffer.= "<td ${alignment} ${nowrap}>\n" ;

				$value = "" ;
				if($mode<>"view" or $original_mode=="add" or zig("checkArray",$field_fetch,"attribute")=="virtual") {
					$value = array_key_exists("defaultValue", $field_fetch) ? $field_fetch['defaultValue'] : $fetch['Default'] ;
				}

				$field_data_required_flag = ($fetch['Null']=="NO" and $mode=="add") ? "<font color='#FF0000'>*</font>" : NULL ;
				if($mode=="add") {
					$fieldValue = $value ;
					$buffer.= zig("template","block","fields","fields div add") ;
				}
				else // -- Start if view or edit
				{
					$fieldValue = array_key_exists($fetch['Field'], $data_fetch) ? zig("checkArray",$data_fetch,$fetch['Field']) : $value ;
					switch($fetch['Type']) {
						case "double": {
							$fields_computed_totals[$fetch['Field']] = zig("checkArray",$fields_computed_totals,$fetch['Field']) + $data_fetch[$fetch['Field']] ;
						}
					}
				}

				$buffer.= zig(array(
					"function"				=>	"field_element",
					"mode"					=>	$mode,
					"table"					=>	$table,
					"dbDefinedField"		=>	$fetch,
					"userDefinedField"		=>	$field_fetch,
					"field_value"			=>	$fieldValue,
					"current_field_name"	=>	$current_field_name,
					//"defaultValue"			=>	$value,
					"elementAttributes"		=>	$attribute_script
				)) ;

				// -- Start field units
				$fieldFetchId = zig("checkArray",$field_fetch,"id") ? $field_fetch['id'] : "" ;
				$unit_sql = "SELECT `short_name` FROM `$zig_global_database`.`${pre}field_units` WHERE `zig_parent_id`='$fieldFetchId'" ;
				$unit_result = zig("query",$unit_sql) ;
				$unit_count = $unit_result->RecordCount() ;
				if($unit_count)
				{
					if($unit_count>1)
					{
						$unit_default_sql = "SELECT `short_name` FROM `${pre}field_units` WHERE `zig_parent_id`='$field_fetch[id]' AND `default`='1' LIMIT 1" ;
						$unit_default_result = zig("query",$unit_default_sql) ;
						$unit_default_fetch = $unit_default_result->fetchRow() ;
						$unit_default = $unit_default_fetch['short_name'] ;
						
						if($id)
						{
							$unit_value_sql = "SELECT `unit_short_name` FROM `zig_units` WHERE `zig_parent_table`='${stripped_table}' AND `parent_field`='$current_field_name' and `zig_parent_id`='$id' LIMIT 1" ;
							$unit_value_result = zig("query",$unit_value_sql) ;
							$unit_value_fetch = $unit_value_result->fetchRow() ;
							$unit_default = $unit_value_fetch['field_short_name'] ;
						}

						$unit_field_properties_sql = "SELECT `attribute` FROM `zig_fields` WHERE `field`='${field_fetch[field]}_field_unit' LIMIT 1" ;
						$unit_field_properties_result = zig("query",$unit_field_properties_sql) ;
						$unit_field_properties_fetch = $unit_field_properties_result->fetchRow() ;

						$droplist_method = ($mode=="add" or $mode=="edit" or $mode=="copy") ? ($unit_field_properties_fetch['attribute']=="suggest"? "suggest" : NULL) : "selected_label" ;
						$unit_droplist_parameters = array
						(
							'function'	=>	"droplist",
							'method'	=>	$droplist_method,
							'sql'		=>	$unit_sql,
							'value'		=>	"short_name",
							'default'	=>	$unit_default,
							'name'		=>	$current_field_name."_field_unit",
							'class'		=>	"zig_droplist_unit",
							'table'		=>	"field_units"
						) ;
						$field_unit = zig($unit_droplist_parameters) ;
					}
					else
					{
						$unit_fetch = $unit_result->fetchRow() ;
						$field_unit = $unit_fetch['short_name'] ;
					}

					$unit_template = zig("template","block","fields","unit") ;
					$field_unit_buffer.= str_replace("{zig_div_fields_unit}",$field_unit,$unit_template) ;
					$buffer.= $field_unit_buffer ;
					unset($field_unit_buffer) ;
				}
				// -- End field units

				$buffer.= $mode=="add" ? "</div>\n" : "" ;

				// -- Start Field Description
				/*$fieldDescription = "" ;
				if($mode<>"view")
				{
					switch(zig("checkArray",$field_fetch,"description"))
					{
						case "":
						case false:
						{
							switch($fetch['Type'])
							{
								case "date":
								{
									$field_fetch['description'] = $date_format_help ;
								}
							}
							break ;
						}
						default:
						{
							$fieldDescription = zig("template","block","fields","description") ;
							$fieldDescription = str_replace("{description}",$field_fetch['description'],$fieldDescription) ;
						}
					}
					switch($mode)
					{
						case "add":
						{
							$buffer.= $fieldDescription ;
						}
						default:
						{
							$buffer = str_replace("{fieldDescription}",$fieldDescription,$buffer) ;
						}
					}
				}*/
				// -- End Field Description

				$buffer.= "</td>\n" ;
				if($method=="parent" or ($method=="child" and count($sorted_fields)>$child_columns))
				{
					$buffer.= "<td>" ;
					$buffer.= "&nbsp;" ;
					$buffer.= "</td>\n" ;

					if($column==$field_columns or $minimum_columns>=$field_count)
					{
						$buffer.= "</tr>\n" ;
						$column = 0 ;
						$row++ ;
					}
				}

				// -- Start parsing hashed variables
				/*switch(is_array($field_info))
				{
					case true:
					{
						if(zig("checkArray",zig("checkArray",$field_info,$fetch['Field']),"hashed_variables","isArray"))
						{
							foreach($field_info[$fetch['Field']]['hashed_variables'] as $hashed_variable_set)
							{
								$hashed_variable_set['hash'] = str_replace("{uniqueString}",$uniqueString,$hashed_variable_set['hash']) ;
								$buffer = str_replace($hashed_variable_set['variable'],zig("hash","encrypt",$hashed_variable_set['hash']),$buffer) ;
							}
						}
					}
				}*/
				// -- End parsing hashed variables

				$zigHash = zig("hash","encrypt","function=editRecord,table=${table},sql=$passedSql,id=${id},uniqueString=${passedUniqueString},zigjax=1") ;
				$buffer = str_replace("{stripped_table}",$stripped_table,$buffer) ;
				$buffer = str_replace("{escaped_data}",htmlspecialchars(zig("checkArray",$data_fetch,$fetch['Field']),ENT_QUOTES),$buffer) ;
				$buffer = str_replace("{fieldValue}",$fieldValue,$buffer) ;
				$buffer = str_replace("{uniqueString}",$uniqueString,$buffer) ;
				$buffer = str_replace("{passedUniqueString}",$passedUniqueString,$buffer) ;
				$buffer = str_replace("{zigHash}",$zigHash,$buffer) ;
				$buffer = str_replace("{tableName}",$semi_stripped_table,$buffer) ;
				$buffer = str_replace("{current_field_name}",$current_field_name,$buffer) ;
				unset($posted_value,$alignment,$balloon_tip,$attribute_script,$field_countdown_flag) ;
				$mode = $original_mode ;
			}
			// -- End sorted field loop

			if(($column and $method=="parent") or $method=="child")
			{
				$buffer.= "</tr>\n" ;
				if($child_row_model_flag)
				{
					$buffer = str_replace("{zig_total_update_on_remove}",$zig_total_update_on_remove,$buffer) ;
					$child_row_model_buffer.= $buffer ;
					$buffer = "" ;
				}
				else if(count($sorted_fields)>$child_columns)
				{
					$buffer = count($sorted_fields)>$child_columns ? $buffer."</table>\n</td>\n</tr>\n" : $buffer ;
				}
			}
			$buffer = str_replace("{zig_total_update_on_remove}",NULL,$buffer) ;
			$child_row_model_flag = false ;
		}
		// -- End while loop on fields

		// -- Start total
		if($method=="child" and count($sorted_fields)<=$child_columns)
		{
			$buffer.= $this->fields_total("fields_total",$sorted_fields,$fields_computed_totals) ;
		}
		// -- End total

		// -- Start close table
		$buffer = ($buffer or $method=="child") ? $buffer_header.$buffer."</table>\n" : NULL ;
		// -- End close table

		if($method=="child") {
			$stack_buffer = $buffer ;
		}
		else {
			if($details and !$fieldset) {
				$fieldsetObject = new zig_fieldset("{fields}","Main Data",false) ;
				$template = $fieldsetObject->result['html'] ;
			}
			else if($fieldset and $buffer) {
				$fieldset_parameters = array(
					'function'		=>	"fieldset",
					'content'		=>	"{fields}",
					'name'			=>	$fieldset,
					'collapsed'		=>	$fieldset_info[$fieldset]['collapsed'],
					'collapsible'	=>	$fieldset_info[$fieldset]['collapsible'],
					'description'	=>	zig("checkArray",$fieldset_info[$fieldset],"description")
				) ;
				$fieldsetObject = new zig_fieldset($fieldset_parameters) ;
				$template = $fieldsetObject->result['html'] ;
			}
			else
			{
				$template = "{fields}" ;
			}
			$buffer = str_replace("{fields}",$buffer,$template) ;
			$stack_buffer = isset($stack_buffer) ? $stack_buffer.$buffer : $buffer ;
		}
			$fields_array[] = $sorted_fields ;
			unset($buffer) ;
		}
		// -- End of fieldset loop

		if($method=="child")
		{
			// -- Start child action button
			if($mode<>"view")
			{
				$stack_buffer.= zig("template","block","fields","child add button") ;
				$stack_buffer = str_replace("{semi_stripped_table}",$semi_stripped_table,$stack_buffer) ;
				$stack_buffer = str_replace("{stripped_table}",$stripped_table,$stack_buffer) ;

				$stack_buffer.= zig("template","block","fields","child row model") ;
				if($child_row_model_buffer)
				{
					$child_row_model_buffer = count($sorted_fields)>$child_columns ? $child_row_model_buffer."</table>\n</td>\n</tr>\n" : $child_row_model_buffer ;
					$child_row_model_buffer = "<table>\n${child_row_model_buffer}</table>" ;
				}
				$stack_buffer = str_replace("{zig_child_fields_row}",$child_row_model_buffer,$stack_buffer) ;
				$stack_buffer = str_replace("{stripped_table}",$stripped_table,$stack_buffer) ;
			}
			// -- End child action button

			$stack_buffer.= "<input type='hidden' id='zig_remove_${semi_stripped_table}_id' name='zig_remove_${semi_stripped_table}' value='' />" ;
			$stack_buffer.= "<input type='hidden' id='zig_remove_positions_${semi_stripped_table}_id' name='zig_remove_positions_${semi_stripped_table}' value='$zig_remove_positions' />" ;
			$titlecase_table = (is_array($child_fetch) and array_key_exists("fieldset",$child_fetch) and $child_fetch['fieldset']<>"") ? $child_fetch['fieldset'] : ucwords(trim($titlecase_table)) ;
			$titlecase_table.= $mode<>"add" ? " (${children}) " : "" ;
			$collapsed = ($children or $mode<>"view") ? zig("checkArray",$child_fetch,"collapsed") : true ;
			$collapsible = ($children or $mode<>"view") ? zig("checkArray",$child_fetch,"collapsible") : false ;
			$fieldset_parameters = array
			(
				'function'		=>	"fieldset",
				'content'		=>	"{child_content}",
				'name'			=>	$titlecase_table,
				'collapsed'		=>	$collapsed,
				'collapsible'	=>	$collapsible,
				'description'	=>	zig("checkArray",$child_fetch,"description")
			) ;
			$child_buffer = zig($fieldset_parameters) ;
			$stack_buffer = str_replace("{child_content}",$stack_buffer,$child_buffer) ;
			$template = zig("template","block","fields","fields") ;
			$stack_buffer = str_replace("{fields}",$stack_buffer,$template) ;
		}

		// -- Start Details
		/*if($method=="parent" and ($mode=="view" or $mode=="edit") and $details)
		{
			$details_parameters = array
			(
				"function"		=>	"field_details",
				"sorted_fields"	=>	$sorted_fields,
				"fetch"			=>	$fetch,
				"exclude"		=>	$exclude,
				"table"			=>	$table,
				"id"			=>	$id
			) ;
			$stack_buffer.= $this->fields_details($details_parameters) ;
		}*/
		// -- End Details

		// -- Start Revisions
		if($method=="parent" and ($mode=="view" or $mode=="edit") and $revisions)
		{
			$stack_buffer.= $this->fields_revisions("field_revisions",$stripped_table,$id) ;
		}
		// -- End Revisions

		if($return_fields)
		{
			$zig_return_array['html'] = $stack_buffer ;
			$zig_return_array['fields'] = $fields_array ;
			$zig_result['value'] = $zig_return_array ;
		}
		else
		{
			$zig_result['value'] = $stack_buffer ;
		}

		$zig_result['return'] = 1 ;			
		return $zig_result ;
	}

	function fields_total($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$sorted_fields = $arg1 ;
			$fields_computed_total = $arg2 ;
		}
		else if(is_array($parameters))
		{
			$sorted_fields = array_key_exists("sorted_fields",$parameters) ? $parameters['sorted_fields'] : NULL ;
		}
		$buffer_total_flag = false ;
		$buffer_total = "" ;

		foreach($sorted_fields as $fetch)
		{
			$total_template = zig("template","block","fields","field total cell") ;
			$total_template = str_replace("{field_name}",$fetch['Field'],$total_template) ;
			if($fetch['Type']=="double")
			{
				$buffer_total_flag = true ;
			}
			$buffer_total.= str_replace("{zig_field_total_cell}","&nbsp;",$total_template) ;
		}
		if($buffer_total_flag)
		{
			$total_template = zig("template","block","fields","field total table") ;
			$buffer_total = str_replace("{zig_field_total_content}",$buffer_total,$total_template) ;
			$zig_result = $buffer_total ;
			return $zig_result ;			
		}
	}

	function fields_details($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$sorted_fields = $arg1 ;
			$fetch = $arg2 ;
			$exclude = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$sorted_fields = array_key_exists("sorted_fields",$parameters) ? $parameters['sorted_fields'] : NULL ;
			$fetch = array_key_exists("fetch",$parameters) ? $parameters['fetch'] : NULL ;
			$exclude = array_key_exists("exclude",$parameters) ? $parameters['exclude'] : NULL ;
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
		}

		foreach($sorted_fields as $fetch)
		{
			if(!in_array($fetch['Field'],$exclude))
			{
				$details_exclude[] = $fetch['Field'] ;
			}
		}

		$details_parameters = array
		(
			'function'	=>	'fields',
			'method'	=>	'parent',
			'table'		=>	$table,
			'exclude'	=>	$details_exclude,
			'id'		=>	$id,
			'mode'		=>	'view',
		) ;
	
		$details_content = zig($details_parameters) ;
		$buffer = zig("fieldset",$details_content,"Details",true) ;
		$zig_result = $buffer ;
		return $zig_result ;
	}

	function fields_revisions($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$stripped_table = $arg1 ;
			$id = $arg2 ;
		}
		else if(is_array($parameters))
		{
			$stripped_table = array_key_exists("stripped_table",$parameters) ? $parameters['stripped_table'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
		}

		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$semi_stripped_table = $pre.$stripped_table ;
		$table = $zig_global_database.".".$semi_stripped_table ;
		$parent_id = $id ;
		$revision_tables[$stripped_table] = "Main Data" ;
		$revisions_overall_count = $fieldset_with_revisions = 0 ;
		$revisions_stack_buffer_single_content = $revisions_stack_buffer = $where_id = "" ;

		$relationship_sql = "SELECT `child_table`,`fieldset`,`description` FROM `zig_relationships` WHERE (`parent_table`='$table' OR `parent_table`='$semi_stripped_table' OR `parent_table`='$stripped_table' OR `parent_table`='all tables') AND `child_table`<>'' AND `zig_status`<>'deleted' ORDER BY `zig_weight`,`fieldset`,`child_table`" ;
		$relationship_result = zig("query",$relationship_sql) ;
		while($relationship_fetch=$relationship_result->fetchRow())
		{
			$revision_tables[$relationship_fetch['child_table']] = $relationship_fetch['fieldset'] ? $relationship_fetch['fieldset'] : ucwords(trim(str_replace("_"," ",$relationship_fetch['child_table']))) ;
		}

		foreach($revision_tables as $revision_table => $fieldset_title)
		{
			// Start remove the database name on the table
			$semi_stripped_revision_table = str_replace($zig_global_database.".","",$revision_table) ;
			// End remove the database name on the table

			// Start stripped table name
			$stripped_revision_table = str_replace($pre,"",$semi_stripped_revision_table) ;
			// End stripped table name

			if($revision_table<>$stripped_table)
			{
				$child_table_sql = "SELECT `id` FROM `${revision_table}` WHERE `zig_parent_id`='${parent_id}'" ;
				$child_table_result = zig("query",$child_table_sql) ;
				if($child_table_result->RecordCount())
				{
					while($child_table_fetch=$child_table_result->fetchRow())
					{
						$where_id = $where_id ? $where_id." OR `row_id`='$child_table_fetch[id]' " : " `row_id`='$child_table_fetch[id]' " ;
					}
					$where_id = " AND ( ${where_id} ) " ;
				}
			}
			else
			{
				$where_id = " AND `row_id`='${parent_id}' " ;
			}

			$semi_stripped_revision_table = "${pre}revision_table" ;
			$revision_table = "${zig_global_database}.${pre}${revision_table}" ;

			if($where_id)
			{
				$revisions_sql = "SELECT `zig_updated`,`zig_user`,`info` 
					FROM `zig_revisions` 
					WHERE ((`table_name`='${revision_table}' OR `table_name`='${semi_stripped_revision_table}' OR `table_name`='${stripped_revision_table}') 
					OR (`table_name` IN (SELECT CONCAT('${zig_global_database}.${pre}',`table_source_name`) FROM `${zig_global_database}`.`${pre}table_views` WHERE `table_view_name`='${revision_table}' OR `table_view_name`='${semi_stripped_revision_table}' OR `table_view_name`='${stripped_revision_table}')) 
					OR (`table_name` IN (SELECT CONCAT('${pre}',`table_source_name`) FROM `${zig_global_database}`.`${pre}table_views` WHERE `table_view_name`='${revision_table}' OR `table_view_name`='${semi_stripped_revision_table}' OR `table_view_name`='${stripped_revision_table}')) 
					OR (`table_name` IN (SELECT `table_source_name` FROM `${zig_global_database}`.`${pre}table_views` WHERE `table_view_name`='${revision_table}' OR `table_view_name`='${semi_stripped_revision_table}' OR `table_view_name`='${stripped_revision_table}'))) 
					${where_id} 
					ORDER BY `zig_updated` DESC" ;
				$revisions_result = zig("query",$revisions_sql) ;
				$revisions_count = $revisions_result->RecordCount() ;
				$revisions_overall_count+= $revisions_count ;
				$revisions_parameters = array
				(
					'function'		=>	'listing',
					'sql'			=>	$revisions_sql,
					'unserialize'	=>	'info',
					'print_view'	=>	true,
					"addLink"		=>	false
				) ;
				$revisions_listing = zig($revisions_parameters) ;
				$where_id = "" ;
				if($revisions_listing)
				{
					$revisions_stack_template = zig("fieldset",$revisions_listing['value'],$fieldset_title." (".$revisions_count.")",true) ;
					$revisions_stack_buffer.= str_replace("{content}",$revisions_listing,$revisions_stack_template) ;
					$revisions_stack_buffer_single_content = $revisions_listing ;
					$fieldset_with_revisions++ ;
				}
			}
		}

		$revisions_stack_buffer = $fieldset_with_revisions>1 ? $revisions_stack_buffer : $revisions_stack_buffer_single_content ;
		if($revisions_stack_buffer)
		{
			$buffer = zig("fieldset",$revisions_stack_buffer,"History ($revisions_overall_count)",true) ;
		}
		else
		{
			$revisions_fieldset = array
			(
			 	"function"		=>	"fieldset",
				"name"			=>	"History (0)",
				"collapsed"		=>	true,
				"collapsible"	=>	false
			) ;
			$buffer = zig($revisions_fieldset) ;
		}

		$zig_result = $buffer ;
		return $zig_result ;
	}
}

?>