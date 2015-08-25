<?php

class zig_print_view
{
	function print_view($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$method = $arg1 ;
		}
		else if(is_array($parameters))
		{
			$method = array_key_exists("print_view",$parameters) ? $parameters['print_view'] : NULL ;
		}

		if($method)
		{
			$tab = str_replace(" ","_",strtolower(zig("info","tab"))) ;
			$action = $GLOBALS['zig']['current']['action'] ;
			$file = $tab.".".$action.".".$method ;
			$zig_result['value'] = $this->$method($file,$method) ;
		}

		$zig_result['return'] = 1 ;
		return $zig_result ;
    }
	
	function print_header($file,$method)
    {
		$buffer = zig("template","file",$file) ;
		$buffer = $buffer ? $buffer : zig("template","file","header") ;
        $header.= zig("display_header","display_header") ;
        $buffer = str_replace("{header}",$header,$buffer) ;
		return $buffer ;
    }
	
    function print_sub_header($file,$method)
    {
		$buffer = zig("template","file",$file) ;
		$buffer = $buffer ? $buffer : zig("template","file","sub_header") ;
        $subheader.= "start of print out" ;
        $buffer = str_replace("{subheader}",$subheader,$buffer);
		return $buffer ;
    }
	
    function print_sub_footer($file,$method)
	{
		$buffer = zig("print_sub_footer",$method) ;
        return $buffer ;
	}
	
    function print_footer($file,$method)
    {
		$buffer = zig("template","file",$file) ;
		$buffer = $buffer ? $buffer : zig("template","file","footer") ;
        $footer.= "Powered by zigbin" ;
        $buffer = str_replace("{footer}",$footer,$buffer) ;
        //return $buffer ;
    }
}
?>