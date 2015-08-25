<?php

class zig_where
{
	function where($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
			$keyword = $arg2 ;
		}
		else if(is_array($parameters))
		{
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$keyword = array_key_exists("keyword",$parameters) ? $parameters['keyword'] : NULL ;
		}
		$keyword = strtolower($keyword) ;
		$zig_global_database = zig("config","global_database") ;
		$pre = zig("config","pre") ;
		$magic_quotes = get_magic_quotes_gpc() ;
		$tables = zig("to_array",$table) ;
		$where_or = $where_and = $select_where = "" ;

		foreach($tables as $table)
		{
			// Start remove the database name on the table
			$semi_stripped_table = str_replace($zig_global_database.".","",$table) ;
			// End remove the database name on the table

			// Start stripped table name
			$stripped_table = str_replace($pre,"",$semi_stripped_table) ;
			// End stripped table name
			$splitted_table = explode(".",$table) ;
			$table = "" ;
			foreach($splitted_table as $escaped_tables)
			{
				$escaped_tables = str_replace("`","",$escaped_tables) ;
				$table.= $table ? ".`".$escaped_tables."`" : "`".$escaped_tables."`" ;
			}
			$field_sql = "SHOW COLUMNS FROM ${table}" ;
			$field_result = zig("query",$field_sql) ;
			
			// -- Start Get Keywords
			if(substr($keyword,0,1)=='"' and substr($keyword,(strlen($keyword)-1),1)=='"')
			{
				$stripped_keyword = substr($keyword,1,(strlen($keyword)-1)) ;
				$stripped_keyword = substr($stripped_keyword,0,(strlen($stripped_keyword)-1)) ;
				$keywords = array($stripped_keyword) ;
			}
			else
			{
				if(stripos($keyword," or "))
				{
					$keyword_pool = explode(" or ",$keyword) ;
					foreach($keyword_pool as $key)
					{
						$keyword_raw[] = $key ;
					}
				}
				else
				{
					$keyword_raw = zig("to_array",$keyword) ;
				}

				foreach($keyword_raw as $key)
				{
					$splitted_key = explode(" ",$key) ;
					foreach($splitted_key as $split_key)
					{
						$keywords[] = trim($split_key) ;
					}
				}

				if(!in_array($keyword,$keywords) and stripos($keyword," or ")===false)
				{
					$keywords[] = $keyword ;
				}						
			}
			// -- End Get Keywords

			while($field_fetch=$field_result->fetchRow())
			{
				$field_info_sql = "SELECT `option_value`,`sql` 
									FROM `zig_fields` 
									WHERE  
										(`table_name`='$table' OR `table_name`='$semi_stripped_table' OR `table_name`='$stripped_table') AND 
										`field`='$field_fetch[Field]' AND 
										`field_type`='select' AND 
										`option_value`<>'' AND 
										`sql`<>'' 
									LIMIT 1" ;
				$field_info_result = zig("query",$field_info_sql) ;
				$field_select_count = $field_info_result->RecordCount() ;
				if($field_select_count>0)
				{
					$field_info_fetch = $field_info_result->fetchRow() ;
					eval("\$select_sql = \"$field_info_fetch[sql]\";") ;
					$select_sql_extracted = zig("extractor","extract_sql",$select_sql) ;
					$field_select_count = (sizeof($select_sql_extracted['fields_array'])==1 and $select_sql_extracted['fields_array'][0]==$field_fetch['option_value']) ? 0 : sizeof($select_sql_extracted['fields_array']) ;
					switch($field_select_count)
					{
						case 0:
						{
							break ;
						}
						default:
						{
							switch(in_array($field_info_fetch['option_value'],$select_sql_extracted))
							{
								case false:
								{
									switch(stripos($select_sql," DISTINCT "))
									{
										case false:
										{
											$select_sql = str_ireplace("SELECT ","SELECT `".$field_info_fetch['option_value']."`,",$select_sql) ;
											break ;
										}
										default:
										{
											$select_sql = str_ireplace("SELECT DISTINCT ","SELECT DISTINCT `".$field_info_fetch['option_value']."`,",$select_sql) ;
											break ;
										}
									}
									break ;
								}
							}
							break ;
						}
					}
				}

				foreach($keywords as $key)
				{
					switch(substr($key,0,1))
					{
						case "-": // Case the keyword have a NOT
						{
							switch($field_select_count)
							{
								case 0:
								{
									$key = substr($key,1,(strlen($key)-1)) ;
									$key = $magic_quotes ? $key : addslashes($key) ;
									$where_and.= $where_and ? " AND ".$table.".`".$field_fetch['Field']."` NOT LIKE '".$key."'" : " ( ".$table.".`".$field_fetch['Field']."` NOT LIKE '".$key."'" ;
									break ;
								}
								default: // Process the field that have a reference SQL
								{
									foreach($select_sql_extracted['fields_array'] as $select_fields)
									{
										$select_where = $select_where ? $select_where." OR `${select_fields}` NOT LIKE '%${key}%'" : "`${select_fields}` NOT LIKE '%${key}%'" ;
									}
									switch($select_where)
									{
										case "":
										{
											break ;
										}
										default:
										{
											$select_sql = stripos($select_sql," WHERE ")===false ? str_ireplace(" FROM ".$select_sql_extracted['table']." "," FROM ".$select_sql_extracted['table']." WHERE ".$select_where." AND ",$select_sql) : str_ireplace(" WHERE "," WHERE ".$select_where." AND ",$select_sql) ;
											$select_where = "" ;
											$select_result = zig("query",$select_sql) ;
											while($select_fetch=$select_result->fetchRow())
											{
												$where_or.= $where_or ? " OR ".$table.".`".$field_fetch['Field']."` NOT LIKE '%".$select_fetch[$field_info_fetch['option_value']]."%'" : " ( ".$table.".`".$field_fetch['Field']."` NOT LIKE '%".$select_fetch[$field_info_fetch['option_value']]."%'" ;
											}
											break ;
										}
									}
									break ;
								}
							}
							break ;
						}
						default: // Case the keyword does not have a NOT
						{
							switch($field_select_count)
							{
								case 0:
								{
									$key = $magic_quotes ? $key : addslashes($key) ;
									$where_or.= $where_or ? " OR ".$table.".`".$field_fetch['Field']."` LIKE '%".$key."%'" : " ( ".$table.".`".$field_fetch['Field']."` LIKE '%".$key."%'" ;
									break ;
								}
								default: // Process the field that have a reference SQL
								{
									foreach($select_sql_extracted['fields_array'] as $select_fields)
									{
										$select_where = $select_where ? $select_where." OR `${select_fields}` LIKE '%${key}%'" : "`${select_fields}` LIKE '%${key}%'" ;
									}
									switch($select_where)
									{
										case "":
										{
											break ;
										}
										default:
										{
											$select_sql = stripos($select_sql," WHERE ")===false ? str_ireplace(" FROM ".$select_sql_extracted['table']." "," FROM ".$select_sql_extracted['table']." WHERE ".$select_where." ",$select_sql) : str_ireplace(" WHERE "," WHERE ".$select_where." AND ",$select_sql) ;
											$select_where = "" ;
											$select_result = zig("query",$select_sql) ;
											while($select_fetch=$select_result->fetchRow())
											{
												$where_or.= $where_or ? " OR ".$table.".`".$field_fetch['Field']."` LIKE '%".$select_fetch[$field_info_fetch['option_value']]."%'" : " ( ".$table.".`".$field_fetch['Field']."` LIKE '%".$select_fetch[$field_info_fetch['option_value']]."%'" ;
											}
											break ;
										}
									}
									break ;
								}
							}
							break ;
						}
					}
				}
			}
			$where_and.= $where_and ? " ) " : "" ;
			$where_or.= $where_or ? " ) " : "" ;
		}

		$where = ($where_and and $where_or) ? $where_and." AND ".$where_or : $where_and.$where_or ;
		$zig_result['value'] = $where ? " WHERE ".$where : "" ;

		$zig_result['return'] = 1 ;	
		return $zig_result ;
	}
}

?>