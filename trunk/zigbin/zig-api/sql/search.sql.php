<?php

class zig_search
{
	function search($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$sql = $arg1 ;
			$table = $arg2 ;
			$row_limit = $arg3 ;
			$filter_sql = $sql ;
			$trigger = true ;
			$trigger_list = true ;
			$dig = true ;
		}
		else if(is_array($parameters))
		{
			$sql = array_key_exists('sql',$parameters) ? $parameters['sql'] : NULL ;
			$search_sql = array_key_exists('search_sql',$parameters) ? $parameters['search_sql'] : $sql ;
			$module = array_key_exists('module',$parameters) ? $parameters['module'] : NULL ;
			$table = array_key_exists('table',$parameters) ? $parameters['table'] : NULL ;
			$row_limit = array_key_exists('row_limit',$parameters) ? $parameters['row_limit'] : NULL ;
			$filters = array_key_exists('filters',$parameters) ? $parameters['filters'] : NULL ;
			$filter_sql = array_key_exists('filter_sql',$parameters) ? $parameters['filter_sql'] : $sql ;
			$zig_keyword = array_key_exists('zig_keyword',$parameters) ? trim($parameters['zig_keyword']) : NULL ;
			$page = array_key_exists('page',$parameters) ? $parameters['page'] : NULL ;
			$zig_attach = array_key_exists('zig_attach',$parameters) ? $parameters['zig_attach'] : NULL ;
			$view_link = array_key_exists('view_link',$parameters) ? $parameters['view_link'] : NULL ;
			$trigger = array_key_exists('trigger',$parameters) ? $parameters['trigger'] : true ;
			$summary = array_key_exists('summary',$parameters) ? $parameters['summary'] : true ;
			$update_listing = array_key_exists('update_listing',$parameters) ? $parameters['update_listing'] : NULL ;
			$trigger_list = array_key_exists('trigger_list',$parameters) ? $parameters['trigger_list'] : true ;
			$unserialize = array_key_exists('unserialize',$parameters) ? $parameters['unserialize'] : NULL ;
			$row_limit = array_key_exists('row_limit',$parameters) ? $parameters['row_limit'] : NULL ;
			$dig = array_key_exists('dig',$parameters) ? $parameters['dig'] : true ;
			$zigjax = array_key_exists('zigjax',$parameters) ? $parameters['zigjax'] : false ;
			$addLink = array_key_exists('addLink',$parameters) ? $parameters['addLink'] : (($zigjax and !$zig_keyword) ? false : true) ;
			$uniqueString = array_key_exists('uniqueString',$parameters) ? $parameters['uniqueString'] : uniqid() ;
		}

		$child_id_where = "" ;
		$sql = strtolower($sql) ;
		$exclude = zig("config","exclude") ;
		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$where = "" ;
		$zig_result['return'] = 1 ;

		if(!$update_listing)
		{
		// -- Start Filter
			$filter_parameters = array
			(
				'function'	=>	'filters',
				'filters'	=>	$filters,
				'sql'		=>	$filter_sql,
				'dig'		=>	$dig,
				'exclude'	=>	$exclude,
				'gui_buffer'=>	0,
				'return'	=>	3
			) ;
			$zig_result = zig($filter_parameters) ;
			$zig_filter['value'] = $zig_result['value'] ;

			if($zig_keyword)
			{
				$zig_filter['config']['zig_keyword'] = $zig_keyword ;
			}
			if($zig_attach)
			{
				$zig_filter['config']['attach'] = $zig_attach ;
			}
			$zig_filter['config']['function'] = "attach" ;
			$zig_attach = zig($zig_filter['config']) ;
			$where = $zig_filter['value'] ? " WHERE ".$zig_filter['value'] : NULL ;
		// -- End Filter
		}

		if($zig_keyword or $where or zig("checkArray",$_POST,"zig_basic_search") or zig("checkArray",$_POST,"zig_current_page") or $trigger_list)
		{
			if($zig_keyword)
			{
				unset($where) ;
				$splitted_sql = explode(" from ",$sql) ;
				$splitted_sql[1] = trim($splitted_sql[1]) ;
				$splitted_sql = explode(" ",$splitted_sql[1]) ;
				$splitted_sql[0] = trim($splitted_sql[0]) ;
				$splitted_tables = explode(",",$splitted_sql[0]) ;
				$where = zig("where",$splitted_tables,$zig_keyword) ;
				if($dig)
				{

				foreach($splitted_tables as $parent_table)
				{
					// Start remove the database name on the table
					$semi_stripped_parent_table = str_replace($zig_global_database.".","",$parent_table) ;
					// End remove the database name on the table

					// Start stripped table name
					$stripped_parent_table = str_replace($pre,"",$semi_stripped_parent_table) ;
					// End stripped table name
					$child_table_sql = "SELECT `child_table` FROM `zig_relationships` WHERE (`parent_table`='$parent_table' OR `parent_table`='$semi_stripped_parent_table' OR `parent_table`='$stripped_parent_table' OR `parent_table`='all tables' OR `child_table`='all tables') AND `child_table`<>'' AND zig_status<>'deleted'" ;
					$child_table_result = zig("query",$child_table_sql) ;
					while($child_table_fetch = $child_table_result->fetchRow())
					{
						$child_table = $child_table_fetch['child_table'] ;
						// Start remove the database name on the table
						$semi_stripped_child_table = str_replace($zig_global_database.".","",$child_table) ;
						// End remove the database name on the table

						// Start stripped table name
						$stripped_child_table = str_replace($pre,"",$semi_stripped_child_table) ;
						// End stripped table name

						$child_where = zig("where","${zig_global_database}.${pre}${stripped_child_table}",$zig_keyword) ;
						$child_sql = "SELECT DISTINCT `zig_parent_id` FROM `${pre}${stripped_child_table}` $child_where AND (`zig_parent_table`='$parent_table' OR `zig_parent_table`='$semi_stripped_parent_table' OR `zig_parent_table`='$stripped_parent_table' OR `zig_parent_table`='' OR `zig_parent_table` IS NULL) AND `zig_parent_id`<>'' AND `zig_status`<>'deleted'" ;
						$child_result = zig("query",$child_sql) ;
						while($child_fetch=$child_result->fetchRow())
						{
							$child_id_where = $child_id_where ? $child_id_where." OR `id`='$child_fetch[zig_parent_id]'" : "`id`='$child_fetch[zig_parent_id]'" ;
						}
					}
				}

				}
				
				$child_id_where = $child_id_where ? " ( ${child_id_where} ) OR " : NULL ;
				$where = $where ? str_ireplace("where "," WHERE ( ${child_id_where} ",$where)." ) " : " WHERE ( ${child_id_where} ) " ;
			}

			if(stripos($sql," where ") and $where)
			{
				$sql = str_ireplace(" where "," $where AND ",$sql) ;
			}
			else if($where)
			{
				$sql.= $where ;
			}

			$search_sql = $zig_keyword ? $search_sql : $sql ;
			$listing_parameters = array
			(
				'function'		=>	'listing',
				'module'		=>	$module,
				'table'			=>	$table,
				'sql'			=>	$sql,
				'search_sql'	=>	$search_sql,
				'zig_keyword'	=>	$zig_keyword,
				'page'			=>	$page,
				'trigger'		=>	$trigger,
				'summary'		=>	$summary,
				'addLink'		=>	$addLink,
				'zigjax'		=>	$zigjax,
				'uniqueString'	=>	$uniqueString,
				'return'		=>	3
			) ;

			if($unserialize)
			{
				$listing_parameters['unserialize'] = $unserialize ;
			}

			if($row_limit)
			{
				$zig_row_limit = $listing_parameters['row_limit'] = $row_limit ;
			}
			else
			{
				$zig_row_limit = zig("config","row_limit") ;
			}

			$zig_listing = zig($listing_parameters) ;
			switch(zig("checkArray",$zig_result,"buffer")=="" or $zig_listing['row_total']==0)
			{
				case true:
				{
					$zig_result['buffer'] = str_replace("{display}","none",$zig_listing['value']) ;
				}
				default:
				{
					$zig_result['buffer'] = str_replace("{display}","block",$zig_result['buffer']) ;
					$zig_result['buffer'] = str_replace("{uniqueString}",$uniqueString,$zig_result['buffer']) ;
					$zig_result['buffer'] = str_replace("{zig_keyword}",$zig_keyword<>"" ? htmlspecialchars($zig_keyword,ENT_QUOTES) : "Search Records",$zig_result['buffer']) ;
					$zig_hash_sql = str_replace(",","{comma}",$sql) ;
					$zig_hash_sql = str_replace("=","{equal}",$zig_hash_sql) ;
					$zig_hash = "function=search,sql=${zig_hash_sql},table=${table},row_limit=${row_limit},view_link=${view_link},
									summary=${summary},dig=${dig},trigger=${trigger},update_listing=1,zigjax=1,uniqueString=${uniqueString}" ;
					$zig_hash = zig("hash","encrypt",$zig_hash) ;
					$zig_result['buffer'] = str_replace("{zig_hash}",$zig_hash,$zig_result['buffer']) ;
					$zig_result['value'] = str_replace("{div_zig_listing}",$zig_listing['value'],$zig_result['buffer']) ;
				}
			}
			unset($zig_result['buffer']) ;
		}

		return $zig_result ;
	}
}

?>