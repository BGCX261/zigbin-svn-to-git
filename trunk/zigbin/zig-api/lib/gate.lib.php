<?php

class zig_gate
{
	function gate($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$method = $arg1 ? $arg1 : "login" ;
			$process = $arg2 ? $arg2 : "display" ;
			$zigjax = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$method = array_key_exists('method',$parameters) ? $parameters['method'] : "login" ;
			$process = array_key_exists('process',$parameters) ? $parameters['process'] : "display" ;
			$zigjax = array_key_exists('zigjax',$parameters) ? $parameters['zigjax'] : false ;
			$email = array_key_exists('email',$parameters) ? $parameters['email'] : NULL ;
			$message = array_key_exists('message',$parameters) ? $parameters['message'] : NULL ;
		}

		if($method=="forgot" or $method=="register")
		{
			$pre = zig("config","pre") ;
			$zig_global_database = zig("config","global_database") ;		
			$domain = $_SERVER['HTTP_HOST'] ;
			switch($process)
			{
				case "display":
				{
					$hashed_values = "function=gate,method=${method},process=email,zigjax=1" ;
					$hashed_values = zig("hash","encrypt",$hashed_values) ;
					$buffer = $this->gate_template($method) ;
					$buffer = str_replace("{hashed_values}",$hashed_values,$buffer) ;
					$buffer = str_replace("{captcha}","",$buffer) ;
					$buffer = str_replace("{message}",$message,$buffer) ;
					$buffer = str_replace("{tabs}",zig(array("function"=>"tabs","permit"=>false,"new_tab"=>false)),$buffer) ;

					$zig_affiliate_id = isset($_GET['zig_affiliate_id']) ? $_GET['zig_affiliate_id'] : (isset($_POST['zig_affiliate_id']) ? $_POST['zig_affiliate_id'] : NULL) ;
					if($method=="register" and $zig_affiliate_id and (($_COOKIE['zig_affiliate_id']==$zig_affiliate_id and $_COOKIE['zig_affiliate_id']) or !$_COOKIE['zig_affiliate_id']))
					{
						$cookie = $GLOBALS['zig']['default']['cookie'] ;
						$host = $_SERVER['HTTP_HOST'] ;
						$url = str_replace($host,"",$_SERVER['PHP_SELF']) ;
						$splitted_url = explode("zig-api/",$url) ;
						$url = $splitted_url[0] ;
						setcookie("zig_affiliate_id",$zig_affiliate_id,$cookie,$url,$host) ;
					}

					$zig_result['value'] =	$buffer ;
					$zig_result['topmenu'] = 0 ;
					$zig_result['applications'] = 0 ;
					$zig_result['actions'] = 0 ;
					$zig_result['return'] =	1 ;
					break ;
				}

				case "email":
				{
					$user_check_message = $this->gate_check_user("gate_check_user",$method,$email,true) ;
					switch($user_check_message['error'])
					{
						case "":
						{
							$to = $email ;
							switch($method)
							{
								case "forgot":
								{
									$zig_hash = "function=gate,method=${method},process=verify,email=${email}" ;
									$zig_hash = zig("hash","encrypt",$zig_hash) ;
									$verification_link = "http://${domain}".$_SERVER['PHP_SELF']."?zig_hash=${zig_hash}" ;
									$subject = $method=="register" ? "${site_name} New Account" : "${site_name} Password Reset" ;
									$message = zig("template","file","email_".$method) ;
									$message = str_replace("{verification_link}",$verification_link,$message) ;
									$zig_result['value'] = "Please check your email to complete your request" ;
									break ;
								}
								default:
								{
									$registration = zig("config","registration") ;
									switch($registration)
									{
										case "with approval":
										{
											$zig_hash = "function=gate,method=${method},process=disapproved,email=${email}" ;
											$zig_hash = zig("hash","encrypt",$zig_hash) ;
											$verification_link = "http://${domain}".$_SERVER['PHP_SELF']."?zig_hash=${zig_hash}" ;
											$disapproved_verification_link = "http://${domain}".$_SERVER['PHP_SELF']."?zig_hash=${zig_hash}" ;
											$to = $email ;
											$subject = "${site_name} New Account Approval" ;
											$message = zig("template","file","email_approval") ;
											$message = str_replace("{email}",$email,$message) ;
											$message = str_replace("{approved_verification_link}",$verification_link,$message) ;
											$message = str_replace("{disapproved_verification_link}",$disapproved_verification_link,$message) ;
											$zig_result['value'] = "Your request have been received.<br />You would receive an email notification once your request have been approved" ;
											break ;
										}
										default:
										{
											$zig_hash = "function=gate,method=${method},process=verify,email=${email}" ;
											$zig_hash = zig("hash","encrypt",$zig_hash) ;
											$verification_link = "http://${domain}".$_SERVER['PHP_SELF']."?zig_hash=${zig_hash}" ;
											$subject = $method=="register" ? "${site_name} New Account" : "${site_name} Password Reset" ;
											$message = zig("template","file","email_".$method) ;
											$message = str_replace("{verification_link}",$verification_link,$message) ;
											$zig_result['value'] = "Please check your email to complete your request" ;
											break ;
										}
									}
								}
							}
							$site_name = zig("config","title") ;
							$message = str_replace("{subject}",$subject,$message) ;
							$message = str_replace("{site_name}",$site_name,$message) ;
							$headers = "MIME-Version: 1.0\r\n" ;
							$headers.= "Content-type: text/html; charset=iso-8859-1\r\n" ;
							$headers.= "From: ${site_name} <noreply@${domain}>\r\n" ;
//							$headers.= "Reply-To: ${site_name} <noreply@${domain}>\r\n" ;
							$headers.= "X-Mailer: PHP/".phpversion() ;
							mail($to, $subject, $message, $headers) ;							
							break ;
						}
						default:
						{
							$zig_result['value'] = $user_check_message['error'] ;
							break ;
						}
					}
					break ;
				}

				case "verify":
				{
					if($email)
					{
						$user_check_message = $this->gate_check_user("gate_check_user",$method,$email,false) ;
						if(!$user_check_message['error'])
						{
							if($method=="register")
							{
								// -- start insert into contacts
								$referred_by = $_COOKIE['zig_affiliate_id'] ? $_COOKIE['zig_affiliate_id'] : NULL ;
								$table = "${zig_global_database}.${pre}contacts" ;
								$fields = "`zig_created`,`zig_user`,`first_name`,`referred_by`" ;
								$values = "NOW(),'gate.lib.php','${email}','${referred_by}'" ;
								$profile = zig("insert",$table,$fields,$values) ;
								// -- end insert into contacts

								// -- start insert into users
								$table = "${zig_global_database}.${pre}users" ;
								$fields = "`zig_created`,`zig_user`,`profile`,`username`,`password`,`email`" ;
								$values = "NOW(),'gate.lib.php','${profile}','${email}','','${email}'" ;
								$user_id = zig("insert",$table,$fields,$values) ;
								// -- start insert into users

								// -- start insert into permissions
								$table = "${zig_global_database}.${pre}permissions" ;
								$fields = "`zig_created`,`zig_user`,`zig_parent_id`,`users`,`module`" ;
								$values = "NOW(),'gate.lib.php','${user_id}','${email}','zig-home'" ;
								zig("insert",$table,$fields,$values) ;

								$fields = "`zig_created`,`zig_user`,`zig_parent_id`,`users`,`module`" ;
								$values = "NOW(),'gate.lib.php','${user_id}','${email}','zig-pref'" ;
								zig("insert",$table,$fields,$values) ;
								// -- end insert into permissions
							}

							$landing_url = "http://${domain}".$_SERVER['PHP_SELF'] ;
							$splitted_url = explode("zig-api/decoder.php",$landing_url) ;
							$landing_url = $splitted_url[0]."zig-pref/?zig_hash=".zig("hash","encrypt","template=reset") ;
							$this->gate_login("gate_login",$user_check_message['value'],$landing_url,NULL) ;
						}
						else
						{
							zig(array("function"=>"gate","method"=>$method,"message"=>$user_check_message['error'])) ;
						}
					}
					break ;
				}
			}

			$zig_result['return'] = 1 ;
		}
		else
		{
			$zig_result = $this->$method() ;
		}

		return $zig_result ;
	}

	function login()
	{
		$zig_passed_hash = isset($_GET['zig_hash']) ? $_GET['zig_hash'] : (isset($_POST['zig_hash']) ? $_POST['zig_hash'] : NULL) ;
		$zig_hash_decrypted_link = zig("hash","decrypt",$zig_passed_hash) ;

		if(!session_id())
		{
			session_start() ;
		}
		if(array_key_exists("zig_hash",$_SESSION))
		{
			if($_SESSION['zig_hash']<>"")
			{
				$zig_hash_vars = zig("hash","vars_decode",$_SESSION['zig_hash']) ;
				if(session_id()==$zig_hash_vars['session_id'])
				{
					$module = zig("config","module") ;
					$return_url = $zig_passed_hash ? "http://".$_SERVER['HTTP_HOST'].$zig_hash_decrypted_link : "../".$module ;
					header("Location: $return_url") ;
					exit() ;
				}
			}
		}

		$login = isset($_GET['login']) ? $_GET['login'] : (isset($_POST['login']) ? $_POST['login'] : '') ;
		$session = session_id() ;
		$pre = $GLOBALS['zig']['sql']['pre'] ;
		$zig_global_database = $GLOBALS['zig']['sql']['global_database'] ;
		$mod = $GLOBALS['zig']['current']['module'] ;
		$GLOBALS[$mod]['current']['method'] = "login" ;
		$buffer = $this->gate_template("login") ;
		$action = $zig_passed_hash ? "index.php?zig_hash=$zig_passed_hash" : "index.php" ;
		$buffer = str_replace("{action}",$action,$buffer) ;

		$sql = "SELECT id FROM `$zig_global_database`.`${pre}session` WHERE `session`='$session' AND `event`='logged in' AND `zig_status`<>'deleted' ORDER BY `id` DESC LIMIT 1" ;
		$result = zig("query",$sql) ;
		if($result->RecordCount())
		{
			$fetch = $result->fetchRow() ;
			$id_where = " AND `id`>$fetch[id]" ;
		}
		else
		{
			$id_where = "" ;
		}

		if($login)
		{
			$username = isset($_POST['username']) ? $_POST['username'] : NULL ;
			$password = isset($_POST['password']) ? $_POST['password'] : NULL ;
			$captcha_flag = isset($_POST['captcha_flag']) ? $_POST['captcha_flag'] : NULL ;
			$user_guess = isset($_POST['user_guess']) ? $_POST['user_guess'] : NULL ;
			
			$zig_authentication = zig("config","authentication") ;
			$authentication = zig("authenticate",$zig_authentication,$username,$password) ;

			if($captcha_flag and $user_guess)
			{
				require_once("../zig-api/plugins/captcha/animatedcaptcha.class.php") ;
				$user_guess = $_POST['user_guess'] ;

				$img = new animated_captcha() ;
				$img->session_name = "my_session" ;
				$img->magic_words("secret") ;
				$valid = $img->validate($user_guess) ;

				if($valid and $authentication)
				{
					$session = session_id() ;
				}
			}

			if($authentication and (($valid and $user_guess) or !$captcha_flag))
			{
				$zig_hash_decrypted_link = $zig_passed_hash ? "http://".$_SERVER['HTTP_HOST'].$zig_hash_decrypted_link : NULL ;
				$this->gate_login("gate_login",$username,$zig_hash_decrypted_link,NULL) ;
				exit() ;
			}
			else
			{
				$sql = "SELECT count(session) as rowcount FROM `$zig_global_database`.`${pre}session` WHERE `session`='$session' AND `event`='login failed' AND `zig_status`<>'deleted' $id_where ORDER BY `id` DESC LIMIT 3" ;
				$result = zig("query",$sql) ;
				$fetch = $result->fetchRow() ;
				$count = $fetch["rowcount"] ;
				if($count<3)
				{
					$sql = "INSERT INTO `${zig_global_database}`.`${pre}session` (`zig_created`,`zig_user`,`username`,`session`,`ip`,`event`) VALUES (NOW(),'gate.lib.php','$username','$session','$_SERVER[REMOTE_ADDR]','login failed')" ;
					zig("query",$sql) ;
					$count++ ;
				}
				$message = "authentication failed!" ;

				if($count>=3)
				{
					$captcha_template = zig("template","file","captcha") ;
					$buffer = str_replace("{captcha}",$captcha_template,$buffer) ;
					$buffer = str_replace("{captcha_image}","<img alt='loading...' id='ci' src='../zig-api/plugins/captcha/animatedcaptcha_generate.php?i=md5(microtime()) ;' />",$buffer) ;
				}
			}
		}

		$username = isset($username) ? $username : NULL ;
		if(!$username)
		{
			if(array_key_exists("current",$GLOBALS['zig']))
			{
				$username = array_key_exists("user",$GLOBALS['zig']['current']) ? $GLOBALS['zig']['current']['user'] : NULL ;
			}
		}
		$buffer = str_replace("{username}",$username,$buffer) ;
		$buffer = str_replace("{captcha}","",$buffer) ;
		$message = isset($message) ? $message : NULL ;
		$message.= substr($_SERVER['HTTP_HOST'],0,5)=="demo." ? "<br />username: <strong>demo</strong>&nbsp;&nbsp;<br />password: <strong>demo</strong>" : NULL  ;
		$buffer = str_replace("{message}",$message,$buffer) ;
		$buffer = str_replace("{tabs}",zig(array("function"=>"tabs","permit"=>false,"new_tab"=>false)),$buffer) ;
		$zig_result['value'] =	$buffer ;
		$zig_result['topmenu'] = 0 ;
		$zig_result['applications'] = 0 ;
		$zig_result['actions'] = 0 ;
		$zig_result['return'] =	1 ;

		return $zig_result ;
	}

	function logout()
	{
		$pre = $GLOBALS['zig']['sql']['pre'] ;
		$zig_global_database = $GLOBALS['zig']['sql']['global_database'] ;
		$cookie = $GLOBALS['zig']['default']['cookie'] ;
		$host = $_SERVER['HTTP_HOST'] ;
		$url = str_replace($host,"",$_SERVER['PHP_SELF']) ;
		$url = str_replace("zig-api/decoder.php","",$_SERVER['PHP_SELF']) ;
		//setcookie("zig_hash","",$cookie,$url,$host) ;
		unset($_SESSION['zig_hash']) ;
		$session = session_id() ;
		$username = zig("info","user") ;
		if($username)
		{
			$event = "logged out" ;
		}
		else
		{
			$sql = "SELECT `username` FROM `$zig_global_database`.`${pre}session` WHERE `session`='${session}' ORDER BY `id` DESC LIMIT 1" ;
			$result = zig("query",$sql) ;
			$fetch = $result->fetchRow() ;
			$username = $fetch['username'] ;
			$event = "timed out" ;
		}
		$sql = "INSERT INTO `$zig_global_database`.`${pre}session` (zig_created,zig_user,username,session,ip,event) VALUES (NOW(),'gate.lib.php','$username','$session','$_SERVER[REMOTE_ADDR]','$event')" ;
		zig("query",$sql) ;
		session_destroy() ;
		header("Location: ../zig-api/") ;
		exit() ;
	}

	function gate_login($parameters,$arg1,$arg2,$arg3)
	{
		$username = $arg1 ;
		$return_url = $arg2 ;
		$query_string = $arg3 ;
		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$session = session_id() ;
		$cookie = $GLOBALS['zig']['default']['cookie'] ;
		$host = $_SERVER['HTTP_HOST'] ;
		$url = str_replace($host,"",$_SERVER['PHP_SELF']) ;
		$splitted_url = explode("zig-api/",$url) ;
		$url = $splitted_url[0] ;
		session_start() ;
		$zig_hash = "username=".$username.",session_id=".session_id().",url=".$url ;
		$zig_hash = zig("hash","encrypt",$zig_hash) ;
		//setcookie("zig_hash",$zig_hash,$cookie,$url,$host) ;
		$_SESSION['zig_hash'] = $zig_hash ;
		$sql = "INSERT INTO `${zig_global_database}`.`${pre}session` (`zig_created`,`zig_user`,`username`,`session`,`ip`,`event`) VALUES (NOW(),'gate.lib.php','${username}','${session}','$_SERVER[REMOTE_ADDR]','logged in')" ;
		zig("query",$sql) ;
		$return_url = $return_url ? $return_url : "../".zig(array("function"=>"config","name"=>"module","user"=>$username)).$query_string ;
		header("Location: ${return_url}") ;
		exit() ;
	}

	function gate_template($template)
	{
		$show_title = zig("config","show title") ;
		$zig_title = $show_title ? zig("config","title") : NULL ;
		$buffer = zig("template","file","gate") ;
		$buffer = str_replace("{zig_form}",zig("template","file",$template),$buffer) ;
		$buffer = str_replace("{zig_title}",$zig_title,$buffer) ;
		$zig_icon = zig("config","icon") ;
		$zig_icon = zig("images",$zig_icon) ;
		$buffer = str_replace("{zig_icon}",$zig_icon,$buffer) ;

		return $buffer ;
	}
	
	function gate_check_user($parameters,$arg1,$arg2,$arg3)
	{
		$method = $arg1 ;
		$email = $arg2 ;
		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$sql = "SELECT `username`,`status` FROM `${zig_global_database}`.`${pre}users` WHERE `email`='${email}' LIMIT 1" ;
		$result = zig("query",$sql) ;
		$fetch = $result->fetchRow() ;
		$username = $zig_return['value'] = $fetch['username'] ;
		$status = $zig_return['status'] = $fetch['status'] ;
		switch($method)
		{
			case "forgot":
			{
				if(!$username)
				{
					$zig_return['error'] = "User does not exists!" ;
				}
				break ;
			}
			case "register":
			{
				switch($status)
				{
					case "":
					{
						break ;
					}
					case "Approved":
					{
						$zig_return['error'] = "User approved, please check your email!" ;
						break ;
					}
					case "Pending":
					{
						$zig_return['error'] = "User pending approval!" ;
						break ;
					}
					default:
					{
						$zig_return['error'] = "User already exists!" ;
						break ;
					}
				}
				break ;
			}
			default:
			{
				$zig_return['error'] = "An error was encountered during the user checking method!" ;
				break ;
			}
		}
		return $zig_return ;
	}
}

?>