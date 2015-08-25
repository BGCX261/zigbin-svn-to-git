// -- start thumbnail preview
function zig_preview(div_id,action)
{
	switch(action)
	{
		case "show":
		{
			//var resource_id = setTimeout("document.getElementById('" + div_id + "').className='zig_image_shown_preview'",500) ;
			//document.getElementById(div_id).setAttribute("resource_id",resource_id) ;
			document.getElementById(div_id).className = "zig_image_shown_preview" ;
			break ;	
		}
		case "hide":
		{
			/*var resource_id = document.getElementById(div_id).getAttribute("resource_id") ;
			if(resource_id)
			{
				clearTimeout(resource_id) ;
			}*/
			document.getElementById(div_id).className = "zig_image_hidden_preview" ;
			break ;
		}
	}
}

function zig_preview_set(div_id)
{
	document.getElementById(div_id).className = "zig_image_shown_preview" ;
}
// -- end thumbnail preview


// -- start file remove
function zig_file_remove(div_id,current_field_name)
{
	var buffer_hidden = document.getElementById(div_id + "_hidden").innerHTML ;
	buffer_hidden = buffer_hidden.replace("_hidden","") ;
	buffer = document.getElementById(div_id).innerHTML ;
	buffer = buffer.replace(current_field_name,current_field_name + "_hidden") ;
	document.getElementById(div_id + "_hidden").innerHTML = buffer ;
	document.getElementById(div_id).innerHTML = buffer_hidden ;
	//document.getElementById(div_id).innerHTML = "<input " + invalid_field_class + " type='file' name='"+ current_field_name +"' " + field_size + " " + attribute_script + " />&nbsp;<a href='javascript: void(0) ;' onclick='javascript: undo() ;'>[undo remove]</a>" ;
}
// -- end file remove


// -- start file undo remove
function zig_file_undo_remove(div_id)
{
	var buffer_hidden = document.getElementById(div_id + "_hidden").innerHTML ;
	buffer_hidden = buffer_hidden.replace("_hidden","") ;
	document.getElementById(div_id + "_hidden").innerHTML = document.getElementById(div_id).innerHTML ;
	document.getElementById(div_id).innerHTML = buffer_hidden ;
}
// -- end file undo remove