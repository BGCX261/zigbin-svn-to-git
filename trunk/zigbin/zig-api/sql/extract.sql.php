<?php

class zig_extract
{
    function extract($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$sql = $arg1 ;
		}
		else if(is_array($parameters))
		{
			$sql = array_key_exists("sql",$parameters) ? $parameters['sql'] : NULL ;
		}
		$sql = strtolower($sql) ;
		$extract_sql = explode("select ",$sql) ;
	    $extract_sql = explode(" from ",$extract_sql[1]) ;
		$extract_sql2 = explode(" where ",$extract_sql[1]) ;
		$extract_sql3 = explode(" group by ",$extract_sql[1]) ;
		$extract_sql4 = explode(" order by ",$extract_sql[1]) ;
		$extract_sql5 = explode(" limit ",$extract_sql[1]) ;
	    $fields1 = $extract_sql[0] ;
	    $fields = str_replace("`","",$fields1) ;
		$extract_sql = explode(" ",$extract_sql[1]) ;
		$table = $extract_sql[0] ;
		$extract_sql2 = explode(" ",$extract_sql2[1]) ;
		$condition = $extract_sql2[0] ;
		$extract_sql3 = explode(" ",$extract_sql3[1]) ;
		$group = $extract_sql3[0] ;
		$extract_sql4 = explode(" ",$extract_sql4[1]) ;
		$order = $extract_sql4[0] ;
		$extract_sql5 = explode(" ",$extract_sql5[1]) ;
		$limit = $extract_sql5[0] ;

	//echo "<br>field(s) = $fields <br>table(s) = $table <br>condition(s) = $condition <br>group = $group <br>order = $order <br>limit = $limit";
	$zig_result['return'] = 1 ;
	return $zig_result ;
	}
}

?>