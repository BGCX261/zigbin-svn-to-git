<?php

class zig_trash
{
	function trash($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
			$id = $arg2 ;
			$method = $arg3 ? "trash_".$arg3 : "trash_delete" ;
		}
		if(is_array($parameters))
		{
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : $arg1 ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : $arg2 ;
			$method = array_key_exists("method",$parameters) ? "trash_".$parameters['method'] : ($arg3 ? $arg3 : "trash_delete") ;
		}

		$zig_result['return'] = 1 ;
		$zig_result['value'] = $this->$method($parameters,$table,$id,$method) ;

		return $zig_result ;
	}
	
	function trash_delete($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
			$id = $arg2 ;
			$method = $arg3 ? $arg3 : "trash" ;
		}
		else if(is_array($parameters))
		{
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
			$method = array_key_exists("method",$parameters) ? $parameters['method'] : "trash" ;
		}

		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;

		// Start remove the database name on the table
		$semi_stripped_table = str_replace($zig_global_database.".","",$table) ;
		// End remove the database name on the table

		// Start stripped table name
		$stripped_table = str_replace($pre,"",$semi_stripped_table) ;
		// End stripped table name

		switch($stripped_table)
		{
			case "trash":
			{
				break ;
			}
			default:
			{
				$user = zig("info","user") ;
				$fields = "`zig_created`,`zig_user`,`table_name`,`row_id`,`info`" ;
				$delete_fields_sql = "SHOW COLUMNS IN ${table}" ;
				$delete_fields_result = zig("query",$delete_fields_sql) ;
				while($delete_fields_fetch=$delete_fields_result->fetchRow())
				{
					$fieldnames[] = $delete_fields_fetch['Field'] ;
				}
		
				$delete_values_sql = "SELECT * FROM ${table} WHERE `id` IN (${id})" ;
				$delete_values_result = zig("query",$delete_values_sql) ;
				while($delete_values_fetch=$delete_values_result->fetchRow())
				{
					foreach($fieldnames as $fieldname)
					{
						$data[$fieldname] = $delete_values_fetch[$fieldname] ;
					}
					$data = addslashes(serialize($data)) ;
					$values = "NOW(),'${user}','${stripped_table}','$delete_values_fetch[id]','${data}'" ;
					$zig_trash_ids[] = zig("insert","${zig_global_database}.${pre}trash",$fields,$values) ;
					unset($data) ;
				}
			}
		}
		zig("query","DELETE FROM ${table} WHERE `id` IN (${id})") ;

		return $zig_trash_ids ;
	}
	
	function trash_empty($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL)
	{
		$zig_global_database = zig("config","global_database") ;
		$pre = zig("config","pre") ;
		$sql = "DELETE FROM `${zig_global_database}`.`${pre}trash`" ;
		zig("query",$sql) ;
		print "Trash Emptied" ;	
	}
}

?>