<?php

class zig_extract
{
	function extract($parameters,$arg1,$arg2,$arg3)
	{
		$sql = NULL ;
		$result = zig("show_table_status","zig") ;
		while($fetch=$result->fetchRow())
		{
			$columns_result = zig("show_full_columns",$fetch['Name']) ;
			$columns = NULL ;
			$described_columns = NULL ;
			while($column_fetch=$columns_result->fetchRow())
			{
				$described_columns.= zig("describe_column",$column_fetch).",\n" ;
				$columns.= $columns ? "," : $columns ;
				$columns.= "`".$column_fetch['Field']."`" ;
			}
			$sql.= $sql ? "\n\n" : $sql ;
			$sql.= "CREATE TABLE IF NOT EXISTS `$fetch[Name]` ( ${described_columns}" ;
			$sql.= zig("describe_indexes",$fetch[Name]) ;
			$sql.= "\n) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='zig' AUTO_INCREMENT=1001 ;" ;

			$values = NULL ;
			$values_result = zig("select_defaults",$fetch['Name']) ;
			while($values_fetch=$values_result->fetchRow())
			{
				$counter = 0 ;
				$column_values = NULL ;
				while(array_key_exists($counter,$values_fetch))
				{
					$column_values.= $column_values ? "," : $column_values ;
					$column_values.= "'".addslashes($values_fetch["${counter}"])."'" ;
					$counter++ ;
				}
				$values.= $values ? "," : $values ;
				$values.= "\n(".$column_values.")" ;
			}
			if($values)
			{
				$sql.= "\n\nINSERT INTO `$fetch[Name]`(${columns}) VALUES\n" ;
				$sql.= "${values} ;" ;
			}
		}
		$files_path = zig("config","files path") ;
		$file_name = "install.sql" ;
		$file = "${files_path}".time().".${file_name}" ;
		/*$handle = fopen($file, "w") ;
		fwrite($handle, $sql); 
		fclose($handle) ;*/
		zig("cache","fwrite",$file,$sql) ;
		header('Content-type: application/text') ;
		header("Content-Disposition: attachment; filename=$file_name") ;
		readfile($file) ;
		zig("cache","unlink",$file) ;
		exit() ;
	}
}

?>