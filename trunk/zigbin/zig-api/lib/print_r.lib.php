<?php

class zig_print_r {
	public $result = array() ;
	function __construct($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL) {
		switch(is_array($parameters)) {
			case true: {
				$this->result = $this->print_r($parameters,$arg1,$arg2,$arg3) ;
				break ;
			}
			default: {
				$this->result = $this->print_r(false,$parameters,$arg1,$arg2) ;
			}
		}
	}

	function print_r($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL) {
		$variable = $arg1 ;
		if(is_array($parameters)) {
			$variable = $parameters ;
		}
		$debug_backtrace = debug_backtrace() ;
		$script_file = $debug_backtrace[1]['file'] ;
		$script_line = $debug_backtrace[1]['line'] ;
		$fileType = gettype($variable) ;
		switch($fileType) {
			case "array":
			case "object": {
				$length = count($variable) ;
				break ;
			}
			default: {
				$length = strlen($variable) ;
			}
		}
		$html = "<pre>" ;
		$html.= "File: ${script_file}<br />" ;
		$html.= "Line: ${script_line}<br />" ;
		$html.= "Type: ${fileType}<br />" ;
		$html.= "Length: ".$length ;
		$html.= "<br />" ;
		$html.= print_r($variable,true) ;
		$html.= "</pre>" ;
		print $html ;
	}
}

?>