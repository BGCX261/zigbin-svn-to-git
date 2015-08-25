<?php

class zig_attach
{
	function attach($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$name = $arg1 ;
			$value = $arg2 ;
			$buffer.= "<input type='hidden' name='$name' value='$value' />" ;
		}
		else if(is_array($parameters))
		{
			$buffer = "" ;
			foreach($parameters as $name => $value)
			{
				$buffer.= "<input type='hidden' name='$name' value='$value' />" ;
			}
		}

		$zig_result['value'] = $buffer ;
		return $zig_result ;
	}	
}

?>