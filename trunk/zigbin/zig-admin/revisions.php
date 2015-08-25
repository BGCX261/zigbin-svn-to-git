<?php

require_once("../zig-api/zigbin.php") ;
$parameters = array
(
	'function'		=>	"wizard",
	'table'			=>	"zig_revisions",
	'unserialize'	=>	"info"
) ;
zig($parameters) ;

?>