<?php

class zig_data_size
{
	function data_size($parameters,$arg1='',$arg2='',$arg3='')
	{
		$type = "" ;
		if($arg1 or $arg2 or $arg3)
		{
			$type = $arg1 ;
		}
		if(is_array($parameters))
		{
			$type = array_key_exists("type",$parameters) ? $parameters['type'] : NULL ;
		}

		$splitted_type = explode(" ",$type) ;
		$type = isset($splitted_type[1]) ? $splitted_type[1] : '' ;
		$splitted_type[0] = str_replace("("," ",$splitted_type[0]) ;
		$splitted_size = explode(" ",$splitted_type[0]) ;
		$size = isset($splitted_size[1]) ? str_replace(")","",$splitted_size[1]) : '' ;

		if(!$size)
		{
			switch($type)
			{
			    case "tinytext":
					$size = 255 ;
					break ;
					
				case "text":
				    $size = 65535 ;
	    	 		break ;
					
				case "mediumtext":
					$size = 16777215 ;
					break ;

			    case "longtext":
					$size = 4294967295 ;
	    	        break ;

				case "tinyblob":
					$size = 255;
					break ;

				case "blob":
					$size = 65535;
					break ;
					
				case "mediumblob":
					$size = 16777215 ;
					break ;		

				case "longblob":
					$size = 4294967295 ;
					break ;
			}
		}

		$zig_return['return'] = 1 ;
		$zig_return['value'] = $size ;
		return $zig_return ;
	}
}

?>