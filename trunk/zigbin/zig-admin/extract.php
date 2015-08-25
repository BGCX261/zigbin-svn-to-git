<?php

require_once("../zig-api/zigbin.php") ;
$html = zig("template","file","extract") ;
$zig_hash = zig("hash","encrypt","function=extract,module=zig-admin,zigjax=1") ;
$html = str_replace("{zig_hash}",$zig_hash,$html) ;
zig("content",$html) ;

?>