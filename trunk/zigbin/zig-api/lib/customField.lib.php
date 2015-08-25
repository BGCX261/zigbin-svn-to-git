<?php

class zig_customField {
	function customField($parameters,$arg1,$arg2,$arg3) {
		$module = $table = $classPath = $result = "" ;
		if($arg1 or $arg2 or $arg3) {
			$module = $arg1 ;
			$table = $arg2 ;
		}
		if(is_array($parameters)) {
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : $module ;
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : $table ;
		} else {
			$parameters['module'] = $module ;
			$parameters['table'] = $table ;
		}

		$classPath = "../${module}/lib/${table}.lib.php" ;
		switch(zig("cache","file_exists",$classPath)) {
			case true: {
				include_once($classPath) ;
				$class = "zig_".$table ;
				$customFieldObject = new $class ;
				switch(is_object($customFieldObject)) {
					case true: {
						switch(array_key_exists("method", $parameters)) {
							case true: {
								switch(method_exists($customFieldObject,$parameters['method'])) {
									case true: {
										$result = $customFieldObject->$table($parameters) ;
									}
								}
								break ;
							}
							default: {
								switch(method_exists($customFieldObject,$table)) {
									case true: {
										$result = $customFieldObject->$table($parameters) ;
									}
								}
								break ;
							}
						}
					}
				}
			}
		}
		$result = is_array($result) ? $result : array() ;
		$return['value'] = array_key_exists("value", $result) ? $result['value'] : array() ;
		return $return ;
	}
}

?>