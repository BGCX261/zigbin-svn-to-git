<?php

class zig_field_element {
	function field_element($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL) {
		$table = "" ;
		if($arg1 or $arg2 or $arg3) {
			$mode = $arg1 ;
			$field_type = $arg2 ;
			$field_value = $arg3 ;
		}
		if(is_array($parameters)) {
			$mode = array_key_exists("mode",$parameters) ? $parameters['mode'] : NULL ;
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : $table ;
			$dbDefinedField = array_key_exists("dbDefinedField",$parameters) ? $parameters['dbDefinedField'] : NULL ;
			$userDefinedField = array_key_exists("userDefinedField",$parameters) ? $parameters['userDefinedField'] : array() ;
			$field_value = array_key_exists("field_value",$parameters) ? $parameters['field_value'] : NULL ;
			$attribute_script = array_key_exists("elementAttributes",$parameters) ? $parameters['elementAttributes'] : zig("checkArray",$userDefinedField,"elementAttributes") ;
			$field_attribute = array_key_exists("field_attribute",$parameters) ? $parameters['field_attribute'] : zig("checkArray",$userDefinedField,"attribute") ;
			$field_type = array_key_exists("field_type",$parameters) ? $parameters['field_type'] : zig("checkArray",$dbDefinedField,"Type") ;
			$field_name = array_key_exists("field_name",$parameters) ? $parameters['field_name'] : zig("checkArray",$dbDefinedField,"Field") ;
			$current_field_name = array_key_exists("current_field_name",$parameters) ? $parameters['current_field_name'] : $field_name ;
			$defaultValue = array_key_exists("defaultValue",$parameters) ? $parameters['defaultValue'] : 
								(array_key_exists("defaultValue", $userDefinedField) ? $userDefinedField['defaultValue'] :zig("checkArray",$dbDefinedField,"Default")) ;
			$userDefinedFieldType = array_key_exists("userDefinedFieldType",$parameters) ? $parameters['userDefinedFieldType'] : zig("checkArray",$userDefinedField,"field_type") ;
			$parent_value = array_key_exists("parent_value",$parameters) ? $parameters['parent_value'] : NULL ;
			$fieldDescription = zig("checkArray",$userDefinedField,"description") ;
		}

		$field_data_size = zig("checkArray",$dbDefinedField,"Type") ? zig("data_size",$dbDefinedField['Type']) : 100 ;
		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$semi_stripped_table = str_replace($zig_global_database.".","",$table) ;
		$stripped_table = str_replace($pre,"",$semi_stripped_table) ;

		$distinct_records_parameters = array(
			'function'	=>	"droplist",
			'method'	=>	"suggest",
			'sql'		=>	"SELECT DISTINCT `$dbDefinedField[Field]` FROM ${table} ORDER BY `$dbDefinedField[Field]`",
			'value'		=>	$dbDefinedField['Field'],
			'default'	=>	$defaultValue,
			'name'		=>	$current_field_name,
			'table'		=>	$stripped_table,
			'mode'		=>	$mode
		) ;

		switch($userDefinedFieldType) {
			case "computed":
			case "file":
			case "password":
			case "readonly":
			case "select":
			case "suggest":
			case "textarea":
			case "virtual": {
				$field_type = $userDefinedFieldType ;
				break ;
			}
			default: {
				if(strpos($field_type,"enum(")!==false) {
					$enumerated_values = substr($field_type,5,(strlen($field_type)-6)) ;
					$field_type = "enum" ;
				}
			}
		}
		$buffer = "" ;
		switch($field_type) {
			case "computed":
			case "readonly":
			case "virtual": {
				switch($mode) {
					case "add":	{
						$buffer = $defaultValue ;
						break ;
					}
					case "edit":
					case "view": {
						$buffer = $field_value ;
						break ;
					}
				}
				break ;	
			}
			case "double": {
				switch($mode) {
					case "add": {
						$buffer = $this->suggest($table,$dbDefinedField['Field']) ? zig($distinct_records_parameters) : 
							zig("template","block","field_element","input add") ;
						break ;
					}
					case "edit": {
						$buffer = $this->suggest($table,$dbDefinedField['Field']) ? zig($distinct_records_parameters) : 
									zig("template","block","field_element","input edit") ;
						$buffer = str_replace("{escaped_data}",number_format($field_value,2),$buffer) ;
						break ;
					}
					case "view": {
						$buffer = number_format($field_value,2) ;
						break ;
					}
				}
				break ;
			}
			case "file":
			case "thumbnail": {
				$fileLinks = "" ;
				$zig_decode_hash = "" ;
				switch($mode) {
					case "add": {
						$buffer = zig("template","block","field_element","file add") ;
						break ;
					}
					case "edit":
					case "view": {
						$thumbnail_view = false ;
						if($field_value<>"") {
							$zig_files_path = zig("config","files path") ;
							$zig_filename = $zig_files_path.$field_value ;
							$zig_new_filename = $field_value ;
							$zig_decode_hash = "get_file,".$zig_filename.",".$zig_new_filename ;
							$width = $height = $preview_width = $preview_height = "" ;
							if($field_attribute=="thumbnail") {
								$splitted_value = explode(".",$field_value) ;
								$file_extension = strtolower($splitted_value[sizeof($splitted_value)-1]) ;
								$field_size = zig("config","thumbnail size") ;
								if(stripos($field_size,"x"))
								{
									$splitted_size = explode("x",strtolower($field_size)) ;
									$thumbnail_width = str_replace("px","",$splitted_size[0]) ;
									$thumbnail_height = str_replace("px","",$splitted_size[1]) ;
									$image_size = zig("cache","file_exists",$zig_filename) ? getimagesize($zig_filename) : NULL ;
									$original_width = $image_size[0] ;
									$original_height = $image_size[1] ;
									$difference_width = $original_width - $thumbnail_width ;
									$difference_width = $difference_width<0 ? $difference_width * -1 : $difference_width ;
									$difference_width = $original_width ? $difference_width / $original_width : 0 ;
									$difference_height = $original_height - $thumbnail_height ;
									$difference_height = $difference_height<0 ? $difference_height * -1 : $difference_height ;
									$difference_height = $original_height ? $difference_height / $original_height : 0 ;
									if($original_width>=$thumbnail_width and $original_height>=$thumbnail_height) // -- start reduce
									{
										if($difference_width>=$difference_height) {
											$width = $original_width - ($original_width * $difference_width) ;
											$height = $original_height - ($original_height * $difference_width) ;
										}
										else {
											$width = $original_width - ($original_width * $difference_height) ;
											$height = $original_height - ($original_height * $difference_height) ;
										}
									}
									else // -- start expand
									{
										if($difference_width>=$difference_height) {
											$width = $original_width + ($original_width * $difference_width) ;
											$height = $original_height + ($original_height * $difference_width) ;
										}
										else {
											$width = $original_width + ($original_width * $difference_height) ;
											$height = $original_height + ($original_height * $difference_height) ;
										}
									}
									$preview_width = $width * 3 ;
									$preview_height = $height * 3 ;
									$width = ($width>0 and $width<$original_width) ? "width='${width}'" : NULL ;
									$height = ($height>0 and $height<$original_height) ? "height='${height}'" : NULL ;
									$preview_width = ($preview_width>0 and $preview_width<$original_width) ? "width='${preview_width}'" : NULL ;
									$preview_height = ($preview_height>0 and $preview_height<$original_height) ? "height='${preview_height}'" : NULL ;
								}
								$image_extensions = zig("config","image extension") ;
								$thumbnail_view = is_array($image_extensions) ? (in_array($file_extension,$image_extensions) ? true : false) : (strcmp($image_extensions,$file_extension) ? true : false) ;
							}
	
							$zig_decode_hash.= $thumbnail_view ? ",view" : ",download" ;
							$zig_decode_hash = zig("hash","encrypt",$zig_decode_hash) ;
							switch($thumbnail_view) {
								case true: {
									switch($field_type) {
										case "thumbnail":
										{
											$buffer = zig("template","block","field_element","thumbnail") ;
											break ;
										}
										default:
										{
											$buffer = zig("template","block","field_element","thumbnail view") ;
										}
									}
									break ;
								}
								default: {
									$buffer = zig("template","block","field_element","file view") ;
								}
							}
							$buffer = str_replace("{width}",$width,$buffer) ;
							$buffer = str_replace("{height}",$height,$buffer) ;
							$buffer = str_replace("{preview_width}",$preview_width,$buffer) ;
							$buffer = str_replace("{preview_height}",$preview_height,$buffer) ;
						}
						else {
							$buffer = zig("template","block","field_element","input view blank") ;
						}
						if($field_type=="thumbnail") {
							break ;
						}
						switch($mode) {
							case "edit": {
								switch($thumbnail_view)
								{
									case true:
									{
										$buffer.= zig("template","block","field_element","thumbnail edit") ;
										switch($field_value<>"")
										{
											case true:
											{
												$fileLinks = zig("template","block","field_element","thumbnailLinks") ;
											}
										}
										break ;
									}
									default: {
										$buffer.= zig("template","block","field_element","file edit") ;
										switch($field_value<>"")
										{
											case true:
											{
												$fileLinks = zig("template","block","field_element","fileLinks") ;
											}
										}
									}
								}
								break ;
							}
						}
						break ;
					}
				}
				break ;
			}
			case "password": {
				switch($mode) {
					case "add": {
						$buffer = zig("template","block","field_element","password add") ;
						break ;
					}
					case "edit": {
						$buffer = zig("template","block","field_element","password edit") ;
						break ;
					}
					case "view": {
						$buffer = zig("template","block","field_element","password view") ;
						break ;
					}
				}
				break ;
			}
			case "enum": {
				switch($mode) {
					case "add": {
						$enumerated_values = str_replace("'","",$enumerated_values) ;
						$select_options = explode(",",$enumerated_values) ;
						if(is_array($select_options)) {
							$default_droplist_value = in_array($field_value,$select_options) ? $field_value : $defaultValue ;
							switch($dbDefinedField['Null']) {
								case "YES": {
									$select_options_buffer = "<option value=''></option>" ;
									break ;
								}
								default:
								{
									$select_options_buffer = "" ;
								}
							}
							foreach($select_options as $option)
							{
								$select_options_buffer.= $default_droplist_value==$option ? "<option value='${option}' selected='selected'>${option}</option>" : "<option value='${option}'>${option}</option>" ;
							}
						}
						$buffer.= zig("template","block","field_element","select add") ;
						$buffer = str_replace("{select_options_buffer}",$select_options_buffer,$buffer) ;						
						break ;
					}
					case "edit": {
						$enumerated_values = str_replace("'","",$enumerated_values) ;
						$select_options = explode(",",$enumerated_values) ;
						if(is_array($select_options)) {
							$default_droplist_value = in_array($field_value,$select_options) ? $field_value : $defaultValue ;
							switch($dbDefinedField['Null']) {
								case "YES": {
									$select_options_buffer = "<option value=''></option>" ;
									break ;
								}
								default: {
									$select_options_buffer = "" ;
								}
							}
							foreach($select_options as $option) {
								$select_options_buffer.= $default_droplist_value==$option ? "<option value='${option}' selected='selected'>${option}</option>" : "<option value='${option}'>${option}</option>" ;
							}
						}
						$buffer = zig("template","block","field_element","select edit") ;
						$buffer = str_replace("{select_options_buffer}",$select_options_buffer,$buffer) ;
						break ;
					}
					case "view": {
						$buffer = $field_value ;
						break ;
					}
				}
				break ;
			}
			case "select": {
				$select_sql =  $userDefinedField['sql'] ;
				switch($mode) {
					case "add": {
						switch($select_sql<>"") {
							case true: {
								$droplist_parameters = array
								(
									'function'	=>	"droplist",
									'sql'		=>	$select_sql,
									'value'		=>	$userDefinedField['option_value'],
									'default'	=>	$defaultValue,
									'name'		=>	$current_field_name,
									'table'		=>	$stripped_table,
									'method'	=>	"selectOptions"
								) ;
								$droplist = zig($droplist_parameters) ;
							}
						}
						$buffer = zig("template","block","field_element","select add") ;
						$buffer = str_replace("{select_options_buffer}",$droplist,$buffer) ;
						break ;
					}
					case "edit": {
						switch($select_sql<>"") {
							case true: {
								$droplist_parameters = array(
									'function'	=>	"droplist",
									'sql'		=>	$select_sql,
									'value'		=>	$userDefinedField['option_value'],
									'default'	=>	$field_value,
									'name'		=>	$current_field_name,
									'table'		=>	$stripped_table,
									'method'	=>	"selectOptions"
								) ;
								$droplist = zig($droplist_parameters) ;
								break ;
							}
						}
						$buffer = zig("template","block","field_element","select edit") ;
						$buffer = str_replace("{select_options_buffer}",$droplist,$buffer) ;
						break ;
					}
					case "view": {
						switch($select_sql<>"") {
							case true: {
								$droplist_parameters = array(
									'function'	=>	"droplist",
									'method'	=>	"selected_label",
									'sql'		=>	$select_sql,
									'value'		=>	$userDefinedField['option_value'],
									'default'	=>	$field_value,
									'name'		=>	$current_field_name,
									'table'		=>	$stripped_table
								) ;
								$buffer = zig($droplist_parameters) ;
								break ;
							}
						}
						break ;
					}
				}
				break ;
			}
			case "tinyint(1)": {
				switch($field_value) {
					case NULL:
					case 0:
					case false: {
						$buffer = zig("template","block","field_element","checkbox unchecked ${mode}") ;
						break ;
					}
					default: {
						$buffer = str_replace("{src}",zig("images","16x16/actions/checked.png"), 
									zig("template","block","field_element","checkbox checked ${mode}")) ;
					}
				}
				break ;
			}
			case "text":
			case "textarea":
			case "varchar(255)": {
				switch($mode) {
					case "add": {
						$buffer = zig("template","block","field_element","text add") ;
						break ;
					}
					case "edit": {
						$buffer = zig("template","block","field_element","text edit") ;
						break ;
					}
					case "view": {
						$buffer = $field_value ;
						break ;
					}
				}
				$attribute_script = $this->countdown("countdown",$attribute_script,$current_field_name,$field_data_size) ;
				break ;
			}
			case "date":
			case "datetime":
			case "time":
			case "timestamp": {
				switch($field_attribute) {
					case "timezoned": {
						$timezone = zig("config","timezone offset") ;
						$timezone_adjustment = $timezone ? "+ ${timezone} hours" : "" ;
						$datetime_info = strtotime($field_value.$timezone_adjustment) ;
						$field_value = date("Y-m-d H:i:s",$datetime_info) ;
						break ;
					}
				}
				$field_value = zig("datetime",$field_value) ;
				switch($mode) {
					case "add": {
						switch($field_attribute) {
							case "timezoned": {
								$datetime_info = strtotime($defaultValue.$timezone_adjustment) ;
								$defaultValue = date("Y-m-d H:i:s",$datetime_info) ;
								break ;
							}
						}
						$buffer = zig("template","block","field_element","date add") ;
						break ;
					}
					case "edit": {
						$buffer = zig("template","block","field_element","date edit") ;
						$buffer = str_replace("{escaped_data}",$field_value,$buffer) ;
						break ;
					}
					case "view": {
						$buffer = $field_value ;
						break ;
					}
				}
				$buffer = str_replace("{date_format}",zig("config","date format"),$buffer) ;
				break ;
			}
			default: {
				switch($mode) {
					case "add": {
						$buffer = $this->suggest($table,$dbDefinedField['Field']) ? zig($distinct_records_parameters) : 
									zig("template","block","field_element","input add") ;
						break ;
					}
					case "edit": {
						$buffer = $this->suggest($table,$dbDefinedField['Field']) ? zig($distinct_records_parameters) : 
									zig("template","block","field_element","input edit") ;
						break ;
					}
					case "view": {
						$buffer = $field_value ;
						break ;
					}
				}
				switch(substr($field_type, 0,3)=="int") {
					case false: {
						$attribute_script = $this->countdown("countdown",$attribute_script,$current_field_name,$field_data_size) ;						
					}
				}
				break ;
			}
		}

		$buffer = str_replace("{field_data_size}",$field_data_size,$buffer) ;
		$buffer = str_replace("{attribute_script}",$attribute_script,$buffer) ;
		$buffer = str_replace("{fieldDescription}",$fieldDescription,$buffer) ;
		switch($mode) {
			case "add":
			{
				// -- start required flag
				$buffer = $dbDefinedField['Null']=="NO" ? 
							str_replace("{requiredFlag}",zig("template","block","field_element","requiredFlag"),$buffer) : 
							str_replace("{requiredFlag}","",$buffer) ;
				// -- end required flag
				break ;
			}
			case "edit": {
				// -- start required flag
				$buffer = $dbDefinedField['Null']=="NO" ? 
							str_replace("{requiredFlag}",zig("template","block","field_element","requiredFlag"),$buffer) : 
							str_replace("{requiredFlag}","",$buffer) ;
				// -- end required flag

				// -- start field element div
				$buffer = str_replace("{fieldElement}",$buffer,zig("template","block","field_element","fieldDiv edit")) ;
				// -- end field element div
				break ;
			}
		}

		switch($field_type) {
			case "file": {
				$buffer.= $fileLinks.zig("template","block","field_element","uploadLinks") ;
			}
			case "thumbnail": {
				$buffer = str_replace("{zig_decode_hash}",$zig_decode_hash,$buffer) ;
			}
		}

		$zig_result['value'] = $buffer ;
		$zig_result['return'] = 1 ;

		return $zig_result ;
	}

	function suggest($table,$field) {
		$total_records = zig("select_count",$table,"id") ;
		$distinct_records = zig("select_count",$table,$field,true) ;
		if($distinct_records<($total_records*0.30) and $distinct_records and $distinct_records<100) {
			return true ;
		}
		else {
			return false ;
		}
	}

	function countdown($parameters,$arg1='',$arg2='',$arg3='') {
		if($arg1 or $arg2 or $arg3) {
			$attribute_script = $arg1 ;
			$current_field_name = $arg2 ;
			$field_data_size = $arg3 ;
		}

		if($attribute_script=="") {
			$attribute_script = "onkeyup='countdown(\"zig_field_countdown_".$current_field_name."_{uniqueString}\",this.id,\"${field_data_size}\",\"".zig("config","countdown tolerance")."\",\"".zig("config","countdown minimum")."\") ;'" ;
		}
		else if(strpos($attribute_script,"onkeyup=")===false) {
			$attribute_script.= " onkeyup='countdown(\"zig_field_countdown_".$current_field_name."_{uniqueString}\",this.id,\"${field_data_size}\",\"".zig("config","countdown tolerance")."\",\"".zig("config","countdown minimum")."\") ;'" ;
		}
		else {
			$attribute_script = str_replace("onkeyup='","onkeyup='countdown(\"zig_field_countdown_".$current_field_name."_{uniqueString}\",this.id,\"${field_data_size}\",\"".zig("config","countdown tolerance")."','".zig("config","countdown minimum")."\") ; ",$attribute_script) ;
		}

		$zig_result = $attribute_script ;
		return $zig_result ;
	}
}

?>