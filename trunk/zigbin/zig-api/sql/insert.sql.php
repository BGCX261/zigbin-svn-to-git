<?php

class zig_insert
{
	function insert($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
			$fields = $arg2 ;
			$values = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$fields = array_key_exists("fields",$parameters) ? $parameters['fields'] : NULL ;
			$values = array_key_exists("values",$parameters) ? $parameters['values'] : NULL ;
		}
		
		$sql = "INSERT INTO $table ( $fields ) VALUES( $values )" ;
		$zig_result['value'] = zig("query",$sql) ;
		$zig_result['return'] = 1 ;

		return $zig_result ;
	}
}

?>