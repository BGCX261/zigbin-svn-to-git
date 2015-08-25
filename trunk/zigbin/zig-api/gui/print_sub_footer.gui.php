<?php

class zig_print_sub_footer
{
	function print_sub_footer($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$id = $arg1 ;
		}
		if(is_array($parameters))
		{
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : $arg1 ;
		}

		$tab = str_replace(" ","_",strtolower(zig("info","tab"))) ;
		$action = $GLOBALS['zig']['current']['action'] ;
		$file = $tab.".".$action.".print_sub_footer" ;
		$buffer = zig("template","file",$file) ;
		$buffer = $buffer ? $buffer : zig("template","file","sub_footer") ;
    	$subfooter.= "end of print out" ;
		$zig_result['value'] = str_replace("{subfooter}",$subfooter,$buffer);

		$zig_result['return'] = 1 ;
		return $zig_result ;
    }
}
?>