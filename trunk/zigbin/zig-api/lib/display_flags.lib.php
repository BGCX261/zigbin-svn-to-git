<?php

class zig_display_flags
{
	function display_flags($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$zig_result = $arg1 ;
			$parameters = $arg2 ;
		}
		else if(is_array($parameters))
		{
			$zig_result = array_key_exists("zig_result",$parameters) ? $parameters['zig_result'] : NULL ;
			$parameters = array_key_exists("parameters",$parameters) ? $parameters['parameters'] : NULL ;
		}
			$zig_gui_flags[] = "topmenu" ;
			$zig_gui_flags[] = "header" ;
			$zig_gui_flags[] = "applications" ;
			$zig_gui_flags[] = "actions" ;
			$zig_gui_flags[] = "tabs" ;
			$zig_gui_flags[] = "messenger" ;
			$zig_gui_flags[] = "side_dock" ;
			$zig_gui_flags[] = "trigger" ;
			$zig_gui_flags[] = "footer" ;
			$zig_gui_flags[] = "print_view" ;
			$zig_gui_flags[] = "print_header" ;
			$zig_gui_flags[] = "print_sub_header" ;
			$zig_gui_flags[] = "print_sub_footer" ;
			$zig_gui_flags[] = "print_footer" ;

			foreach($zig_gui_flags as $flag)
			{
				if(!isset($zig_result[$flag]) and is_array($zig_result))
				{
					$module_config = zig("config",$flag,$GLOBALS['zig']['current']['module']) ;
					$zig_result[$flag] = (isset($parameters[$flag]) and is_array($parameters)) ? $parameters[$flag] : ($module_config<>NULL ? $module_config : zig("config",$flag)) ;
				}
			}

			$zig_return['return'] = 1 ;
			$zig_return['value'] = $zig_result ;
			return $zig_return ;
	}
}
?>