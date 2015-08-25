<?php
	require_once("../zig-api/zigbin.php") ;
	switch($_SERVER['REQUEST_METHOD'])
	{
		case "GET" ;
		{
			$requestVariable = $_GET ;
			$secondaryArray = $_POST ;
			break ;
		}
		default:
		{
			$requestVariable = $_POST ;
			$secondaryArray = $_GET ;
		}
	}
	switch(is_array($secondaryArray))
	{
		case true:
		{
			foreach($secondaryArray as $key => $value)
			{
				switch(array_key_exists($key,$requestVariable))
				{
					case false:
					{
						$requestVariable[$key] = $value ;
					}
				}
			}
		}
	}
	$zig_hash = array_key_exists("zig_hash",$requestVariable) ? $requestVariable['zig_hash'] : NULL ;
	$zig_hash = zig("hash","decrypt",$zig_hash) ;
	$arg1 = array_key_exists("arg1",$requestVariable) ? $requestVariable['arg1'] : NULL ;
	$arg2 = array_key_exists("arg2",$requestVariable) ? $requestVariable['arg2'] : NULL ;
	$arg3 = array_key_exists("arg3",$requestVariable) ? $requestVariable['arg3'] : NULL ;

	$parameters = array() ;
	foreach($requestVariable as $key => $value)
	{
		if(!array_key_exists($key,$parameters))
		{
			$parameters[$key] = $value ;
		}
	}

	if(strpos($zig_hash,"=") and strpos($zig_hash,","))
	{
		$ripped_zig_hash = explode(",",$zig_hash) ;
		foreach($ripped_zig_hash as $value)
		{
			$value = str_replace("{comma}",",",$value) ;
			$ripped_value = explode("=",$value) ;
			$ripped_value[1] = array_key_exists(1,$ripped_value) ? $ripped_value[1] : "" ;
			$parameters[trim($ripped_value[0])] = str_replace("{equal}","=",trim($ripped_value[1])) ;
		}		
		if(!array_key_exists("arg1",$parameters) and $arg1)
		{
			$parameters['arg1'] = $arg1 ;
		}
		if(!array_key_exists("arg2",$parameters) and $arg2)
		{
			$parameters['arg2'] = $arg2 ;
		}
		if(!array_key_exists("arg3",$parameters) and $arg3)
		{
			$parameters['arg3'] = $arg3 ;
		}
		if(array_key_exists("zigjax",$parameters))
		{
			if($parameters['zigjax'])
			{
				$returnResult = zig($parameters) ;
				switch(is_array($returnResult))
				{
					case true:
					{
						print_r(json_encode($returnResult)) ;
						break ;
					}
					default:
					{
						print_r($returnResult) ;
					}
				}
			}
		}
		else
		{
			zig($parameters) ;
		}
	}
	else if($zig_hash)
	{
		$ripped_zig_hash = explode(",",$zig_hash) ;
		$function = $ripped_zig_hash[0] ;
		$arg1 = isset($ripped_zig_hash[1]) ? $ripped_zig_hash[1] : $arg1 ;
		$arg2 = isset($ripped_zig_hash[2]) ? $ripped_zig_hash[2] : $arg2 ;
		$arg3 = isset($ripped_zig_hash[3]) ? $ripped_zig_hash[3] : $arg3 ;
		zig($function,$arg1,$arg2,$arg3) ;
	}

?>