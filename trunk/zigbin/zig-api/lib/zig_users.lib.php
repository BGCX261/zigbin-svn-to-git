<?php

class zig_zig_users {
	function zig_users($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL) {
		$mode = "add" ;
		$method = "getFieldInfo" ;
		if($arg1 or $arg2 or $arg3) {
			$method = $arg1 ;
			$mode = $arg2 ;
		}
		if(is_array($parameters)) {
			$method = array_key_exists("method", $parameters) ? $parameters['method'] : $method ;
			$mode = array_key_exists("mode", $parameters) ? $parameters['mode'] : $mode ;
		}
		else {
			$parameters['method'] = $method ;
			$parameters['mode'] = $mode ;
		}
		$zigReturn['value'] = $this->$method($parameters) ;
		return $zigReturn ;
	}

	function getFieldInfo($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL) {
		if($arg1 or $arg2 or $arg3) {
			$mode = $arg1 ;
		}
		if(is_array($parameters)) {
			$mode = array_key_exists("mode", $parameters) ? $parameters['mode'] : $mode ;
		}
		$fieldInfo = array(
			"password"	=>	$this->password()
		) ;
		return $fieldInfo ;
	}

	function password() {
		$fieldInfo = array(
			"field_type"	=> "password",
			"attribute"		=> "password"
		) ;
		return $fieldInfo ;
	}
}

?>