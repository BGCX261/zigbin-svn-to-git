<html>
<head>
<title>zig-api | Captcha</title>
<link href='animatedcaptcha_background/main.gui.css' rel='stylesheet' type='text/css' />
<script type="text/javascript">
	function refC() {
		document.getElementById('ci').src = 'animatedcaptcha_generate.php?'+Math.random();
	}
</script>
</head>
<body>
<form action="./redirect.php" method="post">
<table align="center">
<tr>
<td align="center">
<img alt="loading..." id="ci" src="./animatedcaptcha_generate.php?i=<?php echo(md5(microtime()));?>" /><br />
<a href = '' onclick="refC()" style="cursor:pointer" >click to refresh</a>

</td>
</tr>
<tr>
<td align="center">
<input type="text" name="user_guess" autocomplete="off" />
</td>
</tr>
<tr>
<td align="center">
<input type="submit" value="Submit" />
</td>
</tr>
</table>
</form>
</html>