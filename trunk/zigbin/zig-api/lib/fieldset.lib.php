<?php

class zig_fieldset {
	public $result = array() ;
	function __construct($parameters,$arg1=NULL,$arg2=NULL,$arg3=NULL) {
		switch(is_array($parameters)) {
			case true: {
				$this->result = $this->fieldset($parameters,$arg1,$arg2,$arg3) ;
				break ;
			}
			default: {
				$this->result = $this->fieldset($arg1,$arg2,$arg3) ;
			}
		}
	}

	function fieldset($parameters,$arg1='',$arg2='',$arg3='') {
		$collapsed = true ;
		$collapsible = true ;
		$description = NULL ;
		$callback = NULL ;
		if($arg1 or $arg2 or $arg3) {
			$content = $arg1 ;
			$name = $arg2 ;
			$collapsed = $arg3===false ? $arg3 : $collapsed ;
		}
		if(is_array($parameters)) {
			$content = array_key_exists("content",$parameters) ? $parameters['content'] : NULL ;
			$name = array_key_exists("name",$parameters) ? $parameters['name'] : NULL ;
			$collapsed = array_key_exists("collapsed",$parameters) ? $parameters['collapsed'] : true ;
			$collapsible = array_key_exists("collapsible",$parameters) ? $parameters['collapsible'] : true ;
			$description = array_key_exists("description",$parameters) ? $parameters['description'] : NULL ;
			$callback = array_key_exists("callback",$parameters) ? ($parameters['callback'] ? ",'".addslashes($parameters['callback'])."'" : NULL) : NULL ;
		}

		$name = str_replace("'","",$name) ;
		$name = str_replace('"',"",$name) ;
		$unique_name = $name."_".uniqid() ;
		$template_block = $collapsible ? "collapsible" : "uncollapsible" ;

		if(!$collapsed) {
			$zig_fieldset_class = "zig_fieldset_displayed" ;
			$zig_fieldset_title_class = "zig_fieldset_title_displayed_class" ;
			$zig_div_fieldset_class = "zig_visible" ;
		}
		else {
			$zig_fieldset_class = "zig_fieldset_collapsed" ;
			$zig_fieldset_title_class = "zig_fieldset_title_collapsed_class" ;
			$zig_div_fieldset_class = "zig_invisible" ;
		}

		$title = str_replace("_"," ",$name) ;
		$title = ucwords(trim($title)) ;
		$title = htmlspecialchars($title,ENT_QUOTES) ;
		$title = str_replace(" ","&nbsp;",$title) ;

		$description_buffer = NULL ;
		switch($description) {
			case "":
			case NULL: {
				break ;
			}
			default: {
				$description_buffer = zig("template","block","fieldset","description") ;
				$description_buffer = str_replace("{description}",$description,$description_buffer) ;
				break ;
			}
		}

		$buffer = zig("template","block","fieldset",$template_block) ;
		$buffer = str_replace("{unique_name}",$unique_name,$buffer) ;
		$buffer = str_replace("{zig_fieldset_class}",$zig_fieldset_class,$buffer) ;
		$buffer = str_replace("{title}",$title,$buffer) ;
		$buffer = str_replace("{zig_div_fieldset_class}",$zig_div_fieldset_class,$buffer) ;
		$buffer = str_replace("{zig_fieldset_title_class}",$zig_fieldset_title_class,$buffer) ;
		$buffer = str_replace("{description}",$description_buffer,$buffer) ;
		$buffer = str_replace("{content}",$content,$buffer) ;
		$buffer = str_replace("{callback}",$callback,$buffer) ;
		$zig_result['value'] = $zig_result['html'] = $buffer ;

		return $zig_result ;
	}
}

?>