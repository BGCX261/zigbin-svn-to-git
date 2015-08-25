<?php

class zig_describe_column
{
	function describe_column($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$updates_columns_fetch = $arg1 ;
		}
		if(is_array($parameters))
		{
			$updates_columns_fetch = $parameters['updates_columns_fetch'] ;
		}
		$sql = "`$updates_columns_fetch[Field]` ".$updates_columns_fetch['Type'] ;
		if($updates_columns_fetch['Collation'])
		{
			$splitted_collation = explode("_",$updates_columns_fetch['Collation']) ;
			$sql.= " CHARACTER SET $splitted_collation[0] COLLATE $updates_columns_fetch[Collation] " ;
		}
		$sql.= $updates_columns_fetch['Null']=="YES" ? " NULL " : " NOT NULL " ;
		if($updates_columns_fetch['Default']<>"")
		{
			switch($updates_columns_fetch['Default'])
			{
				case "CURRENT_TIMESTAMP":
				{
					$sql.= " DEFAULT ".addslashes($updates_columns_fetch['Default']) ;
					break ;
				}
				default:
				{
					$sql.= " DEFAULT '".addslashes($updates_columns_fetch['Default'])."' " ;
					break ;
				}
			}
		}
		$sql.= $updates_columns_fetch['Comment'] ? " COMMENT '".addslashes($updates_columns_fetch['Comment'])."' " : NULL ;
		$sql.= $updates_columns_fetch['Extra'] ? " ".addslashes($updates_columns_fetch['Extra'])." " : NULL ;

		$zig_return['return'] = 1 ;
		$zig_return['value'] = $sql ;
		
		return $zig_return ;
	}
}

?>