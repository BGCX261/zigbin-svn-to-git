<?php

class zig_weight
{
	function weight($parameters,$arg1='',$arg2='',$arg3='')
	{
		$result = $parameters['column_result'] ;
		$exclude = $parameters['exclude'] ;
		$table = $parameters['table'] ;
		$field_info = array_key_exists("field_info", $parameters) ? $parameters['field_info'] : array() ;
		$field_info = is_array($field_info) ? $field_info : array() ;

		$counter = 0 ;
		if(is_array($exclude))
		{
			while($fetch=$result->fetchRow())
			{
				if(in_array($fetch['Field'],$exclude)) {
					continue ;
				}
				switch(array_key_exists($fetch['Field'], $field_info)) {
					case true: {
						$weight = zig("checkArray",$field_info[$fetch['Field']],"zig_weight") ? 
								($field_info[$fetch['Field']]['zig_weight']*1000) : $counter ;
						break ;
					}
					default: {
						$weight = $counter ;
					}
				}
				$fields[] = $fetch ;
				$relation[$counter] = $weight ;
				$counter++ ;
			}
		}

		if(is_array($field_info))
		{
			foreach($field_info as $key => $value)
			{
				if(zig("checkArray",$value,"attribute")=="virtual") {
					if(in_array($key,$exclude)) {
						continue ;
					}
					unset($virtual_fetch) ;
					$virtual_fetch = array
					(
						'Field'		=>	$key,
						'Type'		=>	"virtual(100)",
						'Default'	=>	"",
						'Null'		=>	"YES"
					) ;
					$fields[] = $virtual_fetch ;
					$weight = zig("checkArray",$value,"zig_weight") ? ($value['zig_weight']*1000) : $counter ;
					$relation[$counter] = $weight ;
					$counter++ ;
				}
			}
		}

		if(is_array($relation)) {
			asort($relation) ;
			foreach($relation as $key => $value)
			{
				$sorted_fields[] = $fields[$key] ;
			}
		}
	
		$zig_result['return'] = 1 ;
		$zig_result['value'] = $sorted_fields ;
		
		return $zig_result ;
	}
}