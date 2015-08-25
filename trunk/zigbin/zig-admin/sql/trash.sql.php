<?php

class zig_trash
{
	function trash($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL)
	{
		$global_database = zig("config","global database") ;
		$pre = zig("config","pre") ;
		$sql = "DELETE FROM `${zig_global_database}`.`${pre}trash`" ;
		zig("query",$sql) ;
		print "Trash Emptied" ;
	}
}

?>