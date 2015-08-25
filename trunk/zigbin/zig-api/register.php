<?php

	require_once("../zig-api/zigbin.php") ;
	$content = zig("gate","register") ;
	$content_parameters = array
	(
		"function"		=>	"content",
		"content"		=>	$content,
		"security"		=>	false,
		"topmenu"		=>	false,
		"applications"	=>	false
	) ;
	zig($content_parameters) ;

?>