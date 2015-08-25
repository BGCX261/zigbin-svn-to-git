<?php

class zig_fetchcol
{
	function fetchcol($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$result = $arg1 ;
		}
		else if(is_array($parameters))
		{
			$result = array_key_exists("result",$parameters) ? $parameters['result'] : NULL ;
		}

		if($result)
		{
			$fields = array() ;
			$columns = get_object_vars($result) ;
			$record_count = $result->RecordCount() ;

			if($record_count>0)
			{
				$flag = false ;
				foreach($columns['fields'] as $key=>$values)
				{
					$flag = $flag ? false : true ;
					if(!$flag)
					{
						$fields[] = $key ;
					}
				}
			}
			$zig_result['value'] = $fields ;
		}

		$zig_result['return'] = 1 ;		
		return $zig_result ;
	}
}

?>