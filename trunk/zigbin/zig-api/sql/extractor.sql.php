<?php

class zig_extractor
{
    function extractor($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$method = $arg1 ;
			$value = trim($arg2) ;
			$fieldName = $arg3 ;
		}
		if(is_array($parameters))
		{
			$method = array_key_exists("method",$parameters) ? $parameters['method'] : $arg1 ;
			$value = array_key_exists("value",$parameters) ? trim($parameters['value']) : $value ;
			$fieldName = array_key_exists("fieldName",$parameters) ? $parameters['fieldName'] : $arg3 ;
		}

		$zig_result['value'] = $fieldName ? $this->$method(trim($value),$fieldName) : $this->$method(trim($value)) ;
		$zig_result['return'] = 1 ;
		return $zig_result ;
	}

	function extract_addField($sql,$fieldName)
	{
		switch($this->extract_inFields($sql,$fieldName))
		{
			case false:
			{
				$sql = strtolower($sql) ;
				switch(stripos($sql," distinct "))
				{
					case false:
					{
						$sql = preg_replace('/select /',"select ${fieldName},",$sql,1) ;
						break ;
					}
					default:
					{
						$sql = preg_replace('/select distinct /',"select distinct ${fieldName},",$sql,1) ;
						break ;
					}
				}
			}
		}
		return $sql ;
	}

	function extract_inFields($sql,$fieldName)
	{
		$zig_return = $this->extract_sql($sql) ;
		return in_array($fieldName,$zig_return['fields_array']) ? true : false ;
	}

	function extract_sql($sql)
	{
		$sql = strtolower($sql) ;
		switch(stripos($sql," distinct "))
		{
			case false:
			{
				$extract_sql = explode("select ",$sql) ;
				break ;
			}
			default:
			{
				$extract_sql = explode("select distinct ",$sql) ;
				break ;
			}
		}

	    $extract_sql = explode(" from ",$extract_sql[1]) ;
		$extract_sql2 = explode(" where ",$extract_sql[1]) ;
		$extract_sql3 = explode(" group by ",$extract_sql[1]) ;
		$extract_sql4 = explode(" order by ",$extract_sql[1]) ;
		$extract_sql5 = explode(" limit ",$extract_sql[1]) ;

	    $zig_return['fields'] = $extract_sql[0] ;
	    $zig_return['fields'] = str_replace("`","",$zig_return['fields']) ;
	    $zig_return['fields'] = str_replace(" ","",$zig_return['fields']) ;
		$zig_return['fields_array'] = explode(",",$zig_return['fields']) ;
		$extract_sql = explode(" ",$extract_sql[1]) ;
		$zig_return['table'] = $extract_sql[0] ;
		$extract_sql2 = explode(" ",$extract_sql2[1]) ;
		$condition = $extract_sql2[0] ;
		$extract_sql3 = explode(" ",isset($extract_sql3[1]) ? $extract_sql3[1] : "") ;
		$group = $extract_sql3[0] ;
		$extract_sql4 = explode(" ",isset($extract_sql4[1]) ? $extract_sql4[1] : "") ;
		$order = $extract_sql4[0] ;
		$extract_sql5 = explode(" ",isset($extract_sql5[1]) ? $extract_sql5[1] : "") ;
		$limit = $extract_sql5[0] ;

		return $zig_return ;
	}

	function extract_type($type)
	{
		$splitted_type = explode(" ",$type) ;
		$attribute = isset($splitted_type[1]) ? $splitted_type[1] : '' ;
		$splitted_type[0] = str_replace("("," ",$splitted_type[0]) ;
		$splitted_size = explode(" ",$splitted_type[0]) ;
		$size = isset($splitted_size[1]) ? str_replace(")","",$splitted_size[1]) : '' ;
		$type = $splitted_size[0] ;

		$zig_return['attribute'] = $attribute ;
		$zig_return['size'] = $size ;
		$zig_return['type'] = $type ;
		return $zig_return ;
	}
}

?>