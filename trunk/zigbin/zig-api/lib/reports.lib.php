<?php

class zig_reports
{
	function reports($parameters,$arg1='',$arg2='',$arg3='')
	{
		$applicationName = "" ;
		$method = "reportsLoadReports" ;
		if($arg1 or $arg2 or $arg3)
		{
			$applicationName = $arg1 ? $arg1 : $applicationName ;
			$method = $arg2 ? $arg2 : $method ;
		}
		if(is_array($parameters))
		{
			$applicationName = array_key_exists("applicationName",$parameters) ? $parameters['applicationName'] : $applicationName ;
			$method = array_key_exists("method",$parameters) ? $parameters['method'] : $method ;
		}

		$zig_result['value'] = $this->$method($applicationName) ;
		return $zig_result ;
	}
	
	function reportsLoadReports($applicationName)
	{
		switch(zig("reportsSql",$applicationName,"countReports")>1)
		{
			case true:
			{
				$userDefinedField['sql'] = zig("reportsSql",$applicationName) ;
				$userDefinedField['field_type'] = "select" ;
				$userDefinedField['option_value'] = "report_name" ;
				$fieldElementParameters = array (
					"function"			=>	"field_element",
					"table"				=>	"zig_reports",
					"mode"				=>	"add",
					"userDefinedField"	=>	$userDefinedField,
					"current_field_name"=>	"reports",
					"elementAttributes"	=>	"onchange=\"loadReportFilters('{zigHash}',this.value)\""
				) ;
				$html = zig($fieldElementParameters) ;
				$zigHash = zig("hash","encrypt","function=reports,method=reportsLoadFilters,zigjax=1") ;
				$html = str_replace("{uniqueString}",uniqid(),$html) ;
				$html = str_replace("{tableName}","zig_reports",$html) ;
				$html = str_replace("{current_field_name}","reports",$html) ;
				$html = str_replace("{zigHash}",$zigHash,$html) ;
				$html = str_replace("{reports}",$html,zig("template","block","reports","reports")) ;
				$html = str_replace("{reportFilters}","",$html) ;
				break ;
			}
			default:
			{
				$reportName = zig("reportsSql",$applicationName,"getReportName") ;
				$html = zig("template","block","reports","reportTitle") ;
				$html = str_replace("{reportName}",$reportName,$html) ;
				$html = str_replace("{reports}",$html,zig("template","block","reports","reports")) ;
				$html = str_replace("{reportFilters}",$this->reportsLoadFilters($reportName,$applicationName),$html) ;
			}
		}
		return $html.zig("footer") ;
	}
	
	function reportsLoadFilters($reportName) {
		$html = "" ;
		$mode = "add" ;
		$selectParameters = array
		(
			"function"	=>	"select",
			"table"		=>	"zig_report_filters",
			"where"		=>	"`report_name`='${reportName}'"
		) ;
		$result = zig($selectParameters) ;
		
		while($fetch=$result->fetchRow()) {
			$fieldResult = zig("show_columns",$fetch['table']) ;
			while($dbDefinedField=$fieldResult->fetchRow()) {
				if($dbDefinedField['Field']==$fetch['field']) {
					break ;
				}
			}

			$customFieldParameters = array(
				"function"		=>	"customField",
				"module"		=>	$fetch['zig_user'],
				"table"			=>	$fetch['table'],
				"method"		=>	$fetch['field'],
				"mode"			=>	$mode
			) ;
			$field_info = zig($customFieldParameters) ;

			$fieldElementParameters = array (
				"function"			=>	"field_element",
				"mode"				=>	"add",
				"table"				=>	$fetch['table'],
				"dbDefinedField"	=>	$dbDefinedField,
				"userDefinedField"	=>	$field_info
			) ;
			$filterName = str_replace(" ","_",$fetch['filter_name']) ;
			$filterElement = zig($fieldElementParameters) ;
			$filterElement = str_replace("{current_field_name}",$filterName,$filterElement) ;
			$filterElement = str_replace("{uniqueString}",uniqid(),$filterElement) ;
			$filterElement = str_replace("{tableName}",$fetch['table'],$filterElement) ;
			$filterElement = str_replace("{fieldValue}",zig("checkArray",$field_info,"defaultValue"),$filterElement) ;
			$html.= zig("template","block","reports","filter row") ;
			$html = str_replace("{filterName}",$fetch['filter_name'],$html) ;
			$html = str_replace("{filterElement}",$filterElement,$html) ;
		}
		$selectParameters = array
		(
			"function"	=>	"select",
			"fields"	=>	"file",
			"table"		=>	"zig_reports",
			"where"		=>	"`report_name`='${reportName}'",
			"limit"		=>	1
		) ;
		$result = zig($selectParameters) ;
		$fetch = $result->fetchRow() ;
		$html = $html<>"" ? zig("template","block","reports","filter header").$html.zig("template","block","reports","filter footer") : "" ;
		$html = str_replace("{filters}",$html,zig("template","block","reports","filters")) ;
		return str_replace("{file}",$fetch['file'],$html) ;
	}
}

?>