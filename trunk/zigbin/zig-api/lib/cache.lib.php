<?php

class zig_cache
{
	function cache($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$request = $arg1 ;
			$name = $arg2 ;
			$value = $arg3 ;
		}
		if(is_array($parameters))
		{
			$request = array_key_exists("request") ? $parameters['request'] : isset($request) ? $request : false ;
			$name = array_key_exists("name") ? $parameters['name'] : isset($name) ? $name : false ;
			$value = array_key_exists("value") ? $parameters['value'] : isset($value) ? $value : NULL ;
		}
		$zig_result['return'] = 1 ;
		if(!$name)
		{
			$zig_result['value'] = NULL ;
			return $zig_result ;
		}
		if(!session_id())
		{
			session_start() ;
		}
		if(!is_array($_SESSION))
		{
			$_SESSION['zig_cache'] = array() ;
		}
		else if(!array_key_exists("zig_cache",$_SESSION))
		{
			$_SESSION['zig_cache'] = array() ;
		}

		switch($request)
		{
			case "file_exists":
			{
				if(!array_key_exists("file_exists",$_SESSION['zig_cache']))
				{
					$_SESSION['zig_cache']['file_exists'] = array() ;
				}
				if(array_key_exists($name,$_SESSION['zig_cache']['file_exists']))
				{
					$zig_result['value'] = $_SESSION['zig_cache']['file_exists'][$name] ;
				}
				else
				{
					$zig_result['value'] = $_SESSION['zig_cache']['file_exists'][$name] = file_exists($name) ;
				}
				break ;
			}
			case "filesize":
			{
				if(!array_key_exists("filesize",$_SESSION['zig_cache']))
				{
					$_SESSION['zig_cache']['filesize'] = array() ;
				}
				if(array_key_exists($name,$_SESSION['zig_cache']['filesize']))
				{
					$zig_result['value'] = $_SESSION['zig_cache']['filesize'][$name] ;
				}
				else
				{
					if(array_key_exists("file_exists",$_SESSION['zig_cache']))
					{
						if(array_key_exists($name,$_SESSION['zig_cache']['file_exists']))
						{
							$file_exists = $_SESSION['zig_cache']['file_exists'][$name] ;
						}
						else
						{
							$file_exists = $_SESSION['zig_cache']['file_exists'][$name] = file_exists($name) ;
						}
					}
					else
					{
						$file_exists = $_SESSION['zig_cache']['file_exists'][$name] = file_exists($name) ;
					}
					if($file_exists)
					{
						$zig_result['value'] = $_SESSION['zig_cache']['filesize'][$name] = filesize($name) ;
					}
					else
					{
						$zig_result['value'] = $_SESSION['zig_cache']['filesize'][$name] = 0 ;
					}
				}
				break ;
			}
			case "fread":
			{
				if(!array_key_exists("fread",$_SESSION['zig_cache']))
				{
					$_SESSION['zig_cache']['fread'] = array() ;
				}
				if(array_key_exists($name,$_SESSION['zig_cache']['fread']))
				{
					$zig_result['value'] = $_SESSION['zig_cache']['fread'][$name] ;
				}
				else
				{
					if(array_key_exists("file_exists",$_SESSION['zig_cache']))
					{
						if(array_key_exists($name,$_SESSION['zig_cache']['file_exists']))
						{
							$file_exists = $_SESSION['zig_cache']['file_exists'][$name] ;
						}
						else
						{
							$file_exists = $_SESSION['zig_cache']['file_exists'][$name] = file_exists($name) ;
						}
					}
					else
					{
						$file_exists = $_SESSION['zig_cache']['file_exists'][$name] = file_exists($name) ;
					}
					if($file_exists)
					{
						if(array_key_exists("filesize",$_SESSION['zig_cache']))
						{
							if(array_key_exists($name,$_SESSION['zig_cache']['filesize']))
							{
								$filesize = $_SESSION['zig_cache']['filesize'][$name] ;
							}
							else
							{
								$filesize = $_SESSION['zig_cache']['filesize'][$name] = filesize($name) ;
							}
						}
						else
						{
							$filesize = $_SESSION['zig_cache']['filesize'][$name] = filesize($name) ;
						}
						$handle = fopen($name,"r") ;
						$zig_result['value'] = $_SESSION['zig_cache']['fread'][$name] = fread($handle,$filesize) ;
						fclose($handle) ;
					}
					else
					{
						$zig_result['value'] = NULL ;
					}
				}
				break ;
			}
			case "fwrite":
			{
				$handle = @fopen($name,"w") ;
				switch($handle)
				{
					case false:
					{
						$fileParts = pathinfo($name) ;
						switch(zig("cache","file_exists",$fileParts['dirname']))
						{
							case false:
							{
								mkdir($fileParts['dirname'],0774,true) ;
								$handle = @fopen($name,"w") ;
							}
						}
						break ;
					}
					default:
					{
						fwrite($handle,$value) ;
						fclose($handle) ;
						$_SESSION['zig_cache']['fread'][$name] = $value ;
						$_SESSION['zig_cache']['file_exists'][$name] = true ;
						if(array_key_exists("filesize",$_SESSION['zig_cache']))
						{
							if(array_key_exists($name,$_SESSION['zig_cache']['filesize']))
							unset($_SESSION['zig_cache']['filesize'][$name]) ;
						}
					}
				}
				break ;
			}
			case "mkdir":
			{
				switch(zig("cache","file_exists",$name))
				{
					case false:
					{
						mkdir($name,0774,true) ;
				$_SESSION['zig_cache']['file_exists'][$name] = true ;
					}
				}
				$zig_result['value'] = true ;
				break ;
			}
			case "unlink":
			{
				if(!array_key_exists("file_exists",$_SESSION['zig_cache']))
				{
					$_SESSION['zig_cache']['file_exists'] = array() ;
				}
				if(array_key_exists($name,$_SESSION['zig_cache']['file_exists']))
				{
					$file_exists = $_SESSION['zig_cache']['file_exists'][$name] ;
				}
				else
				{
					$file_exists = $_SESSION['zig_cache']['file_exists'][$name] = file_exists($name) ;
				}
				if($file_exists)
				{
					$zig_result['value'] = unlink($name) ;
					$_SESSION['zig_cache']['file_exists'][$name] = false ;
					$_SESSION['zig_cache']['filesize'][$name] = 0 ;
				}
				break ;
			}
			case "clear":
			{
				if(!array_key_exists("values",$_SESSION['zig_cache']))
				{
					$_SESSION['zig_cache']['values'] = array() ;
				}
				if(array_key_exists($name,$_SESSION['zig_cache']['values']))
				{
					unset($_SESSION['zig_cache']['values'][$name]) ;
				}
				break ;
			}
			case "get":
			{
				if(!array_key_exists("values",$_SESSION['zig_cache']))
				{
					$_SESSION['zig_cache']['values'] = array() ;
				}
				$zig_result['value'] = array_key_exists($name,$_SESSION['zig_cache']['values']) ? $_SESSION['zig_cache']['values'][$name] : NULL ;
				break ;
			}
			case "set":
			default:
			{
				if(!array_key_exists("values",$_SESSION['zig_cache']))
				{
					$_SESSION['zig_cache']['values'] = array() ;
				}
				$_SESSION['zig_cache']['values'][$name] = $value ;
				break ;
			}
		}

		$zig_result['return'] = 1 ;
		return $zig_result ;
	}	
}

?>