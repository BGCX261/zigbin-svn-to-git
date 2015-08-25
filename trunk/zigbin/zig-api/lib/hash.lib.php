<?php

class zig_hash
{
	function hash($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$type = $arg1 ;
			$string = $arg2 ;
			$url = $arg3 ;
			$salt = true ;
		}
		else if(is_array($parameters))
		{
			$type = array_key_exists("type",$parameters) ? $parameters['type'] : NULL ;
			$string = array_key_exists("string",$parameters) ? $parameters['string'] : NULL ;
			$url = array_key_exists("url",$parameters) ? $parameters['url'] : NULL ;
			$salt = array_key_exists("salt",$parameters) ? $parameters['salt'] : true ;
		}
		
		switch($type)
		{
			case 'encrypt':
				$zig_result['value'] = $this->hash_encrypt($string,$salt) ;
				break ;
			case 'decrypt':
				$zig_result['value'] = $this->hash_decrypt($string,$salt) ;
				break ;
			case 'url_encode':
				$zig_result['value'] = $this->hash_url_encode($string,$url) ;
				break ;
			case 'url_decode':
				$zig_result['value'] = $this->hash_url_decode($string,$url) ;
				break ;
			/*case 'vars_encode' :
				$zig_result['value'] = $this->hash_vars_encode($string) ;
				break ;
			case 'vars_decode' :
				$zig_result['value'] = $this->hash_vars_decode($string) ;
				break ;*/
			default:
			{
				$method = "hash_".$type ;
				$zig_result['value'] = $this->$method($string) ;
			}
		}
		return $zig_result ;
	}

	function hash_url_encode($string,$url='')
	{
		$string = $this->hash_encrypt($string) ;
		$url = $url."?".$string ;
		return $url ;
	}

	function hash_url_decode($string='',$url='')
	{
		if($string<>"")
		{
			$string = $this->hash_decrypt($string) ;
		}
		elseif($url<>"")
		{
			$url = explode("?",$url) ;
			$string = $url[1] ;
		}
		return $string ;
	}
	
	function hash_encrypt($string,$salt=true)
	{
		if(!session_id())
		{
			session_start() ;
		}
		$session_id = $salt ? session_id() : NULL ;
		$string = strrev($string) ;
		$string = base64_encode($string) ;
		$string = $session_id.$string ;
		$string = base64_encode($string) ;
		$string = urlencode($string) ;
		switch(strlen($string)<2048)
		{
			default:
			{
				return $string ;
				break ;
			}
			
		}
	}
	
	function hash_decrypt($string,$salt=true)
	{
		if(!session_id())
		{
			session_start() ;
		}
		$session_id = $salt ? session_id() : NULL ;
		$string = urldecode($string) ;
		$string = base64_decode($string) ;
		$string = str_ireplace($session_id,"",$string) ;
		$string = base64_decode($string) ;
		$string = strrev($string) ;
		return $string ;
	}

	function hash_vars_encode($vars)
	{
		if(is_array($vars))
		{
			foreach($vars as $key => $value)
			{
				$vars_string = $vars_string ? $vars_string."," : '' ;
				$vars_string.= $key."=".$value ;
			}
			$vars = $vars_string ;
		}
		$vars_result = $this->hash_encrypt($vars) ;
		return $vars_result ;
	}

	function hash_vars_decode($vars)
	{
		$stripped_vars = false ;
		$vars = $this->hash_decrypt($vars) ;
		if(strpos($vars,","))
		{
			$stripped_vars = explode(",",$vars) ;
		}

		if(is_array($stripped_vars))
		{
			foreach($stripped_vars as $value)
			{
				$stripped = explode("=",$value) ;
				/*$stripped[1] = str_replace("{comma}",",",$stripped[1]) ;
				$vars_result[$stripped[0]] = str_replace("{equal}","=",$stripped[1]) ;*/
				switch(count($stripped)>1)
				{
					case true:
					{
						$vars_result[$stripped[0]] = $this->hash_stringDecode($stripped[1]) ;
					}
				}
			}
		}
		else if(strpos($vars,"="))
		{
			$stripped_vars = explode("=",$vars) ;
			/*$stripped_vars[1] = str_replace("{comma}",",",$stripped_vars[1]) ;
			$vars_result[$stripped_vars[0]] = str_replace("{equal}","=",$stripped_vars[1]) ;*/
			$vars_result[$stripped_vars[0]] = $this->hash_stringDecode($stripped_vars[1]) ;
		}
		else
		{
			/*$vars = str_replace("{comma}",",",$vars) ;
			$vars_result = str_replace("{equal}","=",$vars) ;*/
			$vars_result = $this->hash_stringDecode($vars) ;
		}
		
		return isset($vars_result) ? $vars_result : false ;
	}

	function hash_queryStringDecode($string)
	{
		switch($string<>"")
		{
			case true:
			{
				$string = $this->hash_stringDecode($string) ;
				$explodedString = explode(",",$string) ;
				foreach($explodedString as $variable)
				{
					$explodedVariable = explode("=",$string) ;
					$variables[$explodedVariable[0]] = $explodedVariable[1] ;
				}
				$string = $variables ;
			}
		}
		return $string ;
	}

	function hash_stringEncode($string)
	{
		$string = str_replace(",","{comma}",$string) ;
		return str_replace("=","{equal}",$string) ; 
	}
	
	function hash_stringDecode($string)
	{
		$string = str_replace("{comma}",",",$string) ;
		return str_replace("{equal}","=",$string) ;
	}
}

?>