<?php

require_once(dirname(__FILE__).'/animatedcaptcha.class.php') ;
require_once("../../zig-api/zigbin.php") ;

$user_guess = "" ;
$user_guess = $_POST['user_guess'] ;

$img = new animated_captcha();
$img->session_name = 'my_session';
$img->magic_words('secret');
$valid = $img->validate($user_guess);

if ($user_guess=="")
{
	print "<table align='center'><tr><td align='center'><p>Invalid!</p> <a href='../index.php'>please try again<a></td></tr></table>" ;
}
else if($valid)
{
	$session = session_id() ;
	$sql = "DELETE FROM zig_session WHERE session='$session'" ;
	zig("query",$sql,"redirect.php") ;
	header("Location: ../index.php") ;
}
else
{
	print "<table align='center'><tr><td align='center'><p>Invalid!</p> <a href='../index.php'>please try again<a></td></tr></table>" ;
}

?>