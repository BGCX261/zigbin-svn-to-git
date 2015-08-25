
function insertRowToTable(table_name,model)
{var table=document.getElementById(table_name);var rowToInsertAt=table.tBodies[0].rows.length;for(var i=0;i<table.tBodies[0].rows.length;i++)
{if(table.tBodies[0].rows[i].myRow&&table.tBodies[0].rows[i].myRow.four.getAttribute('type')=='radio'&&table.tBodies[0].rows[i].myRow.four.checked)
{rowToInsertAt=i;break;}}
addRowToTable(rowToInsertAt,table_name,model);}
function addRowToTable(row_number,table_name,model)
{var table=document.getElementById(table_name);if(row_number==null)
{row_number=table.tBodies[0].rows.length;}
var row=table.tBodies[0].insertRow(row_number);var child_row_model_html=document.getElementById(model).innerHTML;child_row_model_html=child_row_model_html.replace("{row_count}",row_number);child_row_model_html=child_row_model_html.replace("<table>","");child_row_model_html=child_row_model_html.replace("<tbody>","");child_row_model_html=child_row_model_html.replace("<tr>","");child_row_model_html=child_row_model_html.replace("</tr>","");child_row_model_html=child_row_model_html.replace("</tbody>","");child_row_model_html=child_row_model_html.replace("</table>","");child_row_model_html=child_row_model_html.replace(/_zig_child_row_count_{row_count}/g,"_zig_child_row_count_"+row_number);row.innerHTML=child_row_model_html;document.getElementById(table_name+"_children_id").value=Number(document.getElementById(table_name+"_children_id").value)+1;}
function deleteRows(rowObjArray)
{for(var i=0;i<rowObjArray.length;i++)
{var rIndex=rowObjArray[i].sectionRowIndex;rowObjArray[i].parentNode.deleteRow(rIndex);}}
function deleteCurrentRow(obj,table_name,hash_id)
{var delRow=obj.parentNode.parentNode;var table=delRow.parentNode.parentNode;var rIndex=delRow.sectionRowIndex;var rowArray=new Array(delRow);document.getElementById(table_name+"_children_id").value=Number(document.getElementById(table_name+"_children_id").value)-1;document.getElementById("zig_remove_"+table_name+"_id").value=document.getElementById("zig_remove_"+table_name+"_id").value?document.getElementById("zig_remove_"+table_name+"_id").value+","+hash_id:hash_id;deleteRows(rowArray);reorderRows(table,rIndex);}
function reorderRows(table,startingIndex)
{if(table.tBodies[0].rows[startingIndex])
{var count=startingIndex;var counter=0;var current_value=null;for(var i=startingIndex;i<table.tBodies[0].rows.length;i++)
{table.tBodies[0].rows[i].cells[0].innerHTML="<strong>"+count+"</strong>";var element_ids=table.tBodies[0].rows[i].innerHTML.split("id=");counter=0;while(counter<element_ids.length)
{var clean_element_id=element_id[counter].split(" ");clean_element_id[0]=clean_element_id[0].replace("'","");var field_id=clean_element_id[0].replace('"',"");if(field_id.search("zig_child_row_count")===true)
{current_value=document.getElementById(field_id).value}}
table.tBodies[0].rows[i].innerHTML=table.tBodies[0].rows[i].innerHTML.replace(RegExp("_zig_child_row_count_"+(count+1),"g"),"_zig_child_row_count_"+count);document.getElementById(field_id).value=current_value;count++;}}}