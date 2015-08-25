<?php

class zig_update
{
	function update($parameters,$arg1='',$arg2='',$arg3='')
	{
		$update_query_fields = $limit = "" ;
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
			$set = $arg2 ;
			$where = $arg3 ;
		}
		if(is_array($parameters))
		{
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : $table ;
			$set = array_key_exists("set",$parameters) ? $parameters['set'] : $set  ;
			$where = array_key_exists("where",$parameters) ? $parameters['where'] : $where ;
			$limit = array_key_exists("limit",$parameters) ? $parameters['limit'] : $limit ;
		}

		if($table and $set)
		{
			if(is_array($set))
			{
				$counter = 0 ;
				foreach($set as $update_set)
				{
					$update_table = is_array($table) ? $table[$counter] : $table ;
					$update_where = is_array($where) ? $where[$counter] : $where ;
					$update_limit = is_array($limit) ? $limit[$counter] : $limit ;
					$update_sets = explode(",",$update_set) ;
					$update_query_fields = "" ;
					foreach($update_sets as $values)
					{
						$splitted_values = explode("=",$values) ;
						$update_fields[] = str_replace("`","",$splitted_values[0]) ;
						$update_query_fields.= $update_query_fields ? ",".$splitted_values[0] : $splitted_values[0] ;
					}
					$update_query = "SELECT id,$update_query_fields FROM $update_table $update_where $update_limit" ;
					$zig_revisions = $this->revision("revision",$update_table,$update_fields,$update_query) ;
					$sql = "UPDATE $update_table SET $update_set $update_where $update_limit " ;
					$result = zig("query",$sql,"",false) ;
					$counter++ ;
				}
			}
			else
			{
				$splitted_sets = explode("' ,`|NULL ,`",$set) ;
				foreach($splitted_sets as $values)
				{
					$splitted_values = explode("=",$values) ;
					$update_fields[] = $splitted_values[0] = trim(str_replace("`","",$splitted_values[0])) ;
					$update_query_fields.= $update_query_fields ? ",`".$splitted_values[0]."`" : "`".$splitted_values[0]."`" ;
				}
				$where = strpos(strtolower($where),"where ")===false ? "WHERE ".$where : $where ;
				$set = substr(strtolower(trim($set)),0,4)=="set " ? $set : "SET ".$set ;
				$query = "SELECT `id`,${update_query_fields} FROM ${table} ${where} ${limit}" ;
				$zig_revisions = $this->revision("revision",$table,$update_fields,$query) ;
				$sql = "UPDATE ${table} ${set} ${where} ${limit} " ;
				$result = zig("query",$sql,"",false) ;
			}
		}

		$zig_result['return'] = 1 ;
		$zig_result['value'] = $zig_revisions['value'] ;

		return $zig_result ;
	}

	function revision($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
			$fields = $arg2 ;
			$query = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$fields = array_key_exists("fields",$parameters) ? $parameters['fields'] : NULL ;
			$query = array_key_exists("query",$parameters) ? $parameters['query'] : NULL ;
		}

		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$semi_stripped_table = str_replace($zig_global_database.".","",$table) ;
		$stripped_table = str_replace($pre,"",$semi_stripped_table) ;
		$passwordFields = array() ;

		foreach($fields as $fieldname)
		{
			switch(zig("isPasswordField",$stripped_table,$fieldname))
			{
				case true:
				{
					$passwordFields[] = $fieldname ;
				}
			}
		}
		$result = zig("query",$query) ;
		while($fetch=$result->fetchRow())
		{
			unset($data) ;
			foreach($fields as $fieldname)
			{
				switch(in_array($fieldname,$passwordFields))
				{
					case true:
					{
						$fetch[$fieldname] = "[hidden]" ;
					}
				}
				$data[] = array
				(
					'fieldname'	=>	$fieldname,
					'value'		=>	$fetch[$fieldname]
				) ;
			}
			$data = addslashes(serialize($data)) ;
			$user = zig("info","user") ;
			$fields = "`zig_created`,`zig_user`,`table_name`,`row_id`,`info`" ;
			$values = "NOW(),'${user}','${table}','$fetch[id]','${data}'" ;
			$zig_revisions[] = zig("insert","${zig_global_database}.${pre}revisions",$fields,$values) ;
		}
		$zig_revisions = sizeof($zig_revisions)==1 ? $zig_revisions[0] : $zig_revisions ;
		$zig_result['value'] = $zig_revisions ;
		return $zig_result ;
	}
}

?>