<?php

class zig_editRecord {
	function editRecord($parameters,$arg1='',$arg2='',$arg3='') {
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
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : 0 ;
		}

		$saveParameters = array(
			"function"		=>	"save",
			"table"			=>	$table,
			"parent_table"	=>	$parentTable,
			"parent_id"		=>	$parentId,
			"mode"			=>	"edit",
			"id"			=>	$id,
			"uniqueString"	=>	$uniqueString
		) ;
		$returnArray['data'] = zig($saveParameters) ;
		switch($sql<>"") {
			case true: {
				$listingParameters = array(
					"function"		=>	"listing",
					"sql"			=>	$sql,
					"table"			=>	$table,
					"uniqueString"	=>	$uniqueString,
					"zigjax"		=>	1
				) ;
				$returnArray['html'] = zig($listingParameters) ;
			}
		}
		$zig_result['value'] = json_encode($returnArray) ; ;
		$zig_result['return'] = 1 ;

		return $zig_result ;
	}
}

?>