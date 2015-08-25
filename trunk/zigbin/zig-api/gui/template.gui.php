<?php

class zig_template {
	function template($parameters,$arg1='',$arg2='',$arg3='') {
		$buffer = $module = $block = $var = $value = "" ;
		if($arg1 or $arg2 or $arg3) {
			$method = $arg1 ;
			if($method=="file") {
				$file = $arg2 ;
				$module = $arg3 ;
			}
			else if($method=="block") {
				$file = $arg2 ;
				$block = $arg3 ;
			}
			else {
				$buffer = $method ;
				$var = $arg2 ;
				$value = $arg3 ;
			}
		}
		if(is_array($parameters)) {
			$method = array_key_exists("method",$parameters) ? $parameters['method'] : $method ;
			$file = array_key_exists("file",$parameters) ? $parameters['file'] : $file ;
			$block = array_key_exists("block",$parameters) ? $parameters['block'] : $block ;
			$buffer = array_key_exists("buffer",$parameters) ? $parameters['buffer'] : $buffer ;
			$var = array_key_exists("var",$parameters) ? $parameters['var'] : $var ;
			$value = array_key_exists("value",$parameters) ? $parameters['value'] : $value ;
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : $module ;
		}

		if(!$module) {
			$debug_backtrace = debug_backtrace() ;
			$module = $debug_backtrace[1]['file'] ;
			$module = str_replace("\\","/",$module) ;
			$module = str_replace($_SERVER['DOCUMENT_ROOT']."/","",$module) ;
			$splitted_module = explode("/",$module) ;
			$module = $splitted_module[sizeof($splitted_module)-2] ;
			$module = ($module=="zig-api" and $GLOBALS['zig']['current']['module']<>"zig-api" and $GLOBALS['zig']['current']['module']<>'') ? $GLOBALS['zig']['current']['module'] : $module ;
		}

		if($method=="file" or $method=="block") {
			$original_file = $file ;
			if((!zig("cache","file_exists",$file) and substr($file,strlen($file),1)<>"/") or zig("cache","file_exists",$file."/")) {
				$file = $file.".gui.tpl" ;
				if(!zig("cache","file_exists",$file)) {
					if(zig("cache","file_exists","../".$module."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file)) {
						$file = "../".$module."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file ;
					}
					else if(zig("cache","file_exists","../".$GLOBALS['zig']['current']['module']."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file)) {
						$file = "../".$GLOBALS['zig']['current']['module']."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file ;
					}
					else if(zig("cache","file_exists","../".$GLOBALS['zig']['path']['api']."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file)) {
						$file = "../".$GLOBALS['zig']['path']['api']."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file ;
					}
					else {
						$file = false ;
					}
				}
				if(!$file) {
					$file = $original_file ;
					$file = $file.".gui.html" ;
					if(!zig("cache","file_exists",$file))
					{
						if(zig("cache","file_exists","../".$module."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file)) {
							$file = "../".$module."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file ;
						}
						else if(zig("cache","file_exists","../".$GLOBALS['zig']['current']['module']."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file)) {
							$file = "../".$GLOBALS['zig']['current']['module']."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file ;
						}
						else if(zig("cache","file_exists","../".$GLOBALS['zig']['path']['api']."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file)) {
							$file = "../".$GLOBALS['zig']['path']['api']."/".$GLOBALS['zig']['path']['template']."/".$GLOBALS['zig']['current']['template']."/".$file ;
						}
						else {
							$file = false ;
						}
					}
				}
			}

			if($method=="file" and $file) {
				$buffer = $this->template_file('',$file) ;
			}
			else if($method=="block" and $file and $block) {
				$buffer = $this->template_block('',$file,$block) ;
			}
			else {
				$buffer = false ;
			}
		}
		else {
			$buffer = $this->template_replace('',$buffer,$var,$value) ;
		}

		$zig_result['value'] = $buffer ;
		$zig_result['return'] = 1 ;
		return $zig_result ;
	}
	
	function template_file($parameters,$arg1='',$arg2='',$arg3='') {
		if($arg1) {
			$file = $arg1 ;
		}
		else {
			$file = $parameters['file'] ;
		}
		$buffer = zig("cache","fread",$file) ;
		return $buffer ;
	}

	function template_replace($parameters,$arg1='',$arg2='',$arg3='') {
		if($arg1) {
			$buffer = $arg1 ;
			$var = $arg2 ;
			$value = $arg3 ;
		}
		else if(is_array($parameters)) {
			
		}
		$buffer = str_replace($var,$value,$buffer) ;
		return $buffer ;
	}

	function template_block($parameters,$arg1='',$arg2='',$arg3='') {
		if($arg1) {
			$file = $arg1 ;
			$block = $arg2 ;
		}
		else if(is_array($parameters)) {
			$file = $parameters['file'] ;
			$block = $parameters['block'] ;
		}

		$buffer = zig("cache","fread",$file) ;
		if($buffer) {
			$ripped_block = explode("<!-- start $block -->",$buffer) ;
			$buffer = isset($ripped_block[1]) ? $ripped_block[1] : "" ;
			$ripped_block = explode("<!-- end $block -->",$buffer) ;
			$buffer = $ripped_block[0] ;
		}
		return $buffer ;
	}
}
?>