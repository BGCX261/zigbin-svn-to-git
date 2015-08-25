<?php

class zig_trigger
{
	function trigger($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$action = $arg1 ;
			$id = $arg2 ;
		}
		else if(is_array($parameters))
		{
			$action = array_key_exists("action",$parameters) ? $parameters['action'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
		}
		$module = $GLOBALS['zig']['current']['module'] ;
		$script = $GLOBALS['zig']['current']['script'] ;
		$sql = "SELECT `zig_tabs`.`name`, `zig_tabs`.`module` 
					FROM `zig_tabs`,`zig_applications` 
					WHERE 
						`directory`='${module}' 
						AND `zig_tabs`.`module`=`zig_applications`.`name` 
						AND `zig_tabs`.`link`='${script}' 
					LIMIT 1" ;
		$result = zig("query",$sql) ;
		$fetch = $result->fetchRow() ;
		$application = $fetch['module'] ;
		$tab = $fetch['name'] ;
		$exclude_action = $this->trigger_permission("trigger_permission",$application,$tab,$action) ;
		if($action=="view" or $action=="listing")
		{
			//$buffer = $this->trigger_view_listing("trigger_view_listing",$action,$id,$exclude_action) ;
			$buffer = zig("template","block","trigger","triggers view") ;
		}
		else
		{
			//$buffer = $this->trigger_add_edit("trigger_add_edit",$application,$tab,$action) ;
			$buffer = zig("template","block","trigger","triggers add") ;
		}
		//$custom_triggers_buffer = $this->trigger_custom("trigger_custom",$application,$tab,$action) ;
		//$buffer = str_replace("{custom_triggers_images}",$custom_triggers_buffer['custom_triggers_images'],$buffer) ;
		//$buffer = str_replace("{custom_triggers_labels}",$custom_triggers_buffer['custom_triggers_labels'],$buffer) ;

		$zig_result['return'] = 1 ;
		$zig_result['value'] = $buffer ;
		return $zig_result ;
	}

	/*function trigger_view_listing($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$action = $arg1 ;
			$id = $arg2 ;
			$exclude_action = $arg3 ;
		}
		else if(is_array($parameters))
		{
			
		}
		$php_self = $_SERVER['PHP_SELF'] ;
		$buffer.= "<table>" ;
		$buffer.= "<tr>" ;
		$buffer.= "{custom_triggers_images}" ;

		if($action=="listing")
		{
			// -- Start view image
			$zig_hash_view = zig("hash","encrypt","view") ;
			$buffer.= "<th id='zig_trigger_view_image' class='zig_invisible'>" ;
			$buffer.= "<div id='div_zig_trigger_view_image' class='{zig_trigger_class}'>" ;
			$buffer.= "<a onclick=\"return listing_multiple_trigger('view','$zig_hash_view') ;\">" ;
			$image = zig("images","32x32/actions/viewmag.png") ;
			$buffer.= "<img src='$image' />" ;
			$buffer.= "</a>" ;
			$buffer.= "</div>" ;
			$buffer.= "</th>" ;
			// -- End view image
			
			if(!in_array("edit",$exclude_action))
			{
				// -- Start edit image
				$zig_hash_edit = zig("hash","encrypt","edit") ;
				$buffer.= "<th id='zig_trigger_edit_image' class='zig_invisible'>" ;
				$buffer.= "<div id='div_zig_trigger_edit_image' class='{zig_trigger_class}'>" ;
				$buffer.= $action=="listing" ? "<a onclick=\"return listing_multiple_trigger('edit','$zig_hash_edit') ;\">" : "<a>" ;
				$image = zig("images","32x32/actions/edit.png") ;
				$buffer.= "<img src='$image' />" ;
				$buffer.= "</a>" ;
				$buffer.= "</div>" ;
				$buffer.= "</th>" ;
				// -- End edit image
			}
			
			if(!in_array("copy",$exclude_action))
			{
			// -- Start copy image
				$zig_hash_copy = zig("hash","encrypt","copy") ;
				$buffer.= "<th id='zig_trigger_copy_image' class='zig_invisible'>" ;
				$buffer.= "<div id='div_zig_trigger_copy_image' class='{zig_trigger_class}'>" ;
				$buffer.= $action=="listing" ? "<a onclick=\"return listing_multiple_trigger('copy','$zig_hash_copy') ;\">" : "<a>" ;
				$image = zig("images","32x32/actions/editcopy.png") ;
				$buffer.= "<img src='$image' />" ;
				$buffer.= "</a>" ;
				$buffer.= "</div>" ;
				$buffer.= "</th>" ;
				// -- End copy image
			}
		}

		// -- Start print image
		$zig_hash_print = "action=print,id=".$id ;
		$zig_hash_print = zig("hash","encrypt",$zig_hash_print) ;
		$zig_link_print = $php_self."?zig_hash=".$zig_hash_print ;
		$zig_hash_print = zig("hash","encrypt","print") ;
		$buffer.= "<th id='zig_trigger_print_image' class='zig_actions_image'>" ;
		$buffer.= "<a target='_blank' href='$zig_link_print'>" ;
		$image = zig("images","32x32/devices/printer1.png") ;
		$buffer.= "<img src='$image' />" ;
		$buffer.= "</a>" ;
		$buffer.= "</th>" ;
		// -- End print image

		// -- Start export image
		$zig_hash_export = "action=export,id=".$id ;
		$zig_hash_export = zig("hash","encrypt",$zig_hash_export) ;
		$zig_link_export = $php_self."?zig_hash=".$zig_hash_export ;
		$buffer.= "<th id='zig_trigger_export_image' class='zig_actions_image'>" ;
		$buffer.= "<a href='$zig_link_export'>" ;
		$image = zig("images","32x32/actions/rotate_cc.png") ;
		$buffer.= "<img src='$image' />" ;
		$buffer.= "</a>" ;
		$buffer.= "</th>" ;
		// -- End export image

		// -- Start spacer
		$buffer.= "<td>" ;
		$buffer.= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ;
		$buffer.= "</td>" ;
		// -- End spacer
		
		if(!in_array("delete",$exclude_action))
		{
			// -- Start delete image
			$zig_hash_delete = "action=delete,id=".$id ;
			$zig_hash_delete = zig("hash","encrypt",$zig_hash_delete) ;
			$zig_link_delete = $php_self."?zig_hash=".$zig_hash_delete ;
			$zig_hash_delete = zig("hash","encrypt","delete") ;
			$buffer.= $action=="listing" ? "<th id='zig_trigger_delete_image' class='zig_invisible'>" : "<th id='zig_trigger_delete_image' class='zig_actions_image'>" ;
			$buffer.= "<div id='div_zig_trigger_delete_image' class='{zig_trigger_class}'>" ;
			$buffer.= $action=="listing" ? "<a onclick=\"return listing_multiple_trigger('delete','$zig_hash_delete') ;\">" : "<a onclick=\"return zig_confirmation('delete','$zig_link_delete')\">" ;
			$image = zig("images","32x32/actions/fileclose.png") ;
			$buffer.= "<img src='$image' />" ;
			$buffer.= "</a>" ;
			$buffer.= "</div>" ;
			$buffer.= "</th>" ;
			// -- End delete image
		}
		$buffer.= "</tr>" ;

		$buffer.= "<tr>" ;
		$buffer.= "{custom_triggers_labels}" ;
		if($action=="listing")
		{
			// -- Start view label
			$buffer.= "<td id='zig_trigger_view'>" ;
			$buffer.= "<div id='div_zig_trigger_view_label' class='{zig_trigger_class}' align='center'>" ;
			$buffer.= "<input type='hidden' id='zig_trigger_hash_view' value='$zig_hash_view' />" ;
			$buffer.= "<a id='zig_trigger_view' onclick=\"return listing_multiple_trigger('view','$zig_hash_view') ;\"  onmouseover=\"document.getElementById(this.id + '_image').className='zig_actions_image_hover';\" onmouseout=\"document.getElementById(this.id + '_image').className='zig_actions_image';\">" ;
			$buffer.= "View" ;
			$buffer.= "</a>" ;
			$buffer.= "</div>" ;
			$buffer.= "</td>" ;
			// -- End view label
			
			if(!in_array("edit",$exclude_action))
			{
				// -- Start edit label
				$buffer.= "<td id='zig_trigger_edit'>" ;
				$buffer.= "<div id='div_zig_trigger_edit_label' class='{zig_trigger_class}' align='center'>" ;
				$buffer.= "<input type='hidden' id='zig_trigger_hash_edit' value='$zig_hash_edit' />" ;
				$buffer.= $action=="listing" ? "<a id='zig_trigger_edit' onclick=\"return listing_multiple_trigger('edit','$zig_hash_edit') ;\" onmouseover=\"document.getElementById(this.id + '_image').className='zig_actions_image_hover';\" onmouseout=\"document.getElementById(this.id + '_image').className='zig_actions_image';\">" : "<a>" ;
				$buffer.= "Edit" ;
				$buffer.= "</a>" ;
				$buffer.= "</div>" ;
				$buffer.= "</td>" ;
				// -- End edit label
			}
			
			if(!in_array("copy",$exclude_action))
			{
				// -- Start copy label
				$buffer.= "<td id='zig_trigger_copy'>" ;
				$buffer.= "<div id='div_zig_trigger_copy_label' class='{zig_trigger_class}' align='center'>" ;
				$buffer.= "<input type='hidden' id='zig_trigger_hash_copy' value='$zig_hash_copy' />" ;
				$buffer.= $action=="listing" ? "<a id='zig_trigger_copy' onclick=\"return listing_multiple_trigger('copy','$zig_hash_copy') ;\" onmouseover=\"document.getElementById(this.id + '_image').className='zig_actions_image_hover';\" onmouseout=\"document.getElementById(this.id + '_image').className='zig_actions_image';\">" : "<a>" ;
				$buffer.= "Copy" ;
				$buffer.= "</a>" ;
				$buffer.= "</div>" ;
				$buffer.= "</td>" ;
				// -- End copy label
			}
		}

		// -- Start print label
		$buffer.= "<td id='zig_trigger_print' onmouseover=\"document.getElementById(this.id + '_image').className='zig_actions_image_hover';\" onmouseout=\"document.getElementById(this.id + '_image').className='zig_actions_image';\">" ;
		$buffer.= "<div align='center'>" ;
		$buffer.= "<input type='hidden' id='zig_trigger_hash_print' value='$zig_hash_print' />" ;
		$buffer.= "<a target='_blank' href='$zig_link_print'>" ;
		$buffer.= "Print" ;
		$buffer.= "</a>" ;
		$buffer.= "</div>" ;
		$buffer.= "</td>" ;
		// -- End print label

		// -- Start export label
		$buffer.= "<td id='zig_trigger_export' onmouseover=\"document.getElementById(this.id + '_image').className='zig_actions_image_hover';\" onmouseout=\"document.getElementById(this.id + '_image').className='zig_actions_image';\">" ;
		$buffer.= "<div align='center'>" ;
		$buffer.= "<a href='$zig_link_export'>" ;
		$buffer.= "Export" ;
		$buffer.= "</a>" ;
		$buffer.= "</div>" ;
		$buffer.= "</td>" ;
		// -- End export label

		$buffer.= "<td>" ;
		$buffer.= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ;
		$buffer.= "</td>" ;

		if(!in_array("delete",$exclude_action))
		{
			// -- Start delete label
			$buffer.= "<td id='zig_trigger_delete' onmouseover=\"document.getElementById(this.id + '_image').className='zig_actions_image_hover';\" onmouseout=\"document.getElementById(this.id + '_image').className='zig_actions_image';\">" ;
			$buffer.= "<div id='div_zig_trigger_delete_label' class='{zig_trigger_class}' align='center'>" ;
			$zig_hash = zig("hash","encrypt","delete") ;
			$buffer.= "<input type='hidden' id='zig_trigger_hash_delete' value='$zig_hash_delete' />" ;
			$buffer.= $action=="listing" ? "<a id='zig_trigger_delete' onclick=\"listing_multiple_trigger('delete','$zig_hash_delete') ;\" onmouseover=\"document.getElementById(this.id + '_image').className='zig_actions_image_hover';\" onmouseout=\"document.getElementById(this.id + '_image').className='zig_actions_image';\">" : "<a onclick=\"return zig_confirmation('delete','$zig_link_delete')\" onmouseover=\"document.getElementById(this.id + '_image').className='zig_actions_image_hover';\" onmouseout=\"document.getElementById(this.id + '_image').className='zig_actions_image';\">" ;
			$buffer.= "Delete" ;
			$buffer.= "</a>" ;
			$buffer.= "</div>" ;
			$buffer.= "</td>" ;
			// -- End delete label
		}
		$buffer.= "</tr>" ;
		$buffer.= "</table>" ;

		$template = zig("template","block","trigger","triggers") ;
		$buffer = str_replace("{trigger}",$buffer,$template) ;

		if($action=="listing")
		{
			$buffer = str_replace("{zig_trigger_class}","zig_invisible",$buffer) ;
		}
		else if($action=="view")
		{
			$buffer = str_replace("{zig_trigger_class}","zig_visible",$buffer) ;
		}
		return $buffer ;
	}*/

	/*function trigger_add_edit($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$application = $arg1 ;
			$tab = $arg2 ;
			$action = $arg3 ;
		}
		if(is_array($parameters))
		{
			
		}
		$buffer = zig("template","block","trigger","trigger add") ;
		$buffer = str_replace("{form_name}","form_zig_".$action,$buffer) ;

		$app_link = "../".$GLOBALS['zig']['current']['module'] ;
		$buffer = str_replace("{app_link}",$app_link,$buffer) ;

		$image = zig("images","32x32/devices/3floppy_mount.png") ;
		$buffer = str_replace("{save_image}",$image,$buffer) ;

		$image = zig("images","32x32/actions/reload.png") ;
		$buffer = str_replace("{reset_image}",$image,$buffer) ;
		return $buffer ;
	}*/

	// -- Start Custom Image Trigger
	/*function trigger_custom($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$application = $arg1 ;
			$tab = $arg2 ;
			$action = $arg3 ;
		}
		if(is_array($parameters))
		{
			
		}
		$action = $action=="listing" ? "search" : $action ;
		$custom_trigger_image_template = NULL ;
		$custom_trigger_label_template = NULL ;
		$custom_trigger_sql = "SELECT `label`,`icon`,`href`,`attributes`,`hash` FROM `zig_actions_triggers` WHERE (`module`='all' OR `module`='${application}') AND (`tab`='all' OR `tab`='${tab}') AND (`action`='all' OR `action`='${action}') AND `type`='trigger' AND `zig_status`<>'deleted' ORDER BY `zig_weight`,`label` ASC" ;
		$custom_trigger_result = zig("query",$custom_trigger_sql) ;
		while($custom_trigger_fetch=$custom_trigger_result->fetchRow())
		{
			$custom_trigger_image_template = zig("template","block","trigger","trigger image") ;
			$custom_trigger_label_template = zig("template","block","trigger","trigger label") ;
			$custom_name = str_replace(" ","_",$custom_trigger_fetch['label']) ;
			$custom_href = $custom_trigger_fetch['href'] ? 'href="'.str_replace('"','\"',$custom_trigger_fetch['href']).'"' : NULL ;
			$custom_attributes = $custom_trigger_fetch['attributes'] ;
			$custom_icon = zig("images",$custom_trigger_fetch['icon']) ;
			if($custom_trigger_fetch['hash'])
			{
				$custom_trigger_hash = str_replace("{id}",$id,$custom_trigger_fetch['hash']) ;
				$custom_trigger_hash = str_replace("{name}",$custom_name,$custom_trigger_hash) ;
				$custom_trigger_hash = zig("hash","encrypt",$custom_trigger_hash) ;
			}

			$custom_trigger_image_template = str_replace("{trigger_image_th_id}","zig_trigger_${custom_name}_image",$custom_trigger_image_template) ;
			$custom_trigger_image_template = str_replace("{trigger_image_th_class}","zig_actions_image",$custom_trigger_image_template) ;
			$custom_trigger_image_template = str_replace("{trigger_image_div_id}","div_zig_trigger_image_div_id_${custom_name}'",$custom_trigger_image_template) ;
			$custom_trigger_image_template = str_replace("{trigger_image_anchor_href}",$custom_href,$custom_trigger_image_template) ;
			$custom_trigger_image_template = str_replace("{trigger_image_anchor_attributes}",$custom_attributes,$custom_trigger_image_template) ;
			$custom_trigger_image_template = str_replace("{zig_hash}",$custom_trigger_hash,$custom_trigger_image_template) ;
			$custom_trigger_image_template = str_replace("{trigger_label}",$custom_trigger_fetch['label'],$custom_trigger_image_template) ;
			$custom_trigger_image_template = str_replace("{trigger_image_source}",$custom_icon,$custom_trigger_image_template) ;

			$custom_trigger_label_template = str_replace("{trigger_label_td_id}","zig_trigger_${custom_name}",$custom_trigger_label_template) ;
			$custom_trigger_label_template = str_replace("{trigger_label_div_id}","zig_trigger_label_div_id_${custom_name}",$custom_trigger_label_template) ;
			$custom_trigger_label_template = str_replace("{trigger_label_anchor_id}","zig_trigger_label_anchor_id_${custom_name}",$custom_trigger_label_template) ;
			$custom_trigger_label_template = str_replace("{trigger_label_anchor_href}",$custom_href,$custom_trigger_label_template) ;
			$custom_trigger_label_template = str_replace("{trigger_label_anchor_attributes}",$custom_attributes,$custom_trigger_label_template) ;
			$custom_trigger_label_template = str_replace("{zig_hash}",$custom_trigger_hash,$custom_trigger_label_template) ;
			$custom_trigger_label_template = str_replace("{trigger_label}",$custom_trigger_fetch['label'],$custom_trigger_label_template) ;
					
			$buffer['custom_triggers_images'].= $custom_trigger_image_template ;
			$buffer['custom_triggers_labels'].= $custom_trigger_label_template ;
		}
		return $buffer ;
	}*/
	// -- Start Custom Image Trigger

	// -- Start Get Permission
	function trigger_permission($parameters,$arg1,$arg2,$arg3)
	{
		if($arg1 or $arg2 or $arg3)
		{
			$application = $arg1 ;
			$tab = $arg2 ;
			$action = $arg3 ;
		}
		if(is_array($parameters))
		{
			
		}
		$module = $GLOBALS['zig']['current']['module'] ;
		$user = zig("info", "user");
		$sql_permission = "SELECT `tab`,`action`,`permission` FROM `zig_permissions` WHERE `users`='${user}' AND (`module`='${module}' OR `module`='all') AND `zig_status`<>'deleted'";
		$table_permissions = zig("query",$sql_permission);
		$exclude_action = array();
		while($permissions = $table_permissions->fetchRow())
		{
			if(($permissions['permission']=='deny') and ($permissions['tab']==$tab or $permissions['tab']=="all"))
			{
				$exclude_action[] = $permissions['action'];
			}
		}
		return $exclude_action ;
	}
	// -- End Get Permission
}

?>