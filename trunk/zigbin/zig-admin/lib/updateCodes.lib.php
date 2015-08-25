<?php

class zig_updateCodes {
	function updateCodes($parameters,$arg1='',$arg2='',$arg3='') {
		$mode = "pull" ;
		$shellResult = "" ;
		if($arg1 or $arg2 or $arg3) {
			$mode = $arg1 ? $arg1 : $mode ;
		}
		if(is_array($parameters)) {
			$mode = array_key_exists("mode",$parameters) ? $parameters['mode'] : $mode ;
		}
		switch($mode) {
			case "push": {
				break ;
			}
			default: {
				set_time_limit(60) ;
				$shellResult.= shell_exec("svn up ../") ;
				$directories = zig("dbTableApplications","getApplicationDirectories") ;
				foreach($directories as $directory) {
					switch(substr($directory,0,4)<>"zig-") {
						case true: {
							set_time_limit(60) ;
							$shellResult.= shell_exec("cd ../${directory}") ;
							$shellResult.= shell_exec("git ${mode}") ;
						}
					}
				}
			}
		}
		$zigReturn['value'] = $shellResult ;
		return $zigReturn ;
	}
}

?>