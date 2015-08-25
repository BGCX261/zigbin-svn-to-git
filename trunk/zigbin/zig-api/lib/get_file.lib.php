<?php

class zig_get_file
{
	function get_file($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$filename = $arg1 ;
			$new_filename = $arg2 ? $arg2 : $arg1 ;
			$method = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$filename = array_key_exists("filename",$parameters) ? $parameters['filename'] : NULL ;
			$new_filename = array_key_exists("new_filename",$parameters) ? $parameters['new_filename'] : $filename ;
			$method = array_key_exists("method",$parameters) ? $parameters['method'] : NULL ;
		}

		if($method=="download")
		{
			header('Content-Disposition: attachment; filename="'.$new_filename.'"') ;
		}
		else if($method=="view")
		{
			$contentType = $this->mime_content_type($filename) ;
			header("Content-type: ${contentType}") ;
			switch(substr($contentType,0,5))
			{
				case "image":
				{
					break ;
				}
				default:
				{
					header('Content-Disposition: filename="'.$new_filename.'"') ;
				}
			}
		}

		if(zig("cache","file_exists",$filename))
		{
			$buffer = readfile($filename) ;
			print $buffer  ;
			exit() ;
		}
	}

	function mime_content_type($filename)
	{
		switch(class_exists("finfo"))
		{
			case true:
			{
			    $result = new finfo() ;
	    		if (is_resource($result) === true)
	    		{
					return $result->file($filename, FILEINFO_MIME_TYPE);
	    		}
			}
		}
	    return false;
	}
}

?>