<?php

class zig_summary
{
	function summary($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$records = $arg1 ;
		}
		else if(is_array($parameters))
		{
			$records = array_key_exists('records',$parameters) ? $parameters['records'] : NULL ;
		}

		$total_records = sizeof($records) ;
		// -- Start Define Exclude List
		$exclude = array
		(
			'id',
			'zig_version',
			'zig_created',
			'zig_updated',
			'zig_status',
			'zig_weight',
			'zig_parent_table',
			'zig_parent_id'
		) ;
		// -- End Define Exclude List

	$counter = 0 ;
	$even = true ;
	foreach($records[0] as $key => $value)
	{
		if($even)
		{
			$even = false ;
			continue ;
		}
		$ripped_fields[] = $key ;
		$even = true ;
	}

	foreach($ripped_fields as $field)
	{
		if(!in_array($field,$exclude))
		{
			$sql_field = "`".str_replace(".","`.`",$field)."`" ;
			$distinct_sql = str_replace($raw_fields,$sql_field.",COUNT(*)${sum_sql}",$sql." GROUP BY ${sql_field} LIMIT 100 ") ;
			$distinct_sql = str_replace("select","SELECT DISTINCT",$distinct_sql) ;
			$distinct_result = zig("query",$distinct_sql) ;
			$distinct_records = $distinct_result->RecordCount() ;
			if($distinct_records<($total_records*0.30) and $distinct_records>1 and $distinct_records<100)
			{
				$buffer.= zig("template","block","summary","summary labels") ;
				$titlecase_field = str_replace("_"," ",$field) ;
				$titlecase_field = ucwords(trim($titlecase_field)) ;
				$buffer = str_replace("{category}",$titlecase_field,$buffer) ;

				while($distinct_fetch=$distinct_result->fetchRow())
				{
					switch($distinct_fetch[$field])
					{
						case NULL:
						case "":
						{
							if($blank_flag)
							{
								$distinct_fetch['COUNT(*)']+= $blank_count ;
								continue ;
							}
							$buffer.= zig("template","block","summary","summary row") ;
							$buffer = str_replace("{records}","{blank_count}",$buffer) ;
							$blank_count = $distinct_fetch['COUNT(*)'] ;
							$category = "[blank]" ;
							$blank_flag = true ;
							break ;
						}
						default:
						{
							if(substr_count($distinct_fetch[$field]," ")==strlen($distinct_fetch[$field]))
							{
								$category = "[space]" ;
							}
							else
							{
								$category = $distinct_fetch[$field] ;
							}
							$buffer.= zig("template","block","summary","summary row") ;
							$buffer = str_replace("{records}",number_format($distinct_fetch['COUNT(*)']),$buffer) ;
							break ;
						}
					}
					$buffer = str_replace("{category}",$category,$buffer) ;
					$saved_records[$field][$category] = $distinct_fetch['COUNT(*)'] ;
					$buffer = str_replace("{chart}","{".$field."_chart}",$buffer) ;
					$buffer = str_replace("{norec}","{".$field."_norec}",$buffer) ;
					
					if(is_array($total_fields))
					{
						foreach($total_fields as $total_field)
						{
							$total_row.= zig("template","block","summary","summary total row") ;
							$total_row = str_replace("{total}",number_format($distinct_fetch["total_${total_field}"],2),$total_row) ;
						}
					}
					$buffer = str_replace("{total_row}",$total_row,$buffer) ;
					unset($total_row) ;
				}
				$buffer = str_replace("{blank_count}",number_format($blank_count),$buffer) ;
				unset($blank_count,$blank_flag) ;
			}
		}
	}

		if($saved_records)
		{
			foreach($saved_records as $category => $items)
			{
				foreach($items as $item_name => $number_of_records)
				{
			    	$labels.= $item_name ? "|".$item_name : $item_name ;
					$number_of_records = round(($number_of_records/$total_records)*100,2) ;
					$pie.= $number_of_records ? ",".$number_of_records : $number_of_records ;
					$norec = sizeof($items) + 1 ;
				}
	
				$image = "<img src='http://chart.apis.google.com/chart?cht=p3&chd=t:0$pie&chs=500x100&chl=$labels'  />";
				$buffer = str_replace("{".$category."_chart}",$image,$buffer) ;
				$buffer = str_replace("{".$category."_norec}",$norec,$buffer) ;
				unset($labels,$pie) ;
			}
	
			$buffer = $buffer ? zig("template","block","summary","summary subheader").$buffer.zig("template","block","summary","summary subfooter") : NULL ;
			$buffer = $total_flag ? str_replace("{total_label}","Total",$buffer) : str_replace("{total_label}","",$buffer) ;
			$buffer = str_replace("{total_subheader}",$total_subheader,$buffer) ;
			$buffer = $total_subheader ? str_replace("{column_span}","3",$buffer) : str_replace("{column_span}","2",$buffer) ;
		}

		if($buffer)
		{
			$buffer = zig("template","block","summary","summary header").$buffer.zig("template","block","summary","summary footer") ;

			// -- Start fieldset
			$buffer = str_replace("{zig_invisible_class}","zig_invisible",$buffer) ;
			$buffer = str_replace("{zig_fieldset_collapsed_class}","zig_fieldset_collapsed",$buffer) ;
			$escaped_sql = str_replace(",","{comma}",$sql) ;
			$escaped_sql = str_replace("=","{equal}",$escaped_sql) ;
			$zig_hash_parameters = "function=summary,sql=${escaped_sql},total_records=${total_records},zigjax=1" ;
			$zig_hash_parameters = zig("hash","encrypt",$zig_hash_parameters) ;
			$buffer = str_replace("{zig_hash_parameters}",$zig_hash_parameters,$buffer) ;
			// -- End fieldset
		}
		else
		{
			$buffer = "Sorry, record(s) are insufficient to be summarized" ;
		}

		$zig_result['value'] = $buffer ;
		$zig_result['return'] = 1 ;

		return $zig_result ;
	}
}

?>