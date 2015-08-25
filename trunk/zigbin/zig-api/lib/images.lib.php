<?php

class zig_images
{
	function images($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$image = $arg1 ;
			$directory = $arg2 ;
		}
		else if(is_array($parameters))
		{
			$image = array_key_exists("image") ? $parameters['image'] : NULL ;
			$directory = array_key_exists("directory") ? $parameters['directory'] : NULL ;
		}

		$application_image = $directory ? $directory."/".$image : NULL ;
		$application_image = ($application_image and zig("cache","file_exists",$application_image)) ? $application_image : "../".$GLOBALS['zig']['current']['module']."/".$GLOBALS['zig']['path']['theme']."/default/img/".$image ;
		$imagePath = zig("cache","file_exists",$application_image) ? $application_image : "../".$GLOBALS['zig']['path']['api']."/".$GLOBALS['zig']['path']['theme']."/default/img/".$image ;

		// -- Start get user define image
		if(!zig("cache","file_exists",$imagePath))
		{
			$imagePath = zig("config","files path").$image ;
			$zigHash = zig("hash","encrypt","function=get_file,method=view,filename=".$imagePath) ;
			switch(zig("cache","file_exists",$imagePath))
			{
				case true:
				{
					$customSource = "../zig-api/decoder.php?zig_hash=".$zigHash ;
				}
			}
		}
		// -- End get user define image

		// -- Start get default image if given image does not exists
		if(!zig("cache","file_exists",$imagePath))
		{
			$image_file = $default_image_file = zig("config","image") ;
			$image_file = !zig("cache","file_exists",$imagePath) ? $default_image_file : $image_file ;
			$imagePath = "../".$GLOBALS['zig']['current']['module']."/".$GLOBALS['zig']['path']['theme']."/default/img/".$image_file ;
			$imagePath = zig("cache","file_exists",$imagePath) ? $imagePath : "../".$GLOBALS['zig']['path']['api']."/".$GLOBALS['zig']['path']['theme']."/default/img/".$image_file ;
		}
		// -- End get default image if given image does not exists

		$zig_result['value'] = isset($customSource) ? $customSource : $imagePath ;
		$zig_result['return'] = 1 ;
		
		return $zig_result ;
	}
}

?>