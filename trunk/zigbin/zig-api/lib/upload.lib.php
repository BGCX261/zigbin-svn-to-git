<?php

class zig_upload
{
	function upload($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{

		}
		if(is_array($parameters))
		{
			
		}
		$fileName = $_GET['fileName'] ;
		$target_path = "upload/" ;
		$target_path = $target_path.basename( $_FILES[$fileName]['name']) ;

		if(move_uploaded_file($_FILES[$fileName]['tmp_name'], $target_path))
		{
		    $parameters['function'] = "save" ;
    		zig($parameters) ;
		    print "The file ".basename( $_FILES[$fileName]['name'])." has been uploaded" ;
		}
		else
		{
	    	print "There was an error uploading the file, please try again!";
		}
	}
}

?>