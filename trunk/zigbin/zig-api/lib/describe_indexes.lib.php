<?php

class zig_describe_indexes
{
	function describe_indexes($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
		}
		if(is_array($parameters))
		{
			$table = $parameters['table'] ;
		}
		$result = zig("show_index",$table) ;
		$fetch_all = $result->GetArray() ;
		$distinct_key_names = array() ;
		foreach($fetch_all as $fetch)
		{
			if(!array_key_exists($fetch['Key_name'],$distinct_key_names))
			{
				$distinct_key_names[$fetch['Key_name']] = $fetch['Non_unique'] ;
			}
		}
		$sql = NULL ;
		foreach($distinct_key_names as $key_name=>$non_unique)
		{
			$sql.= $sql ? ",\n" : $sql ;
			$columns = NULL ;
			foreach($fetch_all as $fetch)
			{
				if($key_name==$fetch['Key_name'])
				{
					$columns.= $columns ? "," : $columns ;
					$columns.= "`".$fetch['Column_name']."`" ;
				}
			}
			switch($key_name)
			{
				case "PRIMARY":
				{
					$sql.= "PRIMARY KEY (${columns})" ;
					break ;
				}
				default:
				{
					switch($non_unique)
					{
						case 1:
						{
							$sql.= "KEY " ;
							break ;
						}
						default:
						{
							$sql.= "UNIQUE KEY " ;
							break ;
						}
					}
					$sql.= "`".$key_name."` (${columns})" ;
				}
			}
		}
		$zig_return['return'] = 1 ;
		$zig_return['value'] = $sql ;

		return $zig_return ;
	}
}

?>