
function zig_preview(div_id,action)
{switch(action)
{case"show":{document.getElementById(div_id).className="zig_image_shown_preview";break;}
case"hide":{document.getElementById(div_id).className="zig_image_hidden_preview";break;}}}
function zig_preview_set(div_id)
{document.getElementById(div_id).className="zig_image_shown_preview";}
function zig_file_remove(div_id,current_field_name)
{var buffer_hidden=document.getElementById(div_id+"_hidden").innerHTML;buffer_hidden=buffer_hidden.replace("_hidden","");buffer=document.getElementById(div_id).innerHTML;buffer=buffer.replace(current_field_name,current_field_name+"_hidden");document.getElementById(div_id+"_hidden").innerHTML=buffer;document.getElementById(div_id).innerHTML=buffer_hidden;}
function zig_file_undo_remove(div_id)
{var buffer_hidden=document.getElementById(div_id+"_hidden").innerHTML;buffer_hidden=buffer_hidden.replace("_hidden","");document.getElementById(div_id+"_hidden").innerHTML=document.getElementById(div_id).innerHTML;document.getElementById(div_id).innerHTML=buffer_hidden;}