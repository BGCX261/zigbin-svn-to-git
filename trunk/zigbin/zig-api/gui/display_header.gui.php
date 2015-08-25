<?php

class zig_display_header
{
	function display_header($parameters,$arg1='',$arg2='',$arg3='')
	{
		$buffer = zig("template","file","header") ;
		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$module = $GLOBALS['zig']['current']['module'] ;

		$sql = "SELECT icon,title FROM ${pre}applications WHERE directory='$module' LIMIT 1" ;
		$result = $GLOBALS['zig']['adodb']->Execute($sql) ;
		$record = $result->RecordCount() ;
		if($record)
		{
			$fetch = $result->fetchRow() ;
			$title =  $fetch['title'] ;
			$icon = $fetch['icon'] ;
		}
		else
		{
			$title = zig("config","title","zig-api") ;
			$icon = zig("config","icon","zig-api") ;
		}

		$icon = zig("images",$icon) ;
		$buffer = str_replace("{zig_module_icon}",$icon,$buffer) ;
		$buffer = str_replace("{header}",$title,$buffer) ;
		$zig_result['value'] = $buffer ;
		$zig_result['return'] = 1 ;
		
		return $zig_result ;
	}
}

?>