<?php

class zig_postCommit
{
	function postCommit($parameters,$arg1,$arg2,$arg3)
	{
		$zig_return['value'] = true ;
		$zig_return['return'] = 1 ;
		return $zig_return ;
	}
}

?>