<?php

class zig_droplist {
	function droplist($parameters,$arg1='',$arg2='',$arg3='') {
		$class = array_key_exists('class',$parameters) ? $parameters['class'] : "zig_droplist" ;
		$table = array_key_exists('table',$parameters) ? $parameters['table'] : NULL ;
		$filter = array_key_exists('filter',$parameters) ? $parameters['filter'] : NULL ;
		$droplist_filter = array_key_exists('droplist_filter',$parameters) ? $parameters['droplist_filter'] : false ;
		$exclude = array_key_exists('exclude',$parameters) ? $parameters['exclude'] : NULL ;
		$list_limit = array_key_exists('limit',$parameters) ? $parameters['list_limit'] : zig("config","list_limit") ;
		$sqlIndex = $index = array_key_exists('value',$parameters) ? $parameters['value'] : NULL ;
		$default = array_key_exists('default',$parameters) ? $parameters['default'] : NULL ;
		$name = array_key_exists('name',$parameters) ? $parameters['name'] : NULL ;
		$method = array_key_exists('method',$parameters) ? $parameters['method'] : NULL ;
		$mode = array_key_exists('mode',$parameters) ? $parameters['mode'] : "add" ;

		$explodedIndex = explode(".", $index) ;
		$index = $explodedIndex[count($explodedIndex)-1] ;
		$index = str_replace("`", "", $index) ;
		$zig_pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$sql = $parameters['sql'] ;
		$sql = str_replace("$"."{pre}",$zig_pre,$sql) ;
		$sql = str_replace("$"."{zig_global_database}",$zig_global_database,$sql) ;
		$parameters['sql'] = $sql ;
		$filter_name = "zig_droplist_filter_".$name ;
		$filter_selected = isset($_GET[$filter_name]) ? $_GET[$filter_name] : (isset($_POST[$filter_name]) ? $_POST[$filter_name] : '') ;
		$droplist = "" ;
		$droplist_array = "" ;
		$droplist_options = "" ;

		if($method=="selected")
		{
			$zig_result['value'] = $this->selected($parameters) ;
		}
		else
		{
			$result = zig("query",$sql,"",false) ;
			$record_count = $result->RecordCount() ;

			if($result<>"") {
				$result->MoveFirst() ;
				$ripped_fields = zig("fetchcol",$result) ;
				if(!in_array($index,$ripped_fields))
				{
					if(strtolower(substr($sql,0,15))=="select distinct") {
						$sql = "select distinct ${sqlIndex},".substr($sql,16) ;
					}
					else {
						$sql = "select ${sqlIndex},".substr($sql,7) ;
					}
					$result = zig("query",$sql) ;
					$ripped_fields = zig("fetchcol",$result) ;
					if(is_array($exclude)) {
						$exclude[] = $index ;
					}
					else if($exclude) {
						$exclude = array
						(
							$exclude,
							$index
						) ;
					}
					else
					{
						$exclude = $index ;
					}
				}

				$parameters['no_blank'] = zig("checkArray",$parameters,"no_blank","arrayKeyExists") ? $parameters['no_blank'] : zig("config","no_blank") ;
				$separator = zig("checkArray",$parameters,"separator","arrayKeyExists") ? $parameters['separator'] : zig("config","separator") ;
				if(strpos($separator,",")) {
					$separator = explode(",",$separator) ;
					$end_separator = end($separator) ;
					reset($separator) ;
				}
				$fields_count = count($ripped_fields) ;
				if($fields_count>1)
				{
					while($fetch=$result->fetchRow())
					{
						foreach($ripped_fields as $value)
						{
							if($value==$exclude)
							{
								continue ;
							}
							else if(is_array($exclude))
							{
								if(in_array($value,$exclude))
								{
									continue ;
								}
							}
							if(strtolower(substr($fetch[$value],0,1))<>$filter and $filter)
							{
								$filter_break = true ;
								break ;
							}
							else
							{
								break ;
							}
						}
						if(isset($filter_break))
						{
							unset($filter_break) ;
							continue ;
						}
						$count = 0 ;
						$value = $fetch[$index] ;
						$droplist.= ($value == $default and $value<>"") ? "<option value='$value' selected='selected'>" : "<option value='$value'>" ;
						$droplist_label = "" ;
						foreach($ripped_fields as $value) {
							$count++ ;
							if($value==$exclude)
							{
								continue ;
							}
							else if(is_array($exclude))
							{
								if(in_array($value,$exclude))
								{
									continue ;
								}
							}
							$droplist.= $fetch[$value] ;
							$droplist_label.= $fetch[$value] ;
							$droplist_options.= $droplist_options ? ",".$fetch[$value] : $fetch[$value] ;
							if($fields_count<>$count)
							{
								if(is_array($separator))
								{
									$droplist.= current($separator) ;
									$droplist_label.= current($separator) ;
									$droplist_options.= current($separator) ;
									if($end_separator!=current($separator))
									{
										next($separator) ;
									}
								}
								else
								{
									$droplist.= $separator ;
									$droplist_label.= $separator ;
									$droplist_options.= $separator ;
								}
							}
						}
						$droplist.= "</option>" ;
						$droplist_array[$fetch[$index]] = $droplist_label ;
						if(is_array($separator)) {
							reset($separator) ;
						}
					}
				}
			else
			{
				$optionTemplate = zig("template","block","droplist","suggest option") ;
				$single_field = isset($ripped_fields[0]) ? $ripped_fields[0] : false ;
				while($fetch=$result->fetchRow())
				{
					if(strtolower(substr($fetch[$single_field],0,1))<>$filter and $filter)
					{
						continue ;
					}
					$value = $fetch[$index] ;
					$droplist.= $method=="suggest" ? str_replace("{optionValue}",$fetch[$single_field],$optionTemplate) : "<option value='${value}'>$fetch[$single_field]</option>" ;
					$droplist_options.= $droplist_options ? ",".$fetch[$single_field] : $fetch[$single_field] ;
					$droplist_array[$fetch[$single_field]] = $fetch[$single_field] ;
				}
			}
			if($droplist and !$parameters['no_blank'] and $method<>"suggest")
			{
				$droplist = "<option></option>".$droplist ;
				$droplist_options = ",".$droplist_options ;
			}
		}
		else
		{
			$zig_result['error'] = $GLOBALS['zig']['obj']['error']->error(106) ;
		}

		if(!stripos($droplist,"selected='selected'"))
		{
			if($default and is_array($parameters))
			{
				$parameters['droplist']  = $droplist ;
				$selected_return = $this->search_selected($parameters) ;
				$droplist = $selected_return['buffer'] ;
			}
			if(!stripos($droplist,"selected='selected'") and $record_count==1)
			{
				$droplist = str_ireplace("<option value=","<option selected='selected' value=",$droplist) ;
			}
		}


			if($method=="selectOptions")
			{
				
			}
			else if($droplist_filter and $record_count>$list_limit and $method<>"suggest")
			{
				$droplist = "<select class='zig_droplist_filtered' id='{current_field_name}_id_{uniqueString}' {attribute_script} name='${name}'>".$droplist."</select>" ;
				$droplist = $droplist_filter ? "<span id='zig_div_field_select_{current_field_name}_{uniqueString}' class='{selectDivClass}'>".$droplist."</span>" : $droplist ;
				$droplist = $droplist_filter ? $this->filter($name,$table).$droplist : $droplist ;
			}
			else if($record_count and $method=="suggest")
			{
				switch($droplist_options)
				{
					case "":
					{
						$droplist = zig("template","block","field_element","input ${mode}") ;
						break ;
					}
					default:
					{
						$inputTemplate = zig("template","block","droplist","suggest ${mode}") ;
						$inputTemplate = str_replace("{droplist_options}",$droplist_options,$inputTemplate) ;
						$droplist = str_replace("{droplist}",$droplist,$inputTemplate) ;
						break ;
					}
				}
				$droplist = $droplist_filter ? "<span id='zig_div_field_droplist_${table}_${name}'>".$droplist."</span>" : $droplist ;
			}
			else
			{
				$droplist = "<select class='$class' id='{current_field_name}_id_{uniqueString}' {attribute_script} name='${name}'>".$droplist."</select>" ;
			}

			switch($method) {
				case "selected_label": {
					$droplist = $droplist_array[$default] ;
					break ;
				}
				case "select_array": {
					$droplist = $droplist_array ;
					break ;
				}
			}
			$zig_result['value'] = $droplist ;
		}

		return $zig_result ;
	}

	function search_selected($parameters) {
		$droplist = $parameters['droplist'] ;
		$sql = $parameters['sql'] ;
		$default = addslashes($parameters['default']) ;
		$value = $parameters['value'] ;

		$sql = "SELECT `a`.`${value}` FROM (${sql}) `a` WHERE `a`.`${value}`='${default}' LIMIT 1" ;
		$selected_result = zig("query",$sql) ;
		$selected_fetch = $selected_result->fetchRow() ;

		$zig_result['value'] = $selected_fetch[$value] ;
		$zig_result['buffer'] = isset($selected_fetch[$value]) ? str_ireplace("<option value='$selected_fetch[$value]'>","<option value='$selected_fetch[$value]' selected='selected'>",$droplist) : $droplist ;
		
		return $zig_result ;
	}
	
	function selected($parameters) {
		$default = $parameters['default'] ;
		$value = $parameters['value'] ;
		$sql = strtolower($parameters['sql']) ;

		if(stripos($sql,"where"))
		{
			$sql = str_ireplace("where","where $value='$default' and ",$sql) ;
		}
		else
		{
			$ripped_sql = explode(" from ",$sql) ;
			$ripped_table = explode(" ",$ripped_sql[1],2) ;
			$sql = $ripped_sql[0]." from ".$ripped_table[0]." where $value='$default' ".$ripped_table[1] ;
		}
		
		if(!stripos($sql,"limit"))
		{
			$sql.= " limit 1" ;
		}
		else
		{
			$ripped_sql = explode("limit",$sql) ;
			$sql = $ripped_sql[0]."limit 1" ;
		}

		$result = zig("query",$sql) ;
		$fetch=$result->fetchRow() ;
		
		if(isset($fetch[$value]))
		{
			$zig_result = $fetch[$value] ;
		}
		else
		{
			$selected_return = $this->search_selected($parameters) ;
			$zig_result = $selected_return['value'] ;
		}
		
		return $zig_result ;
	}
	
	function filter($name,$table)
	{
		$filter_name = "zig_droplist_filter_".$name ;
		$filter_name_id = "zig_droplist_filter_${table}_${name}" ;
		$filter_hash = zig("hash","encrypt","function=update_field,table=${table},field=${name},zigjax=1,droplist_filter=0") ;
		$buffer = "<select id='${filter_name_id}' onchange=\"zig('zig_div_input_${name}','${filter_hash}','','',this.value) ;\">" ;
		$buffer.= "<option></option>" ;
		foreach(range('a','z') as $char)
		{
			$buffer.= "<option value='$char'>" ;
			$buffer.= $char ;
			$buffer.= "</option>" ;
		}
		foreach(range('0','9') as $char)
		{
			$buffer.= "<option value='$char'>" ;
			$buffer.= $char ;
			$buffer.= "</option>" ;
		}

		$buffer.= "</select>" ;
		$filter_selected = isset($_GET[$filter_name]) ? $_GET[$filter_name] : (isset($_POST[$filter_name]) ? $_POST[$filter_name] : '') ;
		$buffer = $filter_selected ? str_ireplace("value='$filter_selected'","value='$filter_selected' selected='selected'",$buffer) : $buffer ;
		$zig_result = $buffer ;
		
		return $zig_result ;
	}
}

?>