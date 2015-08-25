<?php

class zig_validate {
	function validate($parameters,$arg1='',$arg2='',$arg3='') {
		$mode = "add" ;
		if($arg1 or $arg2 or $arg3)
		{
			$column_info = $arg1 ;
			$field_value = $arg2 ;
			$table = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$column_info = array_key_exists("column_info",$parameters) ? $parameters['column_info'] : NULL ;
			$field_value = array_key_exists("field_value",$parameters) ? $parameters['field_value'] : NULL ;
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$field_info = array_key_exists("field_info",$parameters) ? $parameters['field_info'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : NULL ;
			$mode = array_key_exists("mode",$parameters) ? $parameters['mode'] : $mode ;
		}

		$return_values['validation'] = $validation = true ;
		$return_values['message'] = $exclude_self = "" ;

		foreach($column_info as $key => $value) {
			$field_info_fetch = array_key_exists($key, $field_info) ? $field_info[$key] : array() ;
			$field_label = array_key_exists("field_label", $field_info_fetch) ? 
							$field_info_fetch['field_label'] : ucwords(trim(str_replace("_"," ",$key))) ;
				
			// -- Start get field max size
			$splitted_type = explode(" ",$value['Type']) ;
			$attribute = isset($splitted_type[1]) ? $splitted_type[1] : '' ;
			$splitted_type[0] = str_replace("("," ",$splitted_type[0]) ;
			$splitted_size = explode(" ",$splitted_type[0]) ;
			$size = isset($splitted_size[1]) ? str_replace(")","",$splitted_size[1]) : '' ;
			$type = $splitted_size[0] ;
			// -- End get field max size

			if($field_value[$key]<>"") {
				switch($type) {
					case "int":
						$validation = is_numeric($field_value[$key]) ? true : false ;
						if($validation)
						{
							$splitted_field_value = explode("\.",$field_value[$key]) ;
							$validation = array_key_exists(1,$splitted_field_value) ? false : true ;
						}
						$return_values['message'].= $validation ? 
							"" : "ERROR: $field_label \"".$field_value[$key]."\" should be an integer<br />" ;
						break ;
				
					case "float":
						$validation = is_numeric($field_value[$key]) ? true : false ;
						$return_values['message'].= $validation ? 
							"" : "ERROR: $field_label \"".$field_value[$key]."\" should be numeric<br />" ;
						break ;

					case "double":
						$validation = is_numeric($field_value[$key]) ? true : false ;
						$return_values['message'].= $validation ? 
							"" : "ERROR: $field_label \"".$field_value[$key]."\" should be numeric<br />" ;
						break ;
				 
				    case "tinytext":
		                $size = 255 ;
			  		    break ;
					
					case "text":
					    $size = 65535 ;
			     		break ;
					
					case "blob":
						$size = 65535;
						break ;	
											
					case "mediumtext":
						$size = 16777215 ;
		                break ;		
					
					case "mediumblob":
						$size = 16777215 ;
						break ;		
						
				    case "longtext":
						$size = 4294967295 ;
			            break ;		
						
					case "longblob":
						$size = 4294967295 ;
						break ;
							
					case "date":
						$reformatted_date = zig("datetime",$field_value[$key],"Y-m-d") ;
						list($year,$month,$day) = explode("-",$reformatted_date) ;
						$validation = checkdate($month,$day,$year) ? true : false ;
						$return_values['message'].= $validation ? 
							NULL : "ERROR: $field_label \"".$field_value[$key]."\" invalid date<br />" ;
						break ;

					case "enum":
						$size = 0 ;
						break ;
				}
			}

			// -- Start unique field check
			if($value['Key']=="UNI" or (zig("checkArray",$field_info_fetch,"validation")=="unique+blank" and $field_value[$key]<>""))
			{
				$current_field_value = $field_value[$key] ;
				if($id) {
					$exclude_self = " AND id<>'$id' " ;
				}
				$sql = "SELECT ${key} 
						FROM ${table} 
						WHERE $key='$current_field_value' ${exclude_self} 
						LIMIT 1" ;
				$result = zig("query",$sql) ;
				if($result<>"")
				{
					$row_total = $result->RecordCount() ;
					if($row_total)
					{
						$validation*= false ;
						$return_values['message'].= "ERROR: $field_label \"".$current_field_value."\" already exists<br />" ;
					}
				}
			}
			// -- End unique field check

			// -- Start null field check
			if($value['Null']=="NO" and ($field_value[$key]=="") and zig("checkArray",$field_info_fetch,"attribute")<>"readonly") {
				$validation*= false ;
				$return_values['message'].= "ERROR: \"".$field_label."\" must not be blank<br />" ;
			}
			// -- End null field check
			
			// -- Start field attribute check
			if($attribute=="unsigned" and $field_value[$key]<0)
			{
				$validation*= false ;
				$return_values['message'].= "ERROR: $field_label \"".$field_value[$key]."\" must not be a negative value<br />" ;
			}
			// -- End field attribute check

			// -- Start check length check
			if($size < strlen($field_value[$key]) and $size)
			{
				$validation*= false ;
				$return_values['message'].= "ERROR: $field_label \"".$field_value[$key]."\" too long, max length is [$size]<br />" ;
			}
			// -- End check length check

			// -- Start check data type
			if(!preg_match("/[a-z]/i",$field_value[$key]) and zig("checkArray",$field_info_fetch,"data_type")=="alphabet")
			{
				$validation*= false ;
				$return_values['message'].= "ERROR: $field_label \"".$field_value[$key]."\" must be within the alphabet<br />" ;
			}
			else if(!preg_match("/[a-z][0-1]/i",$field_value[$key]) and zig("checkArray",$field_info_fetch,"data_type")=="alphanumeric")
			{
				$validation*= false ;
				$return_values['message'].= "ERROR: $field_label \"".$field_value[$key]."\" must be alphanumeric<br />" ;
			}
			else if(is_numeric($field_value[$key]) and zig("checkArray",$field_info_fetch,"data_type")=="textnumeric")
			{
				$validation*= false ;				
				$return_values['message'].= "ERROR: $field_label \"".$field_value[$key]."\"must be numeric<br />" ;
			}
			// -- End check data type

			$return_values['invalid_fields'][$key] = $validation ? false : true  ;
			$return_values['validation']*= $validation ? true : false  ;
		}

		// -- Start Custom Validation
		$customValidationParameters = array(
			"function"		=> "customField",
			"module"		=> $module,
			"table"			=> $table,
			"method"		=> "validation",
			"fieldValues"	=> $field_value
		) ;
		$customValidation = zig($customValidationParameters) ;
		switch(array_key_exists("validation", $customValidation)) {
			case true: {
				
				$return_values['validation']*= $customValidation['validation'] ;
				$return_values['message'].= $customValidation['message'] ;
			}
		}
		// -- End Custom Validation

		$return_values['message'] = $return_values['validation'] ? 
										$return_values['message'] : 
										($return_values['message'] ? 
											"Unable to save data! See errors below<br />".$return_values['message'] : 
												$return_values['message']) ;
		$zig_return['value'] = $return_values ;
		$zig_return['return'] = 1 ;
		return $zig_return ;
	}
	
}

?>