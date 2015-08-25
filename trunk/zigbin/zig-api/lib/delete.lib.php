<?php

class zig_delete {
	function delete($parameters,$arg1='',$arg2='',$arg3='') {
		$module = $zigReturn = "" ;
		if($arg1 or $arg2 or $arg3) {
			$table = $arg1 ;
			$id = $arg2 ;
			$reloadParameters = $arg3 ;
		}
		if(is_array($parameters)) {
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : $arg1 ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : $arg2 ;
			$reloadParameters = array_key_exists("reloadParameters",$parameters) ? $parameters['reloadParameters'] : $arg3 ;
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : $module ;
		}

		// -- Start Custom Validation
		$customValidationParameters = array(
			"function"		=> "customField",
			"module"		=> $module,
			"table"			=> $table,
			"mode"			=> "delete", 
			"method"		=> "validation"
		) ;
		// -- End Custom Validation

		$explodedIds = explode(",", $id) ;
		$zigReturn['message'] = "" ;
		foreach($explodedIds as $singleId) {
			$customValidationParameters['id'] = $singleId ;
			$customValidation = zig($customValidationParameters) ;
			if(array_key_exists("validation", $customValidation)) {
				switch($customValidation['validation']) {
					case true: {
						zig("trash",$table,$singleId) ;
						break ;
					}
					default: {
						$zigReturn['message'].= $customValidation['message'] ;
					}
				}
			} 
			else {
				zig("trash",$table,$id) ;
				break ;
			}
		}

		//$decodedParameters = zig("hash","vars_decode",$reloadParameters) ;
		//$zig_result['value'] = zig($decodedParameters) ;
		$zig_result['value']['data'] = $zigReturn ;

		return $zig_result ;
	}
}

?>