<?php

class zig_display
{
	function display($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$content = $arg1 ;
			$function = $arg2 ;
			$load_point = $arg3 ? $arg3 : "all" ;
		}
		else if(is_array($parameters))
		{
			$print_view = array_key_exists("print_view",$parameters) ? $parameters['print_view'] : NULL ;
			$content = array_key_exists("buffer",$parameters) ? $parameters['buffer'] : NULL ;
			$topmenu = array_key_exists("topmenu",$parameters) ? $parameters['topmenu'] : NULL ;
			$applications = array_key_exists("applications",$parameters) ? $parameters['applications'] : NULL ;
			$actions = array_key_exists("actions",$parameters) ? $parameters['actions'] : NULL ;
			$actions_exclude = array_key_exists("actions_exclude",$parameters) ? $parameters['actions_exclude'] : NULL ;
			$tabs = array_key_exists("tabs",$parameters) ? $parameters['tabs'] : NULL ;
			$trigger = array_key_exists("trigger",$parameters) ? $parameters['trigger'] : NULL ;
			$footer = array_key_exists("footer",$parameters) ? $parameters['footer'] : NULL ;
			$function = array_key_exists("function",$parameters) ? $parameters['function'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
			$messenger = array_key_exists("messenger",$parameters) ? $parameters['messenger'] : NULL ;
			$zigjax = array_key_exists("zigjax",$parameters) ? $parameters['zigjax'] : NULL ;
			if($messenger)
			{
				$messenger_parameters = $parameters ;
				$messenger_parameters['function'] = "messenger" ;
			}
			$load_point = array_key_exists("load_point",$parameters) ? $parameters['load_point'] : "all" ;
			$jscript_events = array_key_exists("jscript_events",$parameters) ? $parameters['jscript_events'] : NULL ;
		}
		$buffer = "" ;
		if($print_view)
		{
			$buffer = $parameters['print_sub_header'] ? zig("print_view","print_sub_header").$content : $content ;
			$buffer = $parameters['print_header'] ? zig("print_view","print_header").$content : $content ;
			$buffer.= $parameters['print_sub_footer'] ? zig("print_sub_footer",$id) : NULL ;
			$buffer.= $parameters['print_footer'] ? zig("print_view","print_footer") : NULL ;
		}
		else
		{
			switch($load_point)
			{
				case "all":
				{
					$buffer = zig("template","file","display") ;
					$buffer = $topmenu ? str_replace("{topmenu}",zig("topmenu"),$buffer) : str_replace("{topmenu}","",$buffer) ;
				}
				case "applications":
				{
					$buffer = $buffer ? $buffer : zig("template","block","display","applications") ;
					$buffer = $applications ? str_replace("{applications}",zig("applications"),$buffer) : str_replace("{applications}","",$buffer) ;
				}
				case "tabs":
				{
					$buffer = $buffer ? $buffer : zig("template","block","display","tabs") ;
					$buffer = $tabs ? str_replace("{tabs}",zig("tabs"),$buffer) : str_replace("{tabs}","",$buffer) ;
				}
				case "content":
				case "actions":
				{
					$buffer = $buffer ? $buffer : zig("template","block","display","content") ;
					//$buffer = $actions ? str_replace("{actions}",zig("actions",$function,$id,$actions_exclude),$buffer) : str_replace("{actions}","",$buffer) ;
					//$buffer = ($messenger and $actions) ? str_replace("{message}",zig($messenger_parameters),$buffer) : ($messenger ? str_replace("{content}",zig($messenger_parameters)."{content}",$buffer) : str_replace("{message}","",$buffer)) ;
					$buffer = $messenger ? str_replace("{message}",zig($messenger_parameters),$buffer) : str_replace("{message}","",$buffer) ;
					//$content.= $trigger ? zig("trigger",$function,$id) : NULL ;
					$content.= $footer ? zig("footer") : NULL ;
					$buffer = str_replace("{content}",$content,$buffer) ;
				}
			}
		}

		if($print_view or $load_point=="all")
		{
			$buffer = str_replace("{buffer}",$buffer,zig("wrapper","{buffer}",$function,$jscript_events)) ;
		}

		// -- Start print final built html
		print $buffer ;
		// -- End print final built html
	}
}

?>