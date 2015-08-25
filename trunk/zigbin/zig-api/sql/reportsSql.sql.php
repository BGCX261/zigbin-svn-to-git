<?php

class zig_reportsSql
{
	function reportsSql($parameters,$arg1='',$arg2='',$arg3='')
	{
		$applicationName = "" ;
		$method = "reportNamesSql" ;
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

		$zigReturn['value'] = $this->$method($applicationName) ;
		return $zigReturn ;
	}

	function reportNamesSql($applicationName)
	{
		return "SELECT `report_name` FROM `zig_reports` WHERE `application`='${applicationName}'" ;
	}

	function countReports($applicationName)
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `zig_reports` WHERE `application`='${applicationName}'" ;
		$result = zig("query",$sql) ;
		$fetch = $result->fetchRow() ;
		return $fetch['count'] ;
	}

	function getReportName($applicationName)
	{
		$sql = "SELECT `report_name` FROM `zig_reports` WHERE `application`='${applicationName}' LIMIT 1" ;
		$result = zig("query",$sql) ;
		$fetch = $result->fetchRow() ;
		return $fetch['report_name'] ;
	}
}

?>