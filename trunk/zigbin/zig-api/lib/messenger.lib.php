<?php

class zig_messenger
{
	function messenger($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$error = $arg1 ;
			$warning = $arg2 ;
			$system = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$error = array_key_exists("error",$parameters) ? $parameters['error'] : NULL ;
			$warning = array_key_exists("warning",$parameters) ? $parameters['warning'] : NULL ;
			$system = array_key_exists("system",$parameters) ? $parameters['system'] : NULL ;
			$application = array_key_exists("message",$parameters) ? $parameters['message'] : NULL ;
		}

		$messages = $error ? $error : "" ;
		$messages = ($messages and $warning) ? "<br />" : $messages ;
		$messages = $warning ? $messages.$warning : $messages ;
		$messages = ($messages and $system) ? "<br />" : $messages ;
		$messages = $system ? $messages.$system : $messages ;
		$messages = ($messages and $application) ? "<br />" : $messages ;
		$messages = $application ? $messages.$application : $messages ;

		$buffer = zig("template","file","messenger") ;
		$buffer = str_replace("{message}",$messages,$buffer) ;
		$zig_result['value'] = $buffer ;
		$zig_result['return'] = 1 ;
		
		return $zig_result ;
	}
}

?>