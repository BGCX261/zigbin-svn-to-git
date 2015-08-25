<?php

class zig_addRecord {
	function addRecord($parameters,$arg1='',$arg2='',$arg3='') {
		$uniqueString = uniqid() ;
		if($arg1 or $arg2 or $arg3) {
			$table = $arg1 ;
			$parentTable = $arg2 ;
			$parentId = $arg3 ;
		}
		if(is_array($parameters)) {
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : $arg1 ;
			$parentTable = array_key_exists("parentTable",$parameters) ? $parameters['parentTable'] : $arg2 ;
			$parentId = array_key_exists("parentId",$parameters) ? $parameters['parentId'] : $arg3 ;
			$sql = array_key_exists("sql",$parameters) ? $parameters['sql'] : NULL ;
			$uniqueString = array_key_exists("uniqueString",$parameters) ? $parameters['uniqueString'] : $uniqueString ;
		}

		$parameters['function'] = "save" ;
		$parameters['mode'] = "add" ;
		$saveResult = zig($parameters) ;
		$returnArray['data']['message'] = $saveResult['message'] ;
		$returnArray['data']['validation'] = $saveResult['validation'] ;

		if($returnArray['data']['validation']) {
			$childTables = zig("dbTableRelationships","getTableRelationships",$table) ;
			$parameters['method'] = "child" ;
			$parameters['parentTable'] = $table ;
			foreach($childTables as $childTable) {
				$parameters['table'] = $childTable ;
				$parameters['parentId'] = $saveResult['id'] ;
				$childSaveResult = zig($parameters) ;
				$returnArray['data']['message'].= array_key_exists("message",$childSaveResult) ? $childSaveResult['message'] : "" ;
				$returnArray['data']['validation'] = $returnArray['data']['validation']*$childSaveResult['validation'] ;
			}
		}

		$listingParameters = array
		(
			"function"		=>	"listing",
			"sql"			=>	$sql,
			"table"			=>	$table,
			"uniqueString"	=>	$uniqueString,
			"zigjax"		=>	1
		) ;
		$returnArray['html'] = zig($listingParameters) ;
		$zig_result['value'] = json_encode($returnArray) ;

		return $zig_result ;
	}
}

?>