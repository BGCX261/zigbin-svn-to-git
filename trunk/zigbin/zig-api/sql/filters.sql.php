<?php

class zig_filters
{
	function filters($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$sql = $arg1 ;
			$filters = $arg2 ;
			$exclude = $arg3 ;
			$dig = true ;
		}
		else if(is_array($parameters))
		{
			$sql = array_key_exists("sql",$parameters) ? $parameters['sql'] : NULL ;
			$filters = array_key_exists("filters",$parameters) ? $parameters['filters'] : NULL ;
			$exclude = array_key_exists("exclude",$parameters) ? $parameters['exclude'] : NULL ;
			$dig = array_key_exists("dig",$parameters) ? $parameters['dig'] : true ;
		}

		$zig_global_database = zig("config","global_database") ;
		$pre = zig("config","pre") ;

		// Start table process
		$sql = stripos($sql," limit ") ? $sql : $sql." LIMIT 1" ;
		$sql = strtolower($sql) ;
		$splitted_sql = explode(" from ",$sql) ;
		$splitted_sql = explode(" ",$splitted_sql[1]) ;
		$semi_stripped_table = str_replace(array("`",$zig_global_database."."),"",$splitted_sql[0]) ;
		$explodedSemiStrippedTable = explode("_",$semi_stripped_table) ;
		$tablePrefix = $explodedSemiStrippedTable[0] ;
		$stripped_table = str_replace($tablePrefix,"",$semi_stripped_table) ;
		$table = "${tablePrefix}${stripped_table}" ;
		$filter_tables[$table] = ucwords(str_replace("_"," ",$stripped_table)) ;

		if($dig)
		{
			$relationship_sql = "SELECT `fieldset`,`child_table` FROM `${zig_global_database}`.`${pre}relationships` WHERE (`parent_table`='all' OR `parent_table`='${table}' OR `parent_table`='${semi_stripped_table}' OR `parent_table`='${stripped_table}') AND `child_table`<>'' AND `zig_status`<>'deleted' ORDER BY `zig_weight`,`fieldset` ASC" ;
			$relationship_result = zig("query",$relationship_sql) ;
			while($relationship_fetch=$relationship_result->fetchRow())
			{
				$current_table = str_replace(array("`",$zig_global_database.".",$pre),"",$relationship_fetch['child_table']) ;
				$filter_tables[$current_table] = $relationship_fetch['fieldset'] ? $relationship_fetch['fieldset'] : ucwords(str_replace("_"," ",$current_table)) ;
			}
		}
		$fieldset_fields = array() ;
		$table_count = sizeof($filter_tables) ;
		foreach($filter_tables as $filter_table => $filter_fieldset)
		{
			$fieldsets = array($filter_fieldset) ;
			$fieldset_sql = "SELECT DISTINCT `fieldset`,`table_name` FROM `zig_fields` WHERE `fieldset`<>'' AND (`table_name`='all' OR `table_name`='${table}.${pre}${filter_table}' OR `table_name`='${pre}${filter_table}' OR `table_name`='${filter_table}') AND `zig_status`<>'deleted'" ;
			$fieldset_result = zig("query",$fieldset_sql) ;
			while($fieldset_fetch=$fieldset_result->fetchRow())
			{
				$fieldsets[$fieldset_fetch['table_name']] = $fieldset_fetch['fieldset'] ;
				$fieldset_fields_sql = "SELECT `field` FROM `${zig_global_database}`.`${pre}fields` WHERE `fieldset`='$fieldset_fetch[fieldset]' AND (`table_name`='all' OR `table_name`='${table}.${pre}${filter_table}' OR `table_name`='${pre}${filter_table}' OR `table_name`='${filter_table}') AND `zig_status`<>'deleted'" ;
				$fieldset_fields_result = zig("query",$fieldset_fields_sql) ;
				while($fieldset_fields_fetch=$fieldset_fields_result->fetchRow())
				{
					$fieldset_fields[$fieldset_fetch['fieldset']][] = $fieldset_fields_fetch['field'] ;
				}
			}

			$columns_sql = "SHOW COLUMNS FROM `${filter_table}`" ;
			$columns_result = zig("query",$columns_sql) ;
			while($columns_fetch=$columns_result->fetchRow())
			{
				$columns_fetch_array[$filter_table][] = $columns_fetch ;
			}

			foreach($fieldsets as $fieldset)
			{
				if(($fieldset and $table_count==1 and $fieldset<>$filter_fieldset) or ($fieldset and $table_count>1))
				{
					$parameters['filters'][] = array
					(
						'fname'			=>	$fieldset,
						'type'			=>	'optgroup',
						'visible'		=>	1
					) ;
				}

				foreach($columns_fetch_array[$filter_table] as $columns_fetch)
				{
					if(in_array($columns_fetch['Field'],$exclude) or (!$dig and !in_array($columns_fetch['Field'],$columns)))
					{
						continue ;
					}
					if($fieldset and zig("checkArray",$fieldset_fields,$fieldset))
					{
						if(!in_array($columns_fetch['Field'],$fieldset_fields[$fieldset]))
						{
							continue ;
						}
					}
					else if(is_array($fieldset_fields))
					{
						foreach($fieldsets as $fieldset_title)
						{
							if(zig("checkArray",$fieldset_fields,$fieldset_title))
							{
								if(in_array($columns_fetch['Field'],$fieldset_fields[$fieldset_title]))
								{
									$field_found = true ;
									break ;
								}
							}
						}
						if(isset($field_found))
						{
							unset($field_found) ;
							continue ;
						}
					}
					$titlecase_field = str_replace("_"," ",$columns_fetch['Field']) ;
					$titlecase_field = ucwords(trim($titlecase_field)) ;
					$data_type = zig("extractor","extract_type",$columns_fetch['Type']) ;
					$parameters['filters'][] = array
					(
						'filter_table'	=>	$filter_table,
						'fname'			=>	$titlecase_field,
						'vname'			=>	"zig_filter_${filter_table}_".$columns_fetch['Field'],
						'data_type'		=>	$data_type['type'],
						'type'			=>	'input',
						'visible'		=>	1
					) ;
				}
			}
			unset($fieldsets) ;
		}
		// End table process

//		$zig_remove = isset($_GET['zig_remove']) ? $_GET['zig_remove'] : (isset($_POST['zig_remove']) ? $_POST['zig_remove'] : '') ;
//		$zig_include = isset($_GET['zig_include']) ? $_GET['zig_include'] : (isset($_POST['zig_include']) ? $_POST['zig_include'] : '') ;
//		$ripped_zig_remove = split(",",$zig_remove) ;
//		$ripped_zig_include = explode(",",$zig_include) ;
//		$add = ($_POST['zig_add']=="add") ? $_POST['zig_filter_select'] : NULL ;

		$operators = "<option value='='>&equiv;</option>
			<option value='<'>&lt;</option>
			<option value='>'>&gt;</option>
			<option value='<='>&le;</option>
			<option value='>='>&ge;</option>
			<option value='!='>&ne;</option>
			<option value='LIKE'>contains</option>" ;
		
		$filter_select = "" ;
		$child_where = "" ;
		$buffer = zig("template","block","filters","filter header") ;

		/*foreach($parameters['filters'] as $ref => $filters)
		{
			$filter_table = $filters['filter_table'] ;
			$sql_vname = str_replace("zig_filter_${filter_table}_","",$filters['vname']) ;
			$vname = $filters['vname'] ;
			$data_type = $filters['data_type'] ;
			$fname = $filters['fname'] ;
			$type = $filters['type'] ;
			
/*			if($filters['set']<>0)
			{
				foreach($ripped_zig_remove as $value)
				{
					if(trim($value)==$vname and $add<>trim($value))
					{
						$filters['set'] = 0 ;
						break ;
					}
				}
			}*/

			/*if($add==$vname)
			{
				$filters['set'] = 1 ;
				$zig_include.= $vname."," ;
				$zig_remove = str_replace($vname.",","",$zig_remove) ;
				$ripped_zig_remove = explode(",",$zig_remove) ;
			}

			if($filters['set']<>1)
			{
				foreach($ripped_zig_include as $value)
				{
					if(trim($value)==$vname)
					{
						$filters['set'] = 1 ;
					}
				}
			}

			if($filters['set']==1 and $filters['visible']==1 and $filters['type']<>"optgroup")
			{
				$op_vname = "op_".$vname ;
				$op_value = $_GET[$op_vname] ? $_GET[$op_vname] : ($_POST[$op_vname] ? $_POST[$op_vname] : "=") ;
				$default = $filters['default'] ;
				$value = $_GET[$vname] ? $_GET[$vname] : ($_POST[$vname] ? $_POST[$vname] : $default) ;
				$zig_filter_data[$vname] = $value ;
				$zig_filter_data[$op_vname] = $op_value ;
				$list = $filters['list'] ;
				$list = $list ? str_ireplace("value='$value'","value='$value' selected='selected'",$list) : $list ;

				$buffer.= zig("template","block","filters","filter rows") ;
				$buffer = str_replace("{action}","<a href='javascript: return void(0) ;' onclick='filters_deleteCurrentRow(this) ;' name='remove_${vname}'>remove</a>",$buffer) ;
				$buffer = str_replace("{filter}",$filters['fname'],$buffer) ;

				if($type<>"checkbox")
				{
					if(is_array($filters['operators']))
					{
						foreach($filters['operators'] as $op)
						{
							$custom_op.= "<option value='$op'>$op</option>" ;
						}
					}
					else
					{
						$custom_op = "<option value='$filters[operators]'>$filters[operators]</option>" ;
					}
					$ops = $filters['operators'] ? $custom_op : $operators ;
					$op_buffer = "<select name='op_$vname'>$ops</select>" ;
					$op_buffer = $op_value ? str_replace("value='$op_value'","value='$op_value' selected='selected'",$op_buffer) : $op_buffer ;
				}

				$buffer = str_replace("{op}",$op_buffer,$buffer) ;

				switch($type)
				{
					case "input":
					$buffer = str_replace("{values}","<input type='input' name='$vname' value='$value'>",$buffer) ;
					break ;
				
					case "droplist":
					$buffer = str_replace("{values}","<select name='$vname'>$list</select>",$buffer) ;
					break ;
				
					case "checkbox":
					$buffer = str_replace("{values}","<input type='checkbox' name='$vname' value='$value'>",$buffer) ;
					break ;
				}

				switch($data_type)
				{
					case "date":
					case "datetime":
					case "time":
					case "timestamp":
					{
						list($year,$month,$day) = explode("-",$value) ;
						if(@!checkdate($month,$day,$year))
						{
							if(in_array(strtolower(substr($value,0,3)),array("jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec")))
							{
								$sql_vname = "LOWER(CONVERT((SUBSTR(MONTHNAME(${sql_vname}),1,3)) USING latin1))" ;
								$value = strtolower(substr($value,0,3)) ;
							}
							else if(in_array(strtolower(substr($value,0,3)),array("sun","mon","tue","wed","thu","fri","sat")))
							{
								$sql_vname = "LOWER(CONVERT((SUBSTR(DATE_FORMAT(${sql_vname},'%W'),1,3)) USING latin1))" ;
								$value = strtolower(substr($value,0,3)) ;
							}
							else if($value<=31)
							{
								$sql_vname = "DAY(${sql_vname})" ;
							}
							else
							{
								$sql_vname = "YEAR(${sql_vname})" ;
							}
						}
					}
				}

				$operator = $_GET[$op_vname] ? $_GET[$op_vname] : ($_POST[$op_vname] ? $_POST[$op_vname] : "<>") ;
				$like_op = ($operator=="LIKE") ? "%" : NULL ;
				if($filter_table<>$stripped_table and $value)
				{
					$search_sql = "SELECT `${zig_global_database}`.`${pre}${stripped_table}`.`id` FROM `${zig_global_database}`.`${pre}${stripped_table}`,`$zig_global_database`.${pre}${filter_table} WHERE `${zig_global_database}`.`${pre}${filter_table}`.`${sql_vname}` ${operator} '${like_op}${value}${like_op}' AND `${zig_global_database}`.`${pre}${filter_table}`.`zig_parent_id`=`${zig_global_database}`.`${pre}${stripped_table}`.`id` AND `${zig_global_database}`.`${pre}${stripped_table}`.`zig_status`<>'deleted'" ;
					$search_result = zig("query",$search_sql) ;
					while($search_fetch=$search_result->fetchRow())
					{
						$child_where = $child_where ? $child_where." OR `id`='$search_fetch[id]' " : " `id`='$search_fetch[id]' " ;
					}
				}
				else
				{
					$where = ($where and $value) ? $where." AND $sql_vname $operator '${like_op}${value}${like_op}'" : " $sql_vname $operator '${like_op}${value}${like_op}'" ;
				}
			}

			if($filters['visible']==1)
			{
				switch($type)
				{
					case "optgroup":
					{
						$filter_select.= $optgroup_tag_open ? "</optgroup><optgroup label='$fname'>" : "<optgroup label='$fname'>" ;
						$optgroup_tag_open = $optgroup_tag_open ? false : true ;
						break ;
					}
					case "input":
					{
						$filter_select.= "<option value='$vname'>$fname</option>" ;
						break ;
					}
				}
			}
		}
		$filter_select.= in_array("optgroup",$filters) ? "</optgroup>" : NULL ;*/

		if($filter_select<>"")
		{
			$filter_select_buffer = zig("template","block","filters","filter select") ;
			$filter_select_buffer = str_replace("{filter_select}",$filter_select,$filter_select_buffer) ;
			$buffer.= zig("template","block","filters","filter rows") ;
			$buffer = str_replace("{action}","",$buffer) ;
			$buffer = str_replace("{filter}",$filter_select_buffer,$buffer) ;
			$buffer = str_replace("{op}","",$buffer) ;
			$buffer = str_replace("{values}","Select &amp; Add new filter",$buffer) ;
		}
		
		$buffer.= zig("template","block","filters","filter footer") ;
		$buffer = str_replace("{zig_include}","<input type='hidden' id='zig_include' name='zig_include' value='' />",$buffer) ;
//		$buffer = str_replace("{zig_include}","<input type='hidden' id='zig_include' name='zig_include' value='$zig_include' />",$buffer) ;
//		$buffer = str_replace("{zig_includex}","<input type='hidden' id='zig_temp' name='zig_temp' value='' />",$buffer) ;
//		$buffer = str_replace("{zig_remove}","<input type='hidden' id='zig_remove' name='zig_remove' value='$zig_remove' />",$buffer) ;

		// -- Start fieldset
		$buffer = str_replace("{zig_visible_class}","zig_visible",$buffer) ;
		$buffer = str_replace("{zig_invisible_class}","zig_invisible",$buffer) ;
		$buffer = str_replace("{zig_fieldset_collapsed_class}","zig_fieldset_collapsed",$buffer) ;
		$buffer = str_replace("{zig_fieldset_displayed_class}","zig_fieldset_displayed",$buffer) ;
		// -- End fieldset

		$zig_result['buffer'] = $buffer ;

		$where = isset($where) ? (($where and $child_where) ? $where." AND ( ${child_where} ) " : ($child_where ? $child_where : "" )) : ($child_where ? $child_where : "" ) ;
		$zig_result['value'] = $where ? " ( ".$where." ) " : NULL ;
		$zig_result['form'] = 1 ;
		//$zig_result['config']['zig_include'] = $zig_include ;
		//$zig_result['config']['zig_remove'] = $zig_remove ;

		if(isset($zig_filter_data))
		{
			foreach($zig_filter_data as $key => $value)
			{
				$zig_result['config'][$key] = $value ;
			}
		}

		return $zig_result ;
	}
}
?>