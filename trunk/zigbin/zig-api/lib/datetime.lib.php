<?php

class zig_datetime
{
	function datetime($parameters,$arg1='',$arg2='',$arg3='')
	{
		$datetime = "" ;
		if($arg1 or $arg2 or $arg3)
		{
			$datetime = $arg1 ;
			$date_format = $arg2 ;
			$time_format = $arg3 ;
			$timezoned = false ;
		}
		if(is_array($parameters))
		{
			$datetime = array_key_exists("datetime",$parameters) ? $parameters['datetime'] : $datetime ;
			$date_format = array_key_exists("date_format",$parameters) ? $parameters['date_format'] : NULL ;
			$time_format = array_key_exists("time_format",$parameters) ? $parameters['time_format'] : NULL ;
			$timezoned = array_key_exists("timezoned",$parameters) ? $parameters['timezoned'] : false ;
		}

		$zig_result['return'] = 1 ;
		if(!$datetime)
		{
			$zig_result['value'] = "" ;
			return $zig_result ;
		}
		$date_format = $date_format ? $date_format : zig("config","date format") ;

		// -- Start bring datetime to normal format
		$splitted_datetime = date_parse($datetime) ;
		$datetime = date("Y-m-d",mktime($splitted_datetime['hour'],$splitted_datetime['minute'],$splitted_datetime['second'],$splitted_datetime['month'],$splitted_datetime['day'],$splitted_datetime['year'])) ;
		// -- End bring datetime to normal format

		switch($timezoned)
		{
			case true:
			{
				$datetime_info = strtotime($datetime."+".zig("config","timezone offset")."hours") ;
				$datetime = date("Y-m-d H:i:s",$datetime_info) ;
				if(preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $datetime, $matches))
				{
			        $datetime = date($date_format,mktime($matches[4],$matches[5],$matches[6],$matches[2],$matches[3],$matches[1])) ;
				}
				break ;
			}
		}

		if(preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $datetime, $matches))
		{
			if(checkdate($matches[2], $matches[3], $matches[1]))
    	   	{
				$time_format = $time_format ? $time_format : zig("config","time format") ;
		        $zig_result['value'] = date("$date_format $time_format",mktime($matches[4],$matches[5],$matches[6],$matches[2],$matches[3],$matches[1])) ;
			}
		}
		else if(preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $datetime, $matches))
		{
			if(checkdate($matches[2], $matches[3], $matches[1]))
    	   	{
    	   		$match4 = isset($matches[4]) ? $matches[4] : 0 ;
    	   		$match5 = isset($matches[5]) ? $matches[5] : 0 ;
    	   		$match6 = isset($matches[6]) ? $matches[6] : 0 ;
				$zig_result['value'] = date("${date_format}",mktime($match4,$match5,$match6,$matches[2],$matches[3],$matches[1])) ;
			}
		}
		
		return $zig_result ;
	}
}

?>