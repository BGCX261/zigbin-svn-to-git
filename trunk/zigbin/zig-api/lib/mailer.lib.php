<?php

class zig_mailer
{
	function mailer($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$to = $arg1 ;
			$subject = $arg2 ;
			$message = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$to = array_key_exists("to",$parameters) ? $parameters['to'] : NULL ;
			$subject = array_key_exists("subject",$parameters) ? $parameters['subject'] : NULL ;
			$message = array_key_exists("message",$parameters) ? $parameters['message'] : NULL ;
			$headers = array_key_exists("headers",$parameters) ? $parameters['headers'] : NULL ;
			$from = array_key_exists("from",$parameters) ? $parameters['from'] : NULL ;
			$reply_to = array_key_exists("reply_to",$parameters) ? $parameters['reply_to'] : NULL ;
		}

		if(!$headers)
		{
			$headers = "MIME-Version: 1.0\r\n" ;
			$headers.= "Content-type: text/html; charset=iso-8859-1\r\n" ;
			$headers.= $from ? "From: ".$from."\r\n" : "From: ".zig("info","user")." <".zig("info","user_email")."> \r\n" ;
			$headers.= $reply_to ? "Reply-To: ".$reply_to."\r\n" : NULL ;
			$headers.= "X-Mailer: PHP/".phpversion() ;
		}
		$zig_result['value'] = mail($to,$subject,$message,$headers) ;
		$zig_result['return'] = 1 ;
		
		return $zig_result ;
	}
}

?>