<?php

class zig_export
{
	function export($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$sql = $arg1 ;
			$type = $arg2 ? $arg2 : "csv" ;
		}
		else if(is_array($parameters))
		{
			$sql = array_key_exists("sql",$parameters) ? $parameters['sql'] : NULL ;
			$type = array_key_exists("type",$parameters) ? $parameters['type'] : "csv" ;
		}
		
		if($type == "csv")
		{
			require_once("../zig-api/plugins/adodb/toexport.inc.php") ;
			$files_path = zig("config","files path") ;
			$temp_path = "${files_path}blank.csv" ;
			
			//if(file_exists($temp_path))
			if(zig("cache","file_exists",$temp_path))
			{
				$time = time() ;
				$function = "export" ;
				$file_name = $time.$function.".".$type ;
				copy($temp_path,$temp_path.$file_name) ;
				$result = $GLOBALS['zig']['adodb']->Execute($sql) ;
				
				if($result<>"")
				{
					$result->MoveFirst() ;
					$fp = fopen($temp_path.$file_name, "w") ;
					if($fp)
					{
						rs2csvfile($result, $fp) ;
						fclose($fp);
					}
					$buffer = "\n end of file -- file export success" ;
					$location = "$temp_path$file_name" ;
					header('Content-type: application/csv') ;
					header("Content-Disposition: attachment; filename=$file_name") ;
					readfile("$temp_path$file_name") ;
					$zig_result['buffer'] = $buffer ;
					print $buffer  ;
					zig("cache","unlink",$location) ;
					zig("cache","unlink",$temp_path) ;
					//unlink($location);
					//unlink($temp_path);
				}
				else
				{
					$zig_result['error'] = $GLOBALS['zig']['obj']['error']->error(106) ;
				}
			}
			
			else
			{
				$zig_result['error'] = $GLOBALS['zig']['obj']['error']->error(102) ;
			}
		}
		else
		{
			$zig_result['error'] = $GLOBALS['zig']['obj']['error']->error(103) ;
		}

		$zig_result['gui_buffer'] = 0 ;
		return $zig_result ;
	}
}

?>