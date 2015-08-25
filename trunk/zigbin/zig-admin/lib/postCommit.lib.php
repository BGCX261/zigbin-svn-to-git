<?php

class zig_postCommit
{
	function postCommit($parameters,$arg1,$arg2,$arg3)
	{
		$tab = "applications" ;
		$action = "Add" ;
		if($arg1 or $arg2 or $arg3)
		{
			$tab = $arg1 ? $arg1 : $tab ;
			$action = $arg2 ? $arg2 : $action ;
		}
		if(is_array($parameters))
		{
			$tab = array_key_exists("tab",$parameters) ? $parameters['tab'] : $tab ;
			$action = array_key_exists("action",$parameters) ? $parameters['action'] : $action ;
		}

		$zig_return['value'] = $this->$tab$action() ;
		$zig_return['return'] = 1 ;
		return $zig_return ;
	}

	function applicationsAdd()
	{
		return true ;
	}

	function applicationsDelete()
	{
		return true ;
	}
}

?>