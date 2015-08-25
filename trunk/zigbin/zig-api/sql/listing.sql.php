<?php

class zig_listing {
	function listing($parameters,$arg1='',$arg2='',$arg3='') {
		$print_view = false ;
		$table = "" ;
		$unserialize = "" ;
		$zig_keyword = "" ;
		$page = "" ;
		$parent_value = "" ;
		$method = $module = "" ;
		$summary = true ;
		$trigger = true ;
		$zigjax = false ;
		$addLink = false ;
		$sort_field = "" ;
		$sort_direction = "" ;
		$parentId = "" ;
		$parentTable = "" ;
		$table_field_attributes = $tableFieldAttributes = $field_info_fetch = array() ;
		$uniqueString = uniqid() ;
		$detailsMode = "view" ;
		if($arg1 or $arg2 or $arg3) {
			$listing_sql = $search_sql = $arg1 ;
			$print_view = $arg2 ;
			$row_limit = $print_view ? 0 : ($arg3 ? $arg3 : zig("config","row_limit")) ;
		}
		if(is_array($parameters)) {
			$listing_sql = array_key_exists('sql',$parameters) ? $parameters['sql'] : NULL ;
			$search_sql = array_key_exists('search_sql',$parameters) ? $parameters['search_sql'] : $listing_sql ;
			$print_view = array_key_exists('print_view',$parameters) ? $parameters['print_view'] : $print_view ;
			$row_limit = ( $print_view and !array_key_exists('row_limit',$parameters) ) ? 0 : ( array_key_exists('row_limit',$parameters) ? $parameters['row_limit'] : zig("config","row_limit") ) ;
			$module = array_key_exists('module',$parameters) ? $parameters['module'] : NULL ;
			$table = array_key_exists('table',$parameters) ? $parameters['table'] : $table ;
			$unserialize = array_key_exists('unserialize',$parameters) ? $parameters['unserialize'] : $unserialize ;
			$zig_keyword = array_key_exists('zig_keyword',$parameters) ? $parameters['zig_keyword'] : $zig_keyword ;
			$page = array_key_exists('page',$parameters) ? $parameters['page'] : $page ;
			$parentId = array_key_exists('parentId',$parameters) ? $parameters['parentId'] : $parentId ;
			$parentTable = array_key_exists('parentTable',$parameters) ? $parameters['parentTable'] : $parentTable ;
			$row_total = array_key_exists('row_total',$parameters) ? $parameters['row_total'] : NULL ;
			$method = array_key_exists('method',$parameters) ? $parameters['method'] : $method ;
			$summary = array_key_exists('summary',$parameters) ? $parameters['summary'] : $summary ;
			$trigger = array_key_exists('trigger',$parameters) ? $parameters['trigger'] : $trigger ;
			$zigjax = array_key_exists('zigjax',$parameters) ? $parameters['zigjax'] : $zigjax ;
			$addLink = array_key_exists('addLink',$parameters) ? $parameters['addLink'] : ($zigjax ? false : true) ;
			$parent_value = array_key_exists('parent_value',$parameters) ? $parameters['parent_value'] : $parent_value ;
			$sort_field = array_key_exists("sort_field",$parameters) ? $parameters['sort_field'] : $sort_field ;
			$sort_direction = array_key_exists("sort_direction",$parameters) ? $parameters['sort_direction'] : $sort_direction ;
			$table_field_attributes = array_key_exists("tableFieldAttributes",$parameters) ? $parameters['tableFieldAttributes'] : $tableFieldAttributes ;
			$uniqueString = array_key_exists("uniqueString",$parameters) ? $parameters['uniqueString'] : $uniqueString ;
			$detailsMode = array_key_exists("detailsMode",$parameters) ? $parameters['detailsMode'] : $detailsMode ;
		}

		if($method=="list_info") {
			$zig_return = $this->list_info($row_total) ;
			$buffer = $zig_return['buffer'] ;
		}
		else {
		$full_result = zig("query",$listing_sql,"",false) ;
		$row_total = $full_result->RecordCount() ;
		//$zig_hash_sql = str_replace(",","{comma}",$search_sql) ;
		//$zig_hash_sql = str_replace("=","{equal}",$zig_hash_sql) ;
		$zig_hash_sql = zig("hash","stringEncode",$search_sql) ;

		switch($row_total)
		{
			case 0:
			{
				if($print_view)
				{
					$buffer = "" ;
				}
				else
				{
					$buffer = zig("template","block","listing","listing empty") ;
					switch($addLink)
					{
						case true:
						{
							$addParameters = array
							(
								"function"		=>	"add",
								"module"		=>	$module,
								"table"			=>	$table,
								"parentTable"	=>	$parentTable,
								"parentId"		=>	$parentId,
								"uniqueString"	=>	$uniqueString,
								"sql"			=>	$zig_hash_sql,
								"zigjax"		=>	$zigjax,
								"triggers"		=>	"hide"
							) ;
							$addBuffer = zig($addParameters) ;
							$buffer = str_replace("{addBuffer}",$addBuffer,$buffer) ;
							break ;
						}
					}
				}
				break ;
			}
			default:
			{
				if($sort_field)
				{
					if(stripos($listing_sql," ORDER BY "))
					{
						$listing_sql = str_ireplace(" ORDER BY "," ORDER BY `$sort_field` $sort_direction,",$listing_sql) ;			
					}
					else if(stripos($listing_sql," GROUP BY "))
					{
						$listing_sql = str_ireplace(" GROUP BY "," ORDER BY `$sort_field` $sort_direction GROUP BY ",$listing_sql) ;
					}
					else if(stripos($listing_sql," LIMIT "))
					{
						$listing_sql = str_ireplace(" LIMIT "," ORDER BY `$sort_field` $sort_direction LIMIT ",$listing_sql) ;
					}
					else
					{
						$listing_sql = $listing_sql." ORDER BY `$sort_field` $sort_direction " ;
					}
				}

				//$zig_listing_left_palm_sign = zig("config","listing left palm sign") ;
				//$zig_listing_right_palm_sign = zig("config","listing right palm sign") ;
				$zig_global_database = zig("config","global_database") ;
				$pre = zig("config","pre") ;

				// Start remove the database name on the table
				$semi_stripped_table = str_replace($zig_global_database.".","",$table) ;
				// End remove the database name on the table

				// Start stripped table name
				$stripped_table = str_replace($pre,"",$semi_stripped_table) ;
				// End stripped table name
				
				$list_string_limit = zig("config","list_string_limit") ;
				if($print_view)
				{
					$buffer = zig("template","block","listing","listing print view") ;
				}
				else
				{
					if($zigjax)
					{
						$buffer = zig("template","block","listing","listing") ;
					}
					else
					{
						$buffer = zig("template","block","listing","listing wrapper") ;
						$buffer = str_replace("{listing}",zig("template","block","listing","listing"),$buffer) ;
					}
				}
				$sql = $listing_sql ;
				
				/* Start check if sql have LIMIT */
				if(stripos($sql,"limit"))
				{
					$sql = explode("limit ",$sql) ;
					$sql_tail = "" ;
					if(count($sql)>1)
					{
						$sql_limit = explode(" ",$sql[1]) ;
						$sql_tail = $sql_limit[1] ;
						$sql_limit = explode(",",$sql_limit[0]) ;
						$sql_limit = $sql_limit[0] ;
					}
					$sql = $sql[0] ? $sql[0].$sql_tail : $listing_sql ;
				}
				/* End check if sql have LIMIT */

				/* Start Paging */
				$reloadParameters = "function=search,sql=${zig_hash_sql},table=${table},zig_keyword=${zig_keyword},row_total=${row_total},
										trigger=${trigger},summary=${summary},update_listing=1,zigjax=1,uniqueString=${uniqueString}" ;
				$reloadParameters = zig("hash","encrypt",$reloadParameters) ;
				$leftActions = $print_view ? "" : zig("template","block","listing","leftActions") ;
				$rightActions = $print_view ? "" : zig("template","block","listing","rightActions") ;
				$zigAddLink = zig("hash","encrypt","function=add,module=${module},table=${table},parentTable=${parentTable},parentId=${parentId},uniqueString=${uniqueString},sql=${zig_hash_sql},zigjax=1") ;
				$rightActions = str_replace("{zigAddLink}",$zigAddLink,$rightActions) ;
				if($row_limit and $row_limit<$row_total)
				{
					$paging_parameters = array
					(
						"function"			=>	"paging",
						"row_total"			=>	$row_total,
						"row_limit"			=>	$row_limit,
						"page"				=>	$page,
						"reloadParameters"	=>	$reloadParameters
					) ;
					$paging = $this->paging($paging_parameters) ;
					$current_page = $paging['value'] ;
					$row_start = $paging['row_start'] ;
					$buffer = str_replace("{listing_paging_bottom}",$leftActions.$paging['buffer'].$rightActions,$buffer) ;
				}
				else
				{
					$paging = $this->list_info($row_total,$row_total) ;
					$current_page = 1 ;
					$row_start = $paging['value'] ;
					$buffer = str_replace("{listing_paging_bottom}","",$buffer) ;
				}
				$buffer = str_replace("{listing_paging_top}",$leftActions.$paging['buffer'].$rightActions,$buffer) ;
				/* End Paging */

				// -- Start delete link
				$buffer = str_replace("{zigHash}",zig("hash","encrypt","function=delete,module=${module},table=${table},reloadParameters=${reloadParameters},zigjax=1"),$buffer) ;
				// -- End delete link

				$sql_row_start = $row_start ? ($row_start - 1) : $row_start ;
				$sql = $row_limit ? $sql." limit ${sql_row_start},${row_limit}" : $sql ;
				$result = zig("query",$sql,"",false) ;
				$result->MoveFirst() ;

				/* Start Field Header */
				$columns = zig("fetchcol",$result) ;
				if($table)
				{
					$table_field_sql = "SHOW COLUMNS FROM $table" ;
					$table_field_result = zig("query",$table_field_sql) ;
					while($table_field_fetch = $table_field_result->fetchRow())
					{
						$table_field_attributes[$table_field_fetch['Field']] = $table_field_fetch ;
					}
				}

				switch(($print_view or !$trigger))
				{
					case true:
					{
						$listingHeader = "" ;
						$column_number = 0 ;
						break ;
					}
					default:
					{
						$column_number = 1 ;
						$listingHeader = zig("template","block","listing","header all") ;
						$listingHeader = str_replace("{sort_field}",$sort_field,$listingHeader) ;
						$listingHeader = str_replace("{sort_direction}",$sort_direction,$listingHeader) ;
					}
				}
				$headerColumnTemplate = zig("template","block","listing","header column") ;

				$customFieldParameters = array(
					"function"		=>	"customField",
					"module"		=>	$module,
					"table"			=>	$table,
					"mode"			=>	"search",
					"uniqueString"	=>	$uniqueString
				) ;
				$field_info_fetch = zig($customFieldParameters) ;

				foreach($columns as $values) {
					switch(array_key_exists($values,$field_info_fetch))
					{
						case false:
						{
							$field_info_fetch[$values] = array() ;
						}
					}
					if(zig("checkArray",$field_info_fetch[$values],"attribute")<>"password")
					{
						$column_number++ ;
						if(zig("checkArray",$field_info_fetch[$values],"field_type")=="select") {
							$droplist_parameters = array(
								'function'	=>	"droplist",
								'method'	=>	"select_array",
								'sql'		=>	$field_info_fetch[$values]['sql'],
								'value'		=>	zig("checkArray",$field_info_fetch[$values],"option_value"),
								'name'		=>	$values,
								'table'		=>	$stripped_table
							) ;
							$droplist_array[$values] = zig($droplist_parameters) ;
						}
						$fields[] = $values ;
						if(zig("checkArray",$field_info_fetch[$values],"field_label")<>"")
						{
							$titlecase_field = $field_info_fetch[$values]['field_label'] ;
						}
						else
						{
							$titlecase_field = str_replace("_"," ",$values) ;
							$titlecase_field = ucwords(trim($titlecase_field)) ;
						}
						$headerColumnBuffer = $headerColumnTemplate ;
						$headerColumnBuffer = str_replace("{titleCaseField}",$titlecase_field,$headerColumnBuffer) ;
						$headerColumnBuffer = str_replace("{columnNumber}",$column_number,$headerColumnBuffer) ;
						$headerColumnBuffer = str_replace("{values}",$values,$headerColumnBuffer) ;
						$truncateOption = isset($_GET['zig_listing_truncate_'.$values]) ? $_GET['zig_listing_truncate_'.$values] : (isset($_POST['zig_listing_truncate_'.$values]) ? $_POST['zig_listing_truncate_'.$values] : "yes") ;
						$headerColumnBuffer = str_replace("{truncateOption}",$truncateOption,$headerColumnBuffer) ;
						$listingHeader.= $headerColumnBuffer ;
					}
				}
				//$buff.= $zig_listing_right_palm_sign ? "<th>#</th>\n" : "" ;
				$buffer = str_replace("{listingHeader}",$listingHeader,$buffer) ;
				/* End Field Header */

				// Start Rows
				$highlight_conditions = $this->listing_highlight_conditions("listing_highlight_conditions",$stripped_table) ;
				$zig_hash = "action=view,zig_keyword=${zig_keyword},page=${current_page},id=" ;
				$counter = 0 ;
				$zig_row_color = "zig_row_color2" ;
				$buff = "" ;
				while($fetch=$result->fetchRow()) {
					$counter++ ;
					$zig_row_color = $zig_row_color=="zig_row_color2" ? "zig_row_color3" : "zig_row_color2" ;
					$id = zig("checkArray",$fetch,"id") ;
					$zig_view_link = zig("hash","encrypt","function=${detailsMode},module=${module},table=${table},id=${id},
										parentTable=${parentTable},parentId=${parentId},uniqueString=${uniqueString},
										passedSql=$zig_hash_sql,detailsMode=${detailsMode},zigjax=1") ;
					$data_link = "javascript:" ;
					$hashed_link = "listingView('${uniqueString}','${zig_view_link}') ;" ;
					$buff.= "<tr id='${uniqueString}_row_${counter}' {zig_row_color_class} " ;
					$buff.= ($print_view or !$trigger) ? NULL : "onclick=\"${hashed_link}\" onmouseover=\"this.className='zig_listing_row_highlighted' ;\" onmouseout=\"if(!document.getElementById('{uniqueString}_checkbox_$counter').checked) this.className='$zig_row_color' ;\"" ;
					$buff.= ">\n" ;
					//$buff.= $zig_listing_left_palm_sign ? "<td align='center'>$counter</td>\n" : "" ;
					$buff.= ($print_view or !$trigger) ? NULL : "<td align='center'><input type='hidden' id='${uniqueString}_class_$counter' value='$zig_row_color' /><input id='{uniqueString}_checkbox_${counter}' type='checkbox' onmouseover='listingDisableView();' onmouseout='listingEnableView();' /></td>" ;
					// -- Start of foreach loop
					$column = 0 ;
					$alignment = $highlight_color = "" ;
						foreach($fields as $values)
						{
							$column++ ;
							$original_value = $fetch[$values] ;
							$hash_link = "" ;
							$highlight_color = (!$highlight_color and $highlight_conditions) ? $this->listing_highlight_search("listing_highlight_search",$highlight_conditions,$values,$fetch[$values]) : $highlight_color ;
							$field_fetch = $field_info_fetch[$values] ;
							if(zig("checkArray",$field_fetch,"field_type")=="select") {
								$fetch[$values] = zig("checkArray",$droplist_array[$values],$fetch[$values]) ;
							}

							// -- Start trim data
							$truncate_option = isset($_GET['zig_listing_truncate_'.$values]) ? $_GET['zig_listing_truncate_'.$values] : (isset($_POST['zig_listing_truncate_'.$values]) ? $_POST['zig_listing_truncate_'.$values] : "yes") ;
							$field_list_string_limit = zig("checkArray",$field_info_fetch[$values],"truncate_tolerance") ? $field_info_fetch[$values]['truncate_tolerance'] : $list_string_limit ;
							$balloon_tip = "" ;
							if(is_array($unserialize) and in_array($values,$unserialize) or (!is_array($unserialize) and $values==$unserialize) and $fetch[$values])
							{
								$unserialized_array = unserialize($fetch[$values]) ;
								if(is_array($unserialized_array))
								{
									$unserialized_template = zig("template","block","unserialized","header") ;
									foreach($unserialized_array as $revision_info)
									{
										$unserialized_template.= zig("template","block","unserialized","body") ;
										$unserialized_template = str_replace("{fieldname}",$revision_info['fieldname'],$unserialized_template) ;
										$unserialized_template = str_replace("{value}",htmlspecialchars($revision_info['value']),$unserialized_template) ;
									}
									$unserialized_template.= zig("template","block","unserialized","footer") ;
									$trimmed_string = $unserialized_template ;
								}
							}
							else if((strlen($fetch[$values]) > $field_list_string_limit) and $field_list_string_limit and $truncate_option<>"no")
							{
								$trimmed_string = substr($fetch[$values],0,$field_list_string_limit)."..." ;
								$balloon_tip_text = addslashes($fetch[$values]) ;
								$balloon_tip_text = htmlspecialchars($balloon_tip_text,ENT_QUOTES) ;
								$balloon_tip = "onmouseover=\"Tip('${balloon_tip_text}', BALLOON, true, ABOVE, true, OFFSETX, -17, FADEIN, 600, FADEOUT, 600, PADDING, 8) ;\" onmouseout=\"UnTip() ;\"" ;
							}
							else
							{
								$field_info_fetch[$values]['attribute'] = zig("checkArray",$field_info_fetch[$values],"type")=="file" 
																			? "file" : zig("checkArray",$field_info_fetch[$values],"attribute") ;
								if(!$field_info_fetch[$values]['attribute'] and isset($table_field_attributes))
								{
									switch(array_key_exists($values,$table_field_attributes))
									{
										case false:
										{
											$table_field_attributes[$values] = array() ;
										}
									}
									switch(zig("checkArray",$table_field_attributes[$values],"Type"))
									{
										case "tinyint(1)":
										{
											$field_info_fetch[$values]['alignment'] = "center" ;
											$field_info_fetch[$values]['attribute'] = zig("checkArray",zig("checkArray",$field_info_fetch,$values),"attribute")=="tickable" ? "checkbox-tickable" : "checkbox" ;
											break ;
										}
										case "integer":
										case "double":
										{
											$field_info_fetch[$values]['alignment'] = "right" ;
										}
										default:
										{
											if(substr(zig("checkArray",$table_field_attributes[$values],"Type"),0,3)=="int(")
											{
												$field_info_fetch[$values]['attribute'] = "integer" ;
												$field_info_fetch[$values]['alignment'] = "right" ;
											}
											else
											{
												$field_info_fetch[$values]['attribute'] = zig("checkArray",$table_field_attributes[$values],"Type") ;
											}
											break ;	
										}
									}
								}

								$alignment = array_key_exists("alignment",$field_info_fetch[$values]) ? ($field_info_fetch[$values]['alignment']<>"" ? "align='".$field_info_fetch[$values]['alignment']."'" : "") : "" ;
								switch($field_info_fetch[$values]['attribute'])
								{									
									case "password":
									{
										$trimmed_string = "[hidden]" ;
										break ;
									}
									case "checkbox":
									{
										$checked = $fetch[$values] ? "checked=checked" : NULL ;
										$trimmed_string = "<input type=\"checkbox\" $checked disabled=\"disabled\" />" ;
										break ;
									}
									case "checkbox-tickable":
									{
										$checked = $fetch[$values] ? "checked=checked" : NULL ;
										$zig_checkbox_hash_values = "function=update,table=${table},where=WHERE id{zig_escaped_equal}${id} LIMIT 1,zigjax=1" ;
										$zig_checkbox_hash_values = zig("hash","encrypt",$zig_checkbox_hash_values) ;
										$trimmed_string = "<input onchange=\"zig('div_zig_message','${zig_checkbox_hash_values}','','${values}=' + this.checked) ;\" type=\"checkbox\" $checked />" ;
										break ;
									}
									case "date":
									case "datetime":
									case "time":
									case "timestamp":
									{				
										// -- Start date reformatting
										$datetime = $fetch[$values] ? zig("datetime",$fetch[$values]) : NULL ;
										$trimmed_string = $datetime ? $datetime : $fetch[$values] ;
										// -- End date reformatting
										break ;
									}
									case "double":
									{
										$trimmed_string = number_format($fetch[$values],2) ;
										break ;
									}
									case "elapsed":
									{
										$trimmed_string = zig("elapsed",$fetch[$values]) ;
										break ;
									}
									case "thumbnail":
									case "file":
									{
										$field_element_parameters = array
										(
										 	"function"			=>	"field_element",
											"mode"				=>	"view",
											"field_type"		=>	"file",
											"field_value"		=>	$fetch[$values],
											"field_attribute"	=>	$field_info_fetch[$values]['attribute'],
											"current_field_name"=>	$values."_".$counter,
									 	) ;
										$trimmed_string = zig($field_element_parameters) ;
										$trimmed_string = str_replace("{escaped_data}",$fetch[$values],$trimmed_string) ;
										break ;
									}
									default:
										$trimmed_string = htmlspecialchars($fetch[$values]) ;
								}
							}
							$buff.= "<td id='zig_listing_${table}_${column}_${counter}' ${alignment} ${balloon_tip}>" ;
							// -- End trim data

							// -- Start link data
							if($hash_link and !$print_view and $trigger)
							{
								$buff.= "<a id=\"zig_listing_view_index_${counter}\" href=\"${data_link}${hash_link}\">".$trimmed_string."</a>" ;
							}
							else
							{
								$buff.= $trimmed_string ;
							}
							$buff.= "</td>\n" ;
							unset($balloon_tip,$trimmed_string) ;
							// -- End link data
						}
						// -- End of foreach loop

						$zig_row_color_class = $highlight_color ? "style='background-color: ${highlight_color} ;'" : "class='${zig_row_color}'" ;
						$buff = str_replace("{zig_row_color_class}",$zig_row_color_class,$buff) ;
						//$buff.= $zig_listing_right_palm_sign ? "<td align='center'>$counter</td>\n" : "" ;
						$buff.= "</tr>\n" ;
						$records[] = $fetch ;
						unset($highlight_color) ;
					}
					// -- End Rows
				$buffer = $print_view ? str_replace("{zigDivClass}","zigDivClassPrint",$buffer) : str_replace("{zigDivClass}","zigDivClass",$buffer) ;
				//$buffer = $summary ? str_replace("{listing_summary}",zig("summary",$records),$buffer) : $buffer ;
				$buffer = $summary ? str_replace("{listing_summary}","",$buffer) : $buffer ;
				$buffer = str_replace("{listing_list}",$buff,$buffer) ;
			}	// -- End default switch case
		}	// -- End switch condition
		
		}	// -- End else condition

		$jscript_events = isset($jscript_events) ? str_replace("{jscripts}",zig("minify",$jscript_events),zig("template","block","jscripts","jscript parse")) : NULL ;
		$buffer = str_replace("{uniqueString}",$uniqueString,$buffer) ;
		$buffer = str_replace("{jscript_events}",$jscript_events,$buffer) ;
		$zig_result['return'] = 1 ;
		$zig_result['row_total'] = $row_total ;
		$zig_result['value'] = $buffer ;
		$zig_result['form'] = 1 ;

		return $zig_result ;
	}

	//-- Start Paging
	function paging($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$row_total = $arg1 ;
			$row_limit = $arg2 ;
			$current_page = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$row_total = $parameters['row_total'] ;
			$row_limit = $parameters['row_limit'] ;
			$current_page = $parameters['page'] ;
			$reloadParameters = $parameters['reloadParameters'] ;
		}

		$page_range = 100 ;
		$page_jump = 500 ;
		$current_page = $current_page ? $current_page : (zig("checkArray",$_POST,"zig_page") ? $_POST['zig_page'] : (zig("checkArray",$_GET,"zig_page") ? $_GET['zig_page'] : 1) ) ;
		$row_limit = $row_limit ? $row_limit : zig("config","row_limit") ;
		$page_total = ($row_limit==0) ? 0 : $row_total/$row_limit ;
		$page_total = ($page_total-intval($page_total)) ? intval($page_total)+1 : $page_total ;

		$buffer = zig("template","block","listing","listing paging") ;
		$zig_first = isset($_GET['zig_first']) ? $_GET['zig_first'] : (isset($_POST['zig_first']) ? $_POST['zig_first'] : '') ;
		$zig_previous = isset($_GET['zig_previous']) ? $_GET['zig_previous'] : (isset($_POST['zig_previous']) ? $_POST['zig_previous'] : '') ;
		$zig_next = isset($_GET['zig_next']) ? $_GET['zig_next'] : (isset($_POST['zig_next']) ? $_POST['zig_next'] : '') ;
		$zig_last = isset($_GET['zig_last']) ? $_GET['zig_last'] : (isset($_POST['zig_last']) ? $_POST['zig_last'] : '') ;

		if($zig_first)
		{
			$current_page = 1 ;
		}
		else if(zig("checkArray",$_POST,"zig_previous"))
		{
			$current_page-- ;
		}				
		else if(zig("checkArray",$_POST,"zig_next"))
		{
			$current_page++ ; 
		}
		else if(zig("checkArray",$_POST,"zig_last"))
		{
			$current_page = $page_total ;
		}

		$left_paging_control = "" ;
		if($current_page>1)
		{
			$left_paging_control = "<a href=\"javascript:zig('divList_{uniqueString}','${reloadParameters}','','','','zig_page=' + 1) ;\">&lt;&lt;</a>&nbsp;" ;
			$left_paging_control.= "&nbsp;<a href=\"javascript:zig('divList_{uniqueString}','${reloadParameters}','','','','zig_page=' + (Number(document.getElementById('zig_page_id').value) - 1)) ;\">&lt;</a>&nbsp;" ;
		}

		$list_info = $this->list_info($row_total,$row_limit,$current_page) ;
		$buffer = str_replace("{list_info}",$list_info['buffer'],$buffer) ;
		$zig_result['row_start'] = $list_info['value'] ;

		if($page_total>1)
		{
			$patch = ($current_page<100) ? 1 : 0 ;
			$buffer = str_replace("{reloadParameters}",$reloadParameters,$buffer) ;
			$page = ($current_page - 100) > 0 ? $current_page - 100 : 0 ;
			$page_options = "" ;
			while($page_total>$page)
			{
				$difference = abs($page-$current_page) + $patch ;
				if(($difference<100) or ($difference<=100 and $current_page>$page))
				{
					$page++ ;
				}
				else if(((abs($page-$current_page)+$patch)<500 or (($page+500)==$current_page)) and (($page+400)<$page_total))
				{
					$page = $page + 400 ;
				}
				else if(($page+500)<$page_total)
				{
					$page = $page + 500 ;
				}
				else
				{
					$page = $page_total ;
				}
				$page_options.= "<option value='$page'>$page</option>" ;
			}
			$page_options = str_replace("value='$current_page'","value='$current_page' selected='selected'",$page_options) ;
			$buffer = str_replace("{page_options",$page_options,$buffer) ;
		}

		if($current_page<$page_total)
		{
			$right_paging_control = "&nbsp;<a href=\"javascript:zig('divList_{uniqueString}','${reloadParameters}','','','','zig_page=' + (Number(document.getElementById('zig_page_id').value) + 1)) ;\">&gt;</a>&nbsp;" ;
			$right_paging_control.= "&nbsp;<a href=\"javascript:zig('divList_{uniqueString}','${reloadParameters}','','','','zig_page=' + ${page_total}) ;\">&gt;&gt;</a>" ;
		}
		else
		{
			$right_paging_control = "" ;
		}
		$buffer = str_replace("{left_paging_control}",$left_paging_control,$buffer) ;
		$buffer = str_replace("{right_paging_control}",$right_paging_control,$buffer) ;
		$zig_result['buffer'] = $buffer ;
		$zig_result['value'] = $current_page ;

		return $zig_result ;
	}
	//-- End Paging

	//-- Start Listing Info
	function list_info($row_total,$row_limit='',$current_page='')
	{
		$current_page = $current_page ? $current_page : ( isset($_POST['zig_page']) ? $_POST['zig_page'] : (isset($_GET['zig_page']) ? $_GET['zig_page'] : 1) ) ;
		$row_limit = $row_limit ? $row_limit : zig("config","row_limit") ;
		$row_start = ($current_page-1)*$row_limit + 1 ;
		$row_end = (($row_start + $row_limit - 1)>$row_total) ? $row_total : ($row_start + $row_limit - 1) ;
		$zig_result['buffer'] = "record ${row_start} - ${row_end} of ${row_total}" ;
		$zig_result['value'] = $row_start ;
		
		return $zig_result ;
	}
	//-- End Listing Info

	//-- Start Listing Highlight Conditions
	function listing_highlight_conditions($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL)
	{
		$table_name = $arg1 ;
		$counter = 0 ;
		$zig_global_database = zig("config","global_database") ;
		$pre = zig("config","pre") ;
		$highlight_conditions = false ;
		$highlight_table_sql = "SELECT `id`,`color` FROM `zig_highlights` WHERE `table_name`='${table_name}'" ;
		$highlight_table_result = zig("query",$highlight_table_sql) ;
		while($highlight_table_fetch=$highlight_table_result->fetchRow())
		{
			$highlight_conditions_sql = "SELECT `field_name`,`operator`,`field_value` FROM `zig_highlight_conditions` WHERE `zig_parent_id`='$highlight_table_fetch[id]'" ;
			$highlight_conditions_result = zig("query",$highlight_conditions_sql) ;
			while($highlight_conditions_fetch=$highlight_conditions_result->fetchRow())
			{
				$highlight_conditions[$counter]['field_name'] = $highlight_conditions_fetch['field_name'] ;
				$highlight_conditions[$counter]['operator'] = $highlight_conditions_fetch['operator'] ;
				$highlight_conditions[$counter]['field_value'] = $highlight_conditions_fetch['field_value'] ;
				$highlight_conditions[$counter]['color'] = $highlight_table_fetch['color'] ;
			}
			$counter++ ;
		}

		return $highlight_conditions ;
	}
	//-- End Listing Highlight Conditions

	//-- Start Listing Highlight Search
	function listing_highlight_search($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL)
	{
		$highlight_conditions = $arg1 ;
		$field_name = $arg2 ;
		$field_value = $arg3 ;

		foreach($highlight_conditions as $highlight_condition)
		{
			switch($highlight_condition['field_name']==$field_name)
			{
				case true:
				{
					switch($highlight_condition['operator'])
					{
						case "=":
						{
							$color = $highlight_condition['field_value']==$field_value ? $highlight_condition['color'] : NULL ;
							break ;
						}
						case "<=":
						{
							$color = $highlight_condition['field_value']<=$field_value ? $highlight_condition['color'] : NULL ;
							break ;
						}
						case ">=":
						{
							$color = $highlight_condition['field_value']>=$field_value ? $highlight_condition['color'] : NULL ;
							break ;						
						}
						case "<":
						{
							$color = $highlight_condition['field_value']<$field_value ? $highlight_condition['color'] : NULL ;
							break ;						
						}
						case ">":
						{
							$color = $highlight_condition['field_value']>$field_value ? $highlight_condition['color'] : NULL ;
							break ;						
						}
						case "<>":
						{
							$color = $highlight_condition['field_value']<>$field_value ? $highlight_condition['color'] : NULL ;
							break ;						
						}
						case "contains":
						{
							$color = strpos($field_value,$highlight_condition['field_value'])===false ? NULL : $highlight_condition['color'] ;
							break ;						
						}
					}
					break ;
				}
			}
		}

		return $color ;
	}
	//-- End Listing Highlight Search
}

?>