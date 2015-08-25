<?php

class zig_get_mac
{
	function get_mac($parameters,$arg1,$arg2,$arg3)
	{
		$zig_return['return'] = 1 ;
		$arp = `which arp` ;
		$arp = $arp? rtrim($arp) : "arp" ;
		$arpTable = `$arp -n` ;
		$arpSplitted = explode("\n",$arpTable) ;
		$remoteIp = $GLOBALS['REMOTE_ADDR'] ;
		$remoteIp = str_replace(".", "\\.", $remoteIp) ;
		foreach($arpSplitted as $value)
		{
			$valueSplitted = explode(" ",$value) ;
			foreach($valueSplitted as $spLine)
			{
				if(preg_match("/$remoteIp/",$spLine))
				{
					reset($valueSplitted) ;
					foreach($valueSplitted as $spLine)
					{
						if(preg_match("/[0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f]/i",$spLine))
						{
							$zig_return['value'] = $spLine ;
							return $zig_return ;
						}
					}
				}
			}
		}
		return false ;
	}
}

?>