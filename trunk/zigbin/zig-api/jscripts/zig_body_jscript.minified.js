
function checkdate(m,d,y){return m>0&&m<13&&y>0&&y<32768&&d>0&&d<=(new Date(y,m,0)).getDate();}
function countdown(span_id,element_id,maximum,tolerance,minimum)
{if(maximum)
{var characters=document.getElementById(element_id).value.length;var remaining=maximum-characters;var percentage_tolerance=maximum*(tolerance/100);if(remaining<percentage_tolerance||minimum>=remaining)
{document.getElementById(span_id).innerHTML="["+remaining+"]";}
else
{document.getElementById(span_id).innerHTML="";}}}
function enableButton(button,newValue)
{button.value=newValue;button.disabled=false;}
function disableButton(button,newValue)
{button.value=newValue;button.disabled=true;}
function mktime(){var d=new Date(),r=arguments,i=0,e=['Hours','Minutes','Seconds','Month','Date','FullYear'];for(i=0;i<e.length;i++){if(typeof r[i]==='undefined'){r[i]=d['get'+e[i]]();r[i]+=(i===3);}else{r[i]=parseInt(r[i],10);if(isNaN(r[i])){return false;}}}
r[5]+=(r[5]>=0?(r[5]<=69?2e3:(r[5]<=100?1900:0)):0);d.setFullYear(r[5],r[3]-1,r[4]);d.setHours(r[0],r[1],r[2]);return(d.getTime()/1e3>>0)-(d.getTime()<0);}
function zig_fieldset(fieldset_id,div_id,callback)
{var zig_fieldset=document.getElementById(fieldset_id);var zig_div_fieldset=document.getElementById(div_id);var fieldset_class=zig_fieldset.className;if(fieldset_class=="zig_fieldset_displayed")
{zig_fieldset.className="zig_fieldset_collapsed";zig_div_fieldset.className="zig_invisible";}
else
{zig_fieldset.className="zig_fieldset_displayed";zig_div_fieldset.className="";}
if(callback)
{setTimeout(callback,0);}}
function zig_keycode(e)
{var characterCode;if(e&&e.which)
{e=e;characterCode=e.which;}
else
{e=event;characterCode=e.keyCode;}
return characterCode;}
var suggest_div_id;function suggest_toggle(id,action)
{action=action?action:(document.getElementById(id).style.display=="block"?"hide":"show");if(document.getElementById(id))
{switch(action)
{case"show":{if(document.getElementById(suggest_div_id))
{document.getElementById(id).setAttribute("pointer","out");document.getElementById(suggest_div_id).style.display="none";}
document.getElementById(id).style.display="block";break;}
default:{document.getElementById(id).setAttribute("pointer","out");document.getElementById(id).style.display="none";break;}}
suggest_div_id=id;}}
function suggest_toggle_hide()
{if(document.getElementById(suggest_div_id))
{switch(document.getElementById(suggest_div_id).getAttribute("pointer"))
{case"over":{break;}
default:{document.getElementById(suggest_div_id).setAttribute("pointer","over");suggest_toggle(suggest_div_id,"hide");}}}}
function suggest_select(id,input_id,selected_value)
{document.getElementById(input_id).value=selected_value;suggest_toggle(id,"hide");}
function suggest_keyboard(id,input_id,e)
{var keycode=zig_keycode(e);var div_elements=document.getElementById(id).getElementsByTagName("div");var number_of_options=document.getElementById(id).getElementsByTagName("div").length;var current_option=-1;var counter=0;while(counter<number_of_options)
{if(div_elements.item(counter).className.search("_selected")!=-1)
{current_option=counter;break;}
counter++;}
switch(keycode)
{case 13:{suggest_select(id,input_id,div_elements.item(current_option).innerHTML);break;}
case 38:{switch(current_option)
{case-1:case 0:{var new_option=number_of_options-1;break;}
default:{var new_option=current_option-1;break;}}
break;}
case 40:{switch(current_option)
{case(number_of_options-1):{var new_option=0;break;}
default:{var new_option=current_option+1;break;}}
break;}}
switch(current_option)
{case-1:{break;}
default:{div_elements.item(current_option).className=div_elements.item(current_option).className.replace("_selected","");break;}}
div_elements.item(new_option).className=div_elements.item(new_option).className+"_selected";}
function suggest_filter(id,input_id,droplist_options_string)
{var input_filter=document.getElementById(input_id).value.toLowerCase();var first_character="";var droplist_options=droplist_options_string.split(",");var droplist_options_length=droplist_options.length;var new_div_options="";var new_div_options_counter=0;var counter=0;var number_of_options=document.getElementById(id).getElementsByTagName("div").length;while(counter<droplist_options_length)
{first_character=droplist_options[counter].substr(0,input_filter.length);if(input_filter==first_character.toLowerCase()||(droplist_options[counter]==""&&counter==0)||input_filter=="")
{new_div_options+="<div class='zig_droplist_suggest_div_option' onclick=\"suggest_select('"+id+"','"+input_id+"','"+droplist_options[counter]+"') ;\">"+droplist_options[counter]+"</div>";new_div_options_counter++;}
counter++;}
if((new_div_options.length&&counter!=new_div_options_counter)||(counter==new_div_options_counter&&number_of_options!=new_div_options_counter))
{document.getElementById(id).innerHTML=new_div_options;suggest_toggle(id,"show");}
else if(!new_div_options.length)
{suggest_toggle(id,"hide");}}
zig_listener(document.body,"click",suggest_toggle_hide,false);function total_update(div_id,field_id)
{var total=0;var counter=1;var splitted_field_id=field_id.split("_");var row_number=splitted_field_id[splitted_field_id.length-2];var finding_field_pre=field_id.split(row_number+"_id");var field_pre=finding_field_pre[0];while(document.getElementById(field_pre+counter+"_id"))
{total=total+Number(document.getElementById(field_pre+counter+"_id").value);counter++;}
document.getElementById(div_id).innerHTML=addCommas(total.toFixed(2));}
function addCommas(nStr)
{nStr+='';x=nStr.split('.');x1=x[0];x2=x.length>1?'.'+x[1]:'';var rgx=/(\d+)(\d{3})/;while(rgx.test(x1))
{x1=x1.replace(rgx,'$1'+','+'$2');}
return x1+x2;}
function zig_confirmation(action,redirection_link)
{var response=confirm("Are you sure you want to "+action+" this item(s)?");if(response)
{window.location=redirection_link;}
return false;}
function isArray(obj)
{return(typeof(obj.length)=="undefined")?false:true;}
function zig(container,parameters,arg1,arg2,arg3,arg4)
{if(!arg1&&!arg2&&!arg3&&!parameters&&container&&!((container instanceof Array)||(typeof(container)=='object')))
{var url=container;container=null;}
else if(parameters&&!(parameters instanceof Array))
{var arguments="";if(arg1)
{arguments="&arg1="+arg1;}
if(arg2)
{arguments+="&arg2="+arg2;}
if(arg3)
{arguments+="&arg3="+arg3;}
if(arg4)
{arguments+="&"+arg4;}
var url="../zig-api/decoder.php?zig_hash="+parameters+arguments;if((container instanceof Array)||(container!==null&&typeof(container)=='object'))
{container['zig_hash']=parameters;}}
else if((container instanceof Array)||(container!==null&&typeof(container)=='object'))
{var url=container['file'];}
var splittedFile=url.split("?");var content="";switch(splittedFile.length>1)
{case true:{url=splittedFile[0];content=splittedFile[1];break;}}
return zigjax(url,container,content);}
function zigjax(url,parameters,content)
{var method=content?"post":"get";var custom_parameters=new Array();if((parameters instanceof Array)||(parameters!==null&&typeof(parameters)=='object'))
{if(parameters['form_id']!=undefined&&parameters['form_id']!="")
{var content=zigjax_form_encoder(parameters['form_id']);content=parameters['zig_hash']!=undefined?"zig_hash="+parameters['zig_hash']+"&"+content:content;method="post";}
else
{method=(parameters['method']&&parameters['method']!=undefined)?parameters['method']:method;}
if(parameters['loader']==undefined)
{var loader=true;}
else
{var loader=parameters['loader'];}
if(parameters['loaderDiv']==undefined)
{var loaderDiv=parameters['container'];}
else
{var loaderDiv=parameters['loaderDiv'];}
var container=parameters['container'];custom_parameters['return_function']=parameters['return_function'];}
else
{var container=parameters;var loaderDiv=container;var loader=true;}
if(document.getElementById(loaderDiv)&&loader)
{document.getElementById(loaderDiv).innerHTML="<div id='zig_div_ajax_loader'><img src='../zig-api/gui/themes/default/img/16x16/actions/ajax-loader.gif' /></div>";}
else if(document.getElementById(container))
{document.getElementById(container).innerHTML="&nbsp;";}
custom_parameters['loaderDiv']=loaderDiv;custom_parameters['container']=container;zigjax_request(url,method,content,custom_parameters);}
function zigjax_form_encoder(form_id)
{var encoded_form="";var elementValue="";if(document.getElementById(form_id))
{var elements=document.getElementById(form_id).elements;var counter=0;while(counter<elements.length)
{if(elements[counter].name!=undefined)
{switch(elements[counter].type)
{case"file":{if(document.getElementById(elements[counter].id+"_zigHiddenFileName"))
{if(document.getElementById(elements[counter].id+"_zigHiddenFileName").value!="")
{elementValue=document.getElementById(elements[counter].id+"_zigHiddenFileName").value;break;}}}
default:{elementValue=elements[counter].value;}}
encoded_form=encoded_form?encoded_form+"&":encoded_form;encoded_form+=elements[counter].name+"="+encodeURI(elementValue);}
counter++;}}
return encoded_form;}
var zigResponse="";var zigResponseObject="";function zigjax_request(file,method,content,custom_parameters)
{var xmlHttp;try
{xmlHttp=new XMLHttpRequest();}
catch(e)
{try
{xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");}
catch(e)
{try
{xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");}
catch(e)
{alert("Your browser does not support AJAX!");return false;}}}
xmlHttp.onreadystatechange=function()
{if(xmlHttp.readyState==4)
{zigResponse=xmlHttp.responseText;var loaderDiv=custom_parameters['loaderDiv'];var container=custom_parameters['container'];var return_function=custom_parameters['return_function'];if(document.getElementById(loaderDiv))
{document.getElementById(loaderDiv).innerHTML="&nbsp;";}
try{zigResponseObject=JSON.parse(zigResponse);if(zigResponseObject.html){document.getElementById(container).innerHTML=zigResponseObject.html;}
else if(document.getElementById(container)){document.getElementById(container).innerHTML=zigResponse;}}
catch(error){if(document.getElementById(container)){document.getElementById(container).innerHTML=zigResponse;}}
if(return_function)
{setTimeout(return_function,0);}}}
switch(method)
{case"post":{xmlHttp.open("POST",file,true);xmlHttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");xmlHttp.send(content);break;}
default:{xmlHttp.open("GET",file,true);xmlHttp.send(null);break;}}}
function updatePageTitle(type,newString){var explodedTitle=document.title.split(" | ");var application=explodedTitle[0];var tab=explodedTitle[1];var mode=explodedTitle[2];switch(type){case"application":{application=newString;break;}
case"tab":{tab=newString;break;}
case"mode":{mode=newString;break;}}
var newTitle=application;if(tab!=""){newTitle=newTitle+" | "+tab;}
if(mode!=""){newTitle=newTitle+" | "+mode;}
document.title=newTitle;}
function zig_tabs(table_id,cell_number,tab_link){var counter=0;var cells_length=document.getElementById(table_id).rows[0].cells.length;updatePageTitle("tab",document.getElementById("zig_tab_link_name_"+cell_number).innerHTML);while(counter<cells_length){switch(document.getElementById(table_id).rows[0].cells[counter].className){case"zig_td_tab":{break;}
default:{document.getElementById(table_id).rows[0].cells[counter].className="zig_td_tab";document.getElementById("zig_tab_link_name_"+counter).className="";}}
counter++;}
document.getElementById(table_id).rows[0].cells[cell_number].className="zig_tab_active";document.getElementById("zig_tab_link_name_"+cell_number).className="zig_tab_div_active";zigjax(tab_link,'div_zig_body_wrapper');}
function zig_hidden_iframe(zig_hash)
{var iframe=document.createElement("iframe");iframe.src="../zig-api/decoder.php?zig_hash="+zig_hash;iframe.style.display="none";document.body.appendChild(iframe);}
function zig_editField(viewFieldContainerID,editFieldContainerID,editFieldID,fileLinksID,uploadLinksID)
{switch(document.getElementById(viewFieldContainerID).className)
{case"zig_invisible":{break;}
default:{switch(document.getElementById(editFieldID).type)
{case"file":{if(document.getElementById(viewFieldContainerID).innerHTML.replace(/&nbsp;/g,"")!="")
{break;}}
default:{resetToEdit(viewFieldContainerID,editFieldContainerID,editFieldID,fileLinksID,uploadLinksID);}}}}}
function zig_viewField(viewFieldContainerID,editFieldContainerID,editFieldObject,zigHash,fieldName,fileLinksID,uploadLinksID,passedUniqueString)
{switch(document.getElementById(viewFieldContainerID).className)
{case"zig_display":{break;}
default:{switch(editFieldObject.tagName.toLowerCase())
{case"select":{viewValue=editFieldObject.options[editFieldObject.selectedIndex].text;break;}
default:{viewValue=editFieldObject.value;}}
var viewValue=viewValue==""?"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;":viewValue;switch(document.getElementById(viewFieldContainerID).innerHTML!=viewValue)
{case true:{switch(editFieldObject.type)
{case"file":{switch(editFieldObject.value!="")
{case true:{document.getElementById(viewFieldContainerID).innerHTML="Uploading...";uploadFile(editFieldObject,zigHash,fieldName,viewFieldContainerID,"divList_"+passedUniqueString);break;}}
break;}
case"password":{switch(editFieldObject.value!="")
{case true:{zig("divList_"+passedUniqueString,zigHash,'','','',fieldName+"="+editFieldObject.value);editFieldObject.value="";}}
break;}
default:{document.getElementById(viewFieldContainerID).innerHTML=viewValue;zig({"container":"divList_"+passedUniqueString,"return_function":"modifiedRecord()"},zigHash,'','','',fieldName+"="+editFieldObject.value);}}}}
resetToView(viewFieldContainerID,editFieldContainerID,fileLinksID,uploadLinksID);}}}
function modifiedRecord(){if(zigResponseObject.data.validation){zigMessenger(zigResponseObject.data.message);}}
function resetToView(viewFieldContainerID,editFieldContainerID,fileLinksID,uploadLinksID)
{document.getElementById(viewFieldContainerID).className="zig_display";document.getElementById(editFieldContainerID).className="zig_invisible";if(document.getElementById(uploadLinksID))
{document.getElementById(uploadLinksID).className="zig_invisible";}
if(document.getElementById(viewFieldContainerID).innerHTML.replace(/&nbsp;/g,"")!="")
{if(document.getElementById(fileLinksID))
{document.getElementById(fileLinksID).className="zig_display";}}}
function resetToEdit(viewFieldContainerID,editFieldContainerID,editFieldID,fileLinksID,uploadLinksID)
{document.getElementById(viewFieldContainerID).className="zig_invisible";document.getElementById(editFieldContainerID).className="zig_display";document.getElementById(editFieldID).focus();if(document.getElementById(fileLinksID))
{document.getElementById(fileLinksID).className="zig_invisible";}
if(document.getElementById(uploadLinksID))
{document.getElementById(uploadLinksID).className="zig_display";}}
function removeFile(viewFieldContainerID,editFieldContainerID,editFieldObject,zigHash,fieldName,fileLinksID,uploadLinksID)
{switch(confirm("Are you sure you want to remove this file?"))
{case true:{document.getElementById(viewFieldContainerID).innerHTML="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";resetToView(viewFieldContainerID,editFieldContainerID,fileLinksID,uploadLinksID);document.getElementById(fileLinksID).className="zig_invisible";zig("",zigHash,'','','',fieldName+"=");}}}
function viewCalendarField(viewFieldContainerID,editFieldContainerID,editFieldObject,zigHash,fieldName)
{delay(50);switch(document.getElementById("CalendarPickerControl").style.visibility=="visible")
{case false:{zig_viewField(viewFieldContainerID,editFieldContainerID,editFieldObject,zigHash,fieldName);}}}
function delay(milliseconds)
{var startDate=new Date();var currentDate=null;do
{currentDate=new Date();}
while(currentDate-startDate<milliseconds);}
function loadReportFilters(zigHash,reportName)
{document.getElementById("divReportContent").innerHTML="";document.getElementById("divPrintIconHolderId").style.visibility="hidden";zig('divReportFilters',zigHash,reportName);}
function startReport(file)
{document.getElementById("divPrintIconHolderId").style.visibility="visible";zig({'file':file,'container':'divReportContent','form_id':'formReportFilters'});}
function zigMessenger(message)
{document.getElementById("div_zig_message").innerHTML=message;setTimeout(function(){document.getElementById("div_zig_message").innerHTML="";},4000);}
function searchKeypress(searchInputElement,zigHash,uniqueString,event)
{switch(zig_keycode(event))
{case 13:{zig('divList_'+uniqueString,zigHash,'','','','zig_keyword='+searchInputElement.value);event.returnValue=false;return false;}}}
function searchLabelFocus(searchInputElement)
{switch(searchInputElement.className=="inputBasicSearchLabel")
{case true:{searchInputElement.value="";searchInputElement.className="inputBasicSearch";}}}
function searchLabelBlur(searchInputElement)
{switch(searchInputElement.value=="")
{case true:{searchInputElement.className="inputBasicSearchLabel";searchInputElement.value="Search Records";break;}}}
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
var viewFieldContainerIdGlobal="";var listContainerIdGlobal="";var fileObjectGlobal="";function uploadFile(fileObject,zigHash,fieldName,viewFieldContainerId,listContainerId)
{viewFieldContainerIdGlobal=viewFieldContainerId;listContainerIdGlobal=listContainerId;fileObjectGlobal=fileObject;var fd=new FormData();fd.append(fieldName,fileObject.files[0]);var xhr=new XMLHttpRequest();xhr.upload.addEventListener("progress",uploadProgress,false);xhr.addEventListener("load",uploadComplete,false);xhr.addEventListener("error",uploadFailed,false);xhr.addEventListener("abort",uploadCanceled,false);document.getElementById(viewFieldContainerIdGlobal).innerHTML="<div id='divFileBarUploadingId' class='progressBarInProgress'></div>";xhr.open("POST","../zig-api/decoder.php?zig_hash="+zigHash+"&"+fieldName+"="+fileObject.value);xhr.send(fd);fileObject.value="";}
function uploadProgress(evt)
{if(evt.lengthComputable)
{var percentComplete=Math.round(evt.loaded*100/evt.total);document.getElementById("divFileBarUploadingId").style.width=percentComplete.toString()+'%';}
else
{document.getElementById(viewFieldContainerIdGlobal).innerHTML="Uploading...";}}
function uploadComplete(evt)
{var response=JSON.parse(evt.target.responseText);document.getElementById("divFileBarUploadingId").className="progressBarComplete";zigMessenger("File Uploaded");if(response['listing'])
{document.getElementById(listContainerIdGlobal).innerHTML=response['listing'];}
document.getElementById(viewFieldContainerIdGlobal).innerHTML=response['html'];document.getElementById(fileObjectGlobal.id+"_zigHiddenFileName").value=response['field_value'];}
function uploadFailed(evt)
{document.getElementById("divFileBarUploadingId").className="progressBarError";zigMessenger("There was an error attempting to upload the file");}
function uploadCanceled(evt)
{document.getElementById("divFileBarUploadingId").className="progressBarError";zigMessenger("The upload has been canceled by the user or the browser dropped the connection");}
function uploadResize(file)
{var img=document.createElement("img");img.src=window.URL.createObjectURL(file);var canvas=document.createElement("canvas");var ctx=canvas.getContext("2d");ctx.drawImage(img,0,0);var bw=img.width;var bh=img.height;alert("w:"+bw+" bh:"+bh);alert(" w:"+img.width+" h:"+img.height);var MAX_WIDTH=800;var MAX_HEIGHT=600;var width=img.width;var height=img.height;if(width>height)
{if(width>MAX_WIDTH)
{height*=MAX_WIDTH/width;width=MAX_WIDTH;}}
else
{if(height>MAX_HEIGHT)
{width*=MAX_HEIGHT/height;height=MAX_HEIGHT;}}
canvas.width=width;canvas.height=height;var ctx=canvas.getContext("2d");ctx.drawImage(img,0,0,width,height);return canvas;}
function listing_sort(direction,column_number,table_id){var column_content=new Array();var original_row_content=new Array();var original_row_node=new Array();var original_row_attributes=new Array();var original_row_attributes_name=new Array();var original_row_attributes_value=new Array();var row_length=document.getElementById(table_id).rows.length;var column_length=document.getElementById(table_id).rows[0].cells.length;var row=0,column=0;var counter
for(row=1;row_length>row;row++){original_row_content[row]=document.getElementById(table_id).rows[row].innerHTML;original_row_node[row]=document.getElementById(table_id).rows[row];original_row_attributes[row]=document.getElementById(table_id).rows[row].attributes;counter=0;original_row_attributes_name[row]=new Array();original_row_attributes_value[row]=new Array();while(counter<original_row_attributes[row].length)
{original_row_attributes_name[row][counter]=original_row_attributes[row][counter].name;original_row_attributes_value[row][counter]=original_row_attributes[row][counter].value;document.getElementById(table_id).rows[row].setAttribute(original_row_attributes[row][counter].name,"");counter++;}
column_content[row]=document.getElementById(table_id).rows[row].cells[column_number].innerHTML+"_row_"+row;}
var sorted_column=column_content.sort();var sequence=new Array();var ripped_content=new Array();for(row=0;row_length>row;row++)
{if(sorted_column[row])
{ripped_content=sorted_column[row].split("_row_");sequence[row+1]=ripped_content[1];}}
if(direction=="descending")
{sequence.reverse();sequence.unshift("");}
for(row=1;row_length>row;row++){document.getElementById(table_id).rows[row].innerHTML=original_row_content[sequence[row]];counter=0;while(counter<original_row_attributes_name[sequence[row]].length)
{if(original_row_attributes_value[sequence[row]][counter]!=""&&original_row_attributes_value[sequence[row]][counter]!=null)
{document.getElementById(table_id).rows[row].setAttribute(original_row_attributes_name[sequence[row]][counter],original_row_attributes_value[sequence[row]][counter]);}
counter++;}}}
function listing_sort_by_field(direction,field_name){document.getElementById('zig_listing_sort_direction_id').value=direction;document.getElementById('zig_listing_sort_field_id').value=field_name;document.zig_form_listing.submit();}
function listing_move(direction,column_number,table_id){var original_content=new Array();var row_length=document.getElementById(table_id).rows.length;var column_length=document.getElementById(table_id).rows[0].cells.length;var row=0;if(direction=="left")
{var other_column=Number(column_number)-1;while(other_column>=0)
{if(document.getElementById(table_id).rows[0].cells[other_column].style.display=="none")
{other_column--;}
else
{break;}}}
else if(direction=="right")
{var other_column=Number(column_number)+1;while(other_column<column_length)
{if(document.getElementById(table_id).rows[0].cells[other_column].style.display=="none")
{other_column++;}
else
{break;}}}
if(!(column_number==0&&direction=="left")&&!(column_number==(column_length-1)&&direction=="right")&&other_column>=0&&other_column<column_length)
{for(row=0;row_length>row;row++)
{original_content[row]=new Array();original_content[row][column_number]=document.getElementById(table_id).rows[row].cells[column_number].innerHTML;original_content[row][other_column]=document.getElementById(table_id).rows[row].cells[other_column].innerHTML;}
for(row=1;row_length>row;row++)
{document.getElementById(table_id).rows[row].cells[column_number].innerHTML=original_content[row][other_column];document.getElementById(table_id).rows[row].cells[other_column].innerHTML=original_content[row][column_number];}
var other_regexp=RegExp("zig_listing_column_"+other_column,"g");var column_regexp=RegExp("zig_listing_column_"+column_number,"g");document.getElementById(table_id).rows[0].cells[column_number].innerHTML=original_content[0][other_column].replace(other_regexp,"zig_listing_column_"+column_number);document.getElementById(table_id).rows[0].cells[other_column].innerHTML=original_content[0][column_number].replace(column_regexp,"zig_listing_column_"+other_column);}}
function listing_truncate(method,field_name){var truncate_option=method;if(truncate_option=="yes")
{document.getElementById("zig_listing_truncate_"+field_name+"_id").value="yes";}
else
{document.getElementById("zig_listing_truncate_"+field_name+"_id").value="no";}
document.zig_form_listing.submit();}
function listing_alignment(alignment,column_id,column_number,table_id){var row_length=document.getElementById(table_id).rows.length;for(row=1;row_length>row;row++)
{document.getElementById(table_id).rows[row].cells[column_number].align=alignment;}}
function listing_show_hide(method,column_number,table_id){var row_length=document.getElementById(table_id).rows.length;var action="";if(method=="hide")
{action="none";}
for(row=0;row<row_length;row++)
{document.getElementById(table_id).rows[row].cells[column_number].style.display=action;}}
function listing_nowrap(method,column_number,table_id){var row_length=document.getElementById(table_id).rows.length;if(method=="nowrap")
{var action="nowrap";}
else
{var action="";}
for(row=1;row_length>row;row++)
{document.getElementById(table_id).rows[row].cells[column_number].style.whiteSpace=action;}}
function check_uncheck(listingPrefix){var evaluate=document.getElementById(listingPrefix+"_all");if(evaluate.value==0)
{check_all(listingPrefix);}
else
{uncheck_all(listingPrefix);}}
function check_all(listingPrefix){var check=document.getElementById(listingPrefix+"_table").rows;for(c=1;c<check.length;c++)
{document.getElementById(listingPrefix+"_checkbox_"+c).checked=true;document.getElementById(listingPrefix+"_row_"+c).className="zig_listing_row_highlighted";}
document.getElementById(listingPrefix+"_all").value=1;}
function uncheck_all(listingPrefix){var uncheck=document.getElementById(listingPrefix+"_table").rows;for(c=1;c<uncheck.length;c++)
{document.getElementById(listingPrefix+"_checkbox_"+c).checked=false;document.getElementById(listingPrefix+"_row_"+c).className=document.getElementById(listingPrefix+"_class_"+c).value;}
document.getElementById(listingPrefix+"_all").value=0;}
hover_rows=function(){if(document.all&&document.getElementById)
{navRoot=document.getElementById('zig_listing_table');tbody=navRoot.childNodes[0];for(i=1;i<tbody.childNodes.length;i++)
{node=tbody.childNodes[i];if(node.nodeName=="TR")
{node.onmouseover=function()
{this.className="hovered";}
node.onmouseout=function()
{this.className=this.className.replace("hovered","");}}}}}
var timeout=500;var closetimer=0;var ddmenuitem=0;function mopen(id,current_event){mcancelclosetime();if(ddmenuitem)
{ddmenuitem.style.visibility="hidden";}
ddmenuitem=document.getElementById(id);ddmenuitem.style.visibility="visible";}
function mclose(){if(ddmenuitem)ddmenuitem.style.visibility="hidden";}
function mclosetime(){closetimer=window.setTimeout(mclose,timeout);}
function mcancelclosetime(){if(closetimer)
{window.clearTimeout(closetimer);closetimer=null;}}
function trim(str,chars){return ltrim(rtrim(str,chars),chars);}
function ltrim(str,chars){chars=chars||"\\s";return str.replace(new RegExp('^[" + chars + "]+',"g"),"");}
function rtrim(str,chars){chars=chars||"\\s";return str.replace(new RegExp('[" + chars + "]+$',"g"),"");}
function listingDelete(listingIDPrefix,zigHash){var tableObject=document.getElementById(listingIDPrefix+"_table");var checkboxPrefix=listingIDPrefix+"_checkbox_";var id="";var rowsToBeDeleted=new Array();switch(listingIsChecked(tableObject,checkboxPrefix)){case true:{var response=confirm("Are you sure you want to delete these/this record(s)?");switch(response){case true:{zigMessenger("Deleting...");var rows=tableObject.rows;for(counter=1;counter<rows.length;counter++){switch(document.getElementById(checkboxPrefix+counter)!=null){case true:{switch(document.getElementById(checkboxPrefix+counter).checked){case true:{id+=id?","+rows[counter].cells[1].innerHTML:rows[counter].cells[1].innerHTML;break;}}}}}
var parameters=new Array();parameters['container']=listingIDPrefix+"_div";parameters['return_function']=function(){listingDeletePostHandler(listingIDPrefix)};zig(parameters,zigHash,"",id);break;}}}}}
function listingDeletePostHandler(listingIDPrefix){if(zigResponseObject.data.message){zigMessenger(zigResponseObject.data.message);return false;}
else{zigMessenger("");}
var tableObject=document.getElementById(listingIDPrefix+"_table");var checkboxPrefix=listingIDPrefix+"_checkbox_";var id="";var rowsToBeDeleted=new Array();var rows=tableObject.rows;for(counter=1;counter<rows.length;counter++){switch(document.getElementById(checkboxPrefix+counter)!=null){case true:{switch(document.getElementById(checkboxPrefix+counter).checked){case true:{id+=id?","+rows[counter].cells[1].innerHTML:rows[counter].cells[1].innerHTML;rowsToBeDeleted[rowsToBeDeleted.length]=rows[counter].rowIndex;break;}}}}}
for(counter=0;counter<rowsToBeDeleted.length;counter++){tableObject.deleteRow(rowsToBeDeleted[counter]-(counter));}}
function listingIsChecked(tableObject,checkboxPrefix){var rows=tableObject.rows;for(counter=1;counter<rows.length;counter++)
{switch(document.getElementById(checkboxPrefix+counter)!=null)
{case true:{switch(document.getElementById(checkboxPrefix+counter).checked)
{case true:{return true;}}}}}
return false;}
function listingCheckCheckbox(object,zigHash){object.value==1?object.value=0:object.value=1;zig("",zigHash,'','','',object.name+"="+object.value);}
function listingEnableView(){document.getElementById("viewFlag").value=true;}
function listingDisableView(){document.getElementById("viewFlag").value=false;}
function listingAddRecord(buttonObject,uniqueString,saveHash){disableButton(buttonObject,"Saving");zig({loaderDiv:"spanSavingLoaderId",container:"divList_"+uniqueString,'form_id':uniqueString+'_form',return_function:"listingAddedRecord('"+buttonObject.id+"','"+uniqueString+"')"},saveHash);}
function listingAddedRecord(buttonObjectId,uniqueString){if(zigResponseObject.data.validation){zigMessenger("Record Added");if(document.getElementById("divNoRecordsId_"+uniqueString))
{document.getElementById("divAdd_"+uniqueString).innerHTML="";document.getElementById("div_zig_triggers_"+uniqueString).style.display="block";}
document.getElementById("divAdd_"+uniqueString).innerHTML=document.getElementById("divAddReference_"+uniqueString).innerHTML;}
else{zigMessenger(zigResponseObject.data.message);enableButton(document.getElementById(buttonObjectId),"Save");}}
function listingEditRecord(buttonObject,uniqueString,saveHash){disableButton(buttonObject,"Saving");zig({loaderDiv:"spanSavingLoaderId",container:"divList_"+uniqueString,'form_id':uniqueString+'_form',return_function:"listingEditedRecord('"+buttonObject.id+"','"+uniqueString+"')"},saveHash);}
function listingEditedRecord(buttonObjectId,uniqueString){if(zigResponseObject.data.validation){zigMessenger("Record Updated");}
else{zigMessenger(zigResponseObject.data.message);}
enableButton(document.getElementById(buttonObjectId),"Save");}
function listingView(uniqueString,zig_hash){switch(document.getElementById("viewFlag").value){case"true":{updatePageTitle("mode","View");var divID="divView_"+uniqueString;zig(divID,zig_hash);if(document.getElementById("div_zig_filters_"+uniqueString)){document.getElementById("div_zig_filters_"+uniqueString).style.display="none";}
document.getElementById("divList_"+uniqueString).style.display="none";document.getElementById(divID).style.display="block";}}}
function listingAdd(uniqueString,zigHash){updatePageTitle("mode","Add");var divID="divAdd_"+uniqueString;switch(document.getElementById(divID).innerHTML){case"":{zig({"container":""+divID+"","return_function":"listingAddReference('"+uniqueString+"')"},zigHash);}
default:{if(document.getElementById("div_zig_filters_"+uniqueString)){document.getElementById("div_zig_filters_"+uniqueString).style.display="none";}
document.getElementById("divList_"+uniqueString).style.display="none";document.getElementById(divID).style.display="block";}}}
function listingAddReference(uniqueString){document.getElementById("divAddReference_"+uniqueString).innerHTML=document.getElementById("divAdd_"+uniqueString).innerHTML;}
function listingBackToList(uniqueString){updatePageTitle("mode","Search");if(document.getElementById("div_zig_filters_"+uniqueString)){document.getElementById("div_zig_filters_"+uniqueString).style.display="block";}
document.getElementById("divList_"+uniqueString).style.display="block";document.getElementById("divAdd_"+uniqueString).style.display="none";document.getElementById("divView_"+uniqueString).style.display="none";}
function listingViewEdit(){switch(document.getElementById("listingViewEdit").value){case"Edit":{updatePageTitle("mode","Edit");document.getElementById("listingViewEdit").value="View";document.getElementById("divViewFields").style.display="none";document.getElementById("divEditFields").style.display="block";break;}
case"View":{updatePageTitle("mode","View");document.getElementById("listingViewEdit").value="Edit";document.getElementById("divEditFields").style.display="none";document.getElementById("divViewFields").style.display="block";break;}}}
DatePickerControl.defaultFormat="Y-m-d";DatePickerControl.submitFormat="";DatePickerControl.offsetY=1;DatePickerControl.offsetX=0;DatePickerControl.todayText="today";DatePickerControl.buttonTitle="calendar";DatePickerControl.buttonPosition="in";DatePickerControl.buttonOffsetX=0;DatePickerControl.buttonOffsetY=0;DatePickerControl.closeOnTodayBtn=true;DatePickerControl.defaultTodaySel=true;DatePickerControl.autoShow=false;DatePickerControl.firstWeekDay=0;DatePickerControl.weekend=[0,6];DatePickerControl.weekNumber=false;DatePickerControl.Months=["January","February","March","April","May","June","July","August","September","October","November","December"];DatePickerControl.Days=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];DatePickerControl.useTrickyBG=false;if(navigator.userAgent.indexOf("MSIE")>1){DatePickerControl.useTrickyBG=true;DatePickerControl.offsetY=0;DatePickerControl.offsetX=-1;DatePickerControl.buttonOffsetX=-4;DatePickerControl.buttonOffsetY=-2;if(document.getElementsByTagName("html")[0].getAttribute("xmlns")!=null){DatePickerControl.offsetY=16;DatePickerControl.offsetX=10;DatePickerControl.buttonOffsetX=8;DatePickerControl.buttonOffsetY=14;}}
DatePickerControl.editIdPrefix="cal_";DatePickerControl.displayed=false;DatePickerControl.HIDE_TIMEOUT=300;DatePickerControl.hideTimeout=null;DatePickerControl.buttonIdPrefix="CALBUTTON";DatePickerControl.dayIdPrefix="CALDAY";DatePickerControl.currentDay=1;DatePickerControl.originalValue="";DatePickerControl.calFrameId="calendarframe";DatePickerControl.submitByKey=false;DatePickerControl.dayOfWeek=0;DatePickerControl.firstFocused=false;DatePickerControl.hideCauseBlur=true;DatePickerControl.onSubmitAsigned=false;DatePickerControl.minDate=null;DatePickerControl.maxDate=null;DatePickerControl.DOMonth=[31,28,31,30,31,30,31,31,30,31,30,31];DatePickerControl.lDOMonth=[31,29,31,30,31,30,31,31,30,31,30,31];function DatePickerControl()
{}
DatePickerControl.init=function()
{if(!document.getElementById("CalendarPickerControl"))
{this.setGlobalParams();this.calBG=null;if(this.useTrickyBG){this.calBG=document.createElement("iframe");this.calBG.id="CalendarPickerControlBG";this.calBG.style.zIndex="49999";this.calBG.style.position="absolute";this.calBG.style.display="none";this.calBG.style.border="0px solid transparent";document.body.appendChild(this.calBG);}
this.calContainer=document.createElement("div");this.calContainer.id="CalendarPickerControl";this.calContainer.style.zIndex="50000";this.calContainer.style.position="absolute";this.calContainer.style.display="none";document.body.appendChild(this.calContainer);if(this.calContainer.addEventListener){this.calContainer.addEventListener("click",cal_onContainerClick,false);window.addEventListener("resize",cal_onWindowResize,false);}
else if(this.calContainer.attachEvent){this.calContainer.attachEvent("onclick",cal_onContainerClick);window.attachEvent("onresize",cal_onWindowResize);}}}
DatePickerControl.setGlobalParams=function()
{var obj=document.getElementById("cal_DEFAULT_FORMAT");if(obj)this.defaultFormat=obj.value;obj=document.getElementById("cal_SUBMIT_FORMAT");if(obj)this.submitFormat=obj.value;obj=document.getElementById("cal_FIRST_WEEK_DAY");if(obj)this.firstWeekDay=(obj.value<0||obj.value>6)?0:parseInt(obj.value);obj=document.getElementById("cal_WEEKEND_DAYS");if(obj)eval("this.weekend = "+obj.value);obj=document.getElementById("cal_AUTO_SHOW");if(obj)this.autoShow=obj.value=="true";obj=document.getElementById("cal_DEFAULT_TODAY");if(obj)this.defaultTodaySel=obj.value=="true";obj=document.getElementById("cal_CALENDAR_OFFSET_X");if(obj)this.offsetX=parseInt(obj.value);obj=document.getElementById("cal_CALENDAR_OFFSET_Y");if(obj)this.offsetY=parseInt(obj.value);obj=document.getElementById("cal_TODAY_TEXT");if(obj)this.todayText=obj.value;obj=document.getElementById("cal_BUTTON_TITLE");if(obj)this.buttonTitle=obj.value;obj=document.getElementById("cal_BUTTON_POSITION");if(obj)this.buttonPosition=obj.value;obj=document.getElementById("cal_BUTTON_OFFSET_X");if(obj)this.buttonOffsetX=parseInt(obj.value);obj=document.getElementById("cal_BUTTON_OFFSET_Y");if(obj)this.buttonOffsetY=parseInt(obj.value);obj=document.getElementById("cal_WEEK_NUMBER");if(obj)this.weekNumber=obj.value=="true";obj=document.getElementById("cal_MONTH_NAMES");if(obj)eval("this.Months = "+obj.value);obj=document.getElementById("cal_DAY_NAMES");if(obj)eval("this.Days = "+obj.value);}
function cal_autoInit()
{DatePickerControl.init();}
if(window.addEventListener)
{window.addEventListener("load",cal_autoInit,false);}
else if(window.attachEvent)
{window.attachEvent("onload",cal_autoInit);}
DatePickerControl.createButton=function(input,useId)
{return true;}
DatePickerControl.show=function()
{if(!this.displayed){var input=this.inputControl;if(input==null)return;if(input.disabled)return;var top=getObject.getSize("offsetTop",input);var left=getObject.getSize("offsetLeft",input);var calframe=document.getElementById(this.calFrameId);this.calContainer.style.top=top+input.offsetHeight+this.offsetY+"px";this.calContainer.style.left=left+this.offsetX+"px";this.calContainer.style.display="none";this.calContainer.style.visibility="visible";this.calContainer.style.display="block";this.calContainer.style.height=calframe.offsetHeight;if(this.calBG){this.calBG.style.top=this.calContainer.style.top;this.calBG.style.left=this.calContainer.style.left;this.calBG.style.display="none";this.calBG.style.visibility="visible";this.calBG.style.display="block";this.calBG.style.width=this.calContainer.offsetWidth;if(calframe){this.calBG.style.height=calframe.offsetHeight;}}
this.displayed=true;input.focus();}}
DatePickerControl.hide=function()
{if(this.displayed){this.calContainer.style.visibility="hidden";this.calContainer.style.left=-1000;this.calContainer.style.top=-1000;if(this.calBG){this.calBG.style.visibility="hidden";this.calBG.style.left=-1000;this.calBG.style.top=-1000;}
this.inputControl.value=this.originalValue;this.displayed=false;}}
DatePickerControl.getMonthName=function(monthNumber)
{return this.Months[monthNumber];}
DatePickerControl.getDaysOfMonth=function(monthNo,p_year)
{if(this.isLeapYear(p_year)){return this.lDOMonth[monthNo];}
else{return this.DOMonth[monthNo];}}
DatePickerControl.calcMonthYear=function(p_Month,p_Year,incr)
{var ret_arr=new Array();if(incr==-1){if(p_Month==0){ret_arr[0]=11;ret_arr[1]=parseInt(p_Year)-1;}
else{ret_arr[0]=parseInt(p_Month)-1;ret_arr[1]=parseInt(p_Year);}}
else if(incr==1){if(p_Month==11){ret_arr[0]=0;ret_arr[1]=parseInt(p_Year)+1;}
else{ret_arr[0]=parseInt(p_Month)+1;ret_arr[1]=parseInt(p_Year);}}
return ret_arr;}
DatePickerControl.getAllCode=function()
{var vCode="";vCode+="<table class='calframe' id='"+this.calFrameId+"'>";vCode+=this.getHeaderCode();vCode+=this.getDaysHeaderCode();vCode+=this.getDaysCode();vCode+="</table>";return vCode;}
DatePickerControl.getHeaderCode=function()
{var prevMMYYYY=this.calcMonthYear(this.month,this.year,-1);var prevMM=prevMMYYYY[0];var prevYYYY=prevMMYYYY[1];var nextMMYYYY=this.calcMonthYear(this.month,this.year,1);var nextMM=nextMMYYYY[0];var nextYYYY=nextMMYYYY[1];var gNow=new Date();var vCode="";var numberCols=this.weekNumber?8:7;vCode+="<tr><td colspan='"+numberCols+"' class='monthname'>";vCode+="<span title='"+this.Months[this.month]+" "+(parseInt(this.year)-1)+"' class='yearbutton' ";vCode+="onclick='DatePickerControl.build("+this.month+", "+(parseInt(this.year)-1)+");return false;'>&laquo;</span>";vCode+="&nbsp;"+this.year+"&nbsp;";vCode+="<span title='"+this.Months[this.month]+" "+(parseInt(this.year)+1)+"' class='yearbutton' ";vCode+="onclick='DatePickerControl.build("+this.month+", "+(parseInt(this.year)+1)+");return false;'>&raquo;</span>";vCode+="</td></tr>";vCode+="<tr><td style='border-width:0px' colspan='"+numberCols+"'>";vCode+="<table class='navigation' width='100%'><tr>";vCode+="<td class='navbutton' title='"+this.Months[prevMM]+" "+prevYYYY+"' ";vCode+="onclick='DatePickerControl.build("+prevMM+", "+prevYYYY+");return false;'>&lt;&lt;</td>";vCode+="<td class='navbutton' title='"+gNow.getDate()+" "+this.Months[gNow.getMonth()]+" "+gNow.getFullYear()+"' ";vCode+="onclick='DatePickerControl.build("+gNow.getMonth()+", "+gNow.getFullYear()+");DatePickerControl.selectToday();return false;'>";vCode+=this.monthName+"</td>";vCode+="<td class='navbutton' title='"+this.Months[nextMM]+" "+nextYYYY+"' ";vCode+="onclick='DatePickerControl.build("+nextMM+", "+nextYYYY+");return false;'>&gt;&gt;</td>";vCode+="</tr></table>";vCode+="</td></tr>";return vCode;}
DatePickerControl.getDaysHeaderCode=function()
{var vCode="";vCode=vCode+"<tr>";if(this.weekNumber){vCode+="<td class='weeknumber'>&nbsp;</td>"}
for(i=this.firstWeekDay;i<this.firstWeekDay+7;i++){vCode+="<td class='dayname' width='14%'>"+this.Days[i%7]+"</td>";}
vCode=vCode+"</tr>";return vCode;}
DatePickerControl.getDaysCode=function()
{var vDate=new Date();vDate.setDate(1);vDate.setMonth(this.month);vDate.setFullYear(this.year);var vFirstDay=vDate.getDay();var vDay=1;var vLastDay=this.getDaysOfMonth(this.month,this.year);var vOnLastDay=0;var vCode="";this.dayOfWeek=vFirstDay;var prevm=this.month==0?11:this.month-1;var prevy=this.prevm==11?this.year-1:this.year;prevmontdays=this.getDaysOfMonth(prevm,prevy);vFirstDay=(vFirstDay==0&&this.firstWeekDay)?7:vFirstDay;if(this.weekNumber){var week=this.getWeekNumber(this.year,this.month,1);}
vCode+="<tr>";if(this.weekNumber){vCode+="<td class='weeknumber'>"+week+"</td>";}
for(i=this.firstWeekDay;i<vFirstDay;i++){vCode=vCode+"<td class='dayothermonth'>"+(prevmontdays-vFirstDay+i+1)+"</td>";}
for(j=vFirstDay-this.firstWeekDay;j<7;j++){if(this.isInRange(vDay)){classname=this.getDayClass(vDay,j);vCode+="<td class='"+classname+"' class_orig='"+classname+"' "+"onClick='DatePickerControl.writeDate("+vDay+")' id='"+this.dayIdPrefix+vDay+"'>"+vDay+"</td>";}
else{vCode+="<td class='dayothermonth'>"+vDay+"</td>";}
vDay++;}
vCode=vCode+"</tr>";for(k=2;k<7;k++){vCode=vCode+"<tr>";if(this.weekNumber){week++;if(week>=53)week=1;vCode+="<td class='weeknumber'>"+week+"</td>";}
for(j=0;j<7;j++){if(this.isInRange(vDay)){classname=this.getDayClass(vDay,j);vCode+="<td class='"+classname+"' class_orig='"+classname+"' "+"onClick='DatePickerControl.writeDate("+vDay+")' id='"+this.dayIdPrefix+vDay+"'>"+vDay+"</td>";}
else{vCode+="<td class='dayothermonth'>"+vDay+"</td>";}
vDay++;if(vDay>vLastDay){vOnLastDay=1;break;}}
if(j==6)
vCode+="</tr>";if(vOnLastDay==1)
break;}
for(m=1;m<(7-j);m++){vCode+="<td class='dayothermonth'>"+m+"</td>";}
return vCode;}
DatePickerControl.getDayClass=function(vday,dayofweek)
{var gNow=new Date();var vNowDay=gNow.getDate();var vNowMonth=gNow.getMonth();var vNowYear=gNow.getFullYear();if(vday==vNowDay&&this.month==vNowMonth&&this.year==vNowYear){return"today";}
else{var realdayofweek=(7+dayofweek+this.firstWeekDay)%7;for(i=0;i<this.weekend.length;i++){if(realdayofweek==this.weekend[i]){return"weekend";}}
return"day";}}
DatePickerControl.formatData=function(p_day)
{var vData;var vMonth=1+this.month;vMonth=(vMonth.toString().length<2)?"0"+vMonth:vMonth;var vMon=this.getMonthName(this.month).substr(0,3).toUpperCase();var vFMon=this.getMonthName(this.month).toUpperCase();var vY4=new String(this.year);var vY2=new String(this.year).substr(2,2);var vDD=(p_day.toString().length<2)?"0"+p_day:p_day;var time=new Date();var curr_hour=time.getHours();if(curr_hour<12)
{a_p="AM";}
else
{a_p="PM";}
if(curr_hour==0)
{curr_hour=12;}
if(curr_hour>12)
{curr_hour=curr_hour-12;}
var curr_min=time.getMinutes();curr_min=curr_min+"";if(curr_min.length==1)
{curr_min="0"+curr_min;}
var curr_sec=time.getSeconds();curr_sec=curr_sec+"";if(curr_sec.length==1)
{curr_sec="0"+curr_sec;}
switch(this.format){case"m/d/Y":vData=vMonth+"/"+vDD+"/"+vY4;break;case"m/d/y":vData=vMonth+"/"+vDD+"/"+vY2;break;case"m-d-Y":vData=vMonth+"-"+vDD+"-"+vY4;break;case"m-d-y":vData=vMonth+"-"+vDD+"-"+vY2;break;case"Y-m-d":vData=vY4+"-"+vMonth+"-"+vDD;break;case"Y/m/d":vData=vY4+"/"+vMonth+"/"+vDD;break;case"d/M/Y":vData=vDD+"/"+vMon+"/"+vY4;break;case"d/M/y":vData=vDD+"/"+vMon+"/"+vY2;break;case"d-M-Y":vData=vDD+"-"+vMon+"-"+vY4;break;case"d-M-y":vData=vDD+"-"+vMon+"-"+vY2;break;case"d/F/Y":vData=vDD+"/"+vFMon+"/"+vY4;break;case"d/F/y":vData=vDD+"/"+vFMon+"/"+vY2;break;case"d-F-Y":vData=vDD+"-"+vFMon+"-"+vY4;break;case"d-F-y":vData=vDD+"-"+vFMon+"-"+vY2;break;case"d/m/Y":vData=vDD+"/"+vMonth+"/"+vY4;break;case"d/m/y":vData=vDD+"/"+vMonth+"/"+vY2;break;case"d-m-Y":vData=vDD+"-"+vMonth+"-"+vY4;break;case"d-m-y":vData=vDD+"-"+vMonth+"-"+vY2;break;case"d.m.Y":vData=vDD+"."+vMonth+"."+vY4;break;case"d.m.y":vData=vDD+"."+vMonth+"."+vY2;break;default:vData=vMonth+"/"+vDD+"/"+vY4;}
return vData;}
DatePickerControl.getDateFromControl=function(ctrl)
{if(ctrl==null)ctrl=this.inputControl;var value=ctrl.value;var format=document.getElementById("zig_calendar_"+ctrl.id).getAttribute("datepicker_format");return this.getDateFromString(value,format.toString());}
DatePickerControl.getDateFromString=function(strdate,format)
{var aDate=new Date();var day,month,year;if(strdate==""||format=="")return aDate;strdate=strdate.replace("/","@").replace("/","@");strdate=strdate.replace("-","@").replace("-","@");strdate=strdate.replace(".","@").replace(".","@");if(strdate.indexOf("/")>=0||strdate.indexOf("-")>=0||strdate.indexOf(".")>=0)return aDate;var data=strdate.split("@");if(data.length!=3)return aDate;for(i=0;i<3;i++){data[i]=parseFloat(data[i]);if(isNaN(data[i]))return aDate;}
aDate.setDate(1);if(format.substring(0,1).toUpperCase()=="D"){aDate.setFullYear(this.yearTwo2Four(data[2]));aDate.setMonth(data[1]-1);aDate.setDate(data[0]);}
else if(format.substring(0,1).toUpperCase()=="Y"){aDate.setFullYear(this.yearTwo2Four(data[0]));aDate.setMonth(data[1]-1);aDate.setDate(data[2]);}
else if(format.substring(0,1).toUpperCase()=="M"){aDate.setFullYear(this.yearTwo2Four(data[2]));aDate.setMonth(data[0]-1);aDate.setDate(data[1]);}
return aDate;}
DatePickerControl.yearTwo2Four=function(year)
{if(year<99){if(year>=30){year+=1900;}
else{year+=2000;}}
return year;}
DatePickerControl.writeDate=function(day)
{var d=this.formatData(day);this.inputControl.value=d;this.originalValue=d;this.hide();if(DatePickerControl.onSelect)DatePickerControl.onSelect(this.inputControl.id);this.firstFocused=true;this.inputControl.focus();}
DatePickerControl.writeCurrentDate=function()
{var d=this.formatData(this.currentDay);this.inputControl.value=d;}
DatePickerControl.build=function(m,y)
{var bkm=this.month;var bky=this.year;var calframe=document.getElementById(this.calFrameId);if(m==null){var now=new Date();this.month=now.getMonth();this.year=now.getFullYear();}
else{this.month=m;this.year=y;}
if(!this.isInRange(null)){this.month=bkm;this.year=bky;}
if(!this.isInRange(this.currentDay)){if(this.minDate&&this.currentDay<this.minDate.getDate())this.currentDay=this.minDate.getDate();if(this.maxDate&&this.currentDay>this.maxDate.getDate())this.currentDay=this.maxDate.getDate();}
this.monthName=this.Months[this.month];var code=this.getAllCode();writeLayer(this.calContainer.id,null,code);if(this.calContainer&&calframe)this.calContainer.style.height=calframe.offsetHeight;this.firstFocused=true;this.inputControl.focus();this.selectDay(this.currentDay);}
DatePickerControl.buildPrev=function()
{if(!this.displayed)return;var prevMMYYYY=this.calcMonthYear(this.month,this.year,-1);var prevMM=prevMMYYYY[0];var prevYYYY=prevMMYYYY[1];this.build(prevMM,prevYYYY);}
DatePickerControl.buildNext=function()
{if(!this.displayed)return;var nextMMYYYY=this.calcMonthYear(this.month,this.year,1);var nextMM=nextMMYYYY[0];var nextYYYY=nextMMYYYY[1];this.build(nextMM,nextYYYY);}
DatePickerControl.selectToday=function()
{var now=new Date();var today=now.getDate();if(!this.isInRange(today))return;if(this.closeOnTodayBtn){this.currentDay=today;this.writeDate(this.currentDay);}
else{this.selectDay(today);}}
DatePickerControl.selectDay=function(day)
{if(!this.displayed)return;if(!this.isInRange(day)){return;}
var n=this.currentDay;var max=this.getDaysOfMonth(this.month,this.year);if(day>max)return;var newDayObject=document.getElementById(this.dayIdPrefix+day);var currentDayObject=document.getElementById(this.dayIdPrefix+this.currentDay);if(currentDayObject){currentDayObject.className=currentDayObject.getAttribute("class_orig");}
if(newDayObject){newDayObject.className="current";this.currentDay=day;this.writeCurrentDate();}}
DatePickerControl.selectPrevDay=function(decr)
{if(!this.displayed)return;var n=this.currentDay;var max=this.getDaysOfMonth(this.month,this.year);var prev=n-decr;if(prev<=0){if(decr==7){n=(n+this.dayOfWeek)+28-this.dayOfWeek;n--;prev=n>max?n-7:n;}
else{prev=max;}}
this.selectDay(prev);}
DatePickerControl.selectNextDay=function(incr)
{if(!this.displayed)return;var n=this.currentDay;var max=this.getDaysOfMonth(this.month,this.year);var next=n+incr;if(next>max){if(incr==7){n=((n+this.dayOfWeek)%7)-this.dayOfWeek;next=n<0?n+7:n;next++;}
else{next=1;}}
this.selectDay(next);}
DatePickerControl.showForEdit=function(edit)
{if(this.displayed)return;if(edit==null)return;if(edit.disabled)return;this.inputControl=edit;this.originalValue=edit.value;this.setupRange();var format=this.inputControl.getAttribute("datepicker_format");if(format==null)format=this.defaultFormat;this.format=format;if(this.validate(edit.value,format)){var date=this.getDateFromControl();this.currentDate=date;this.build(date.getMonth(),date.getFullYear());this.currentDay=date.getDate();}
else{edit.value="";this.originalValue="";this.currentDate=null;if(this.defaultTodaySel){this.currentDay=new Date().getDate();}
else{this.currentDay=1;}
this.build(null,null);}
var currentDayObject=document.getElementById(this.dayIdPrefix+this.currentDay);if(currentDayObject)currentDayObject.className="current";this.writeCurrentDate();this.show();}
DatePickerControl.isInRange=function(day)
{if(!this.minDate&&!this.maxDate)return true;if(day){var aDate=new Date();aDate.setFullYear(this.year);aDate.setMonth(this.month);aDate.setDate(day);if(this.minDate){if(this.compareDates(aDate,this.minDate)<0)return false;}
if(this.maxDate){if(this.compareDates(aDate,this.maxDate)>0)return false;}}
else{var currentym=parseInt(this.year.toString()+(this.month<10?"0"+this.month.toString():this.month.toString()));var m;if(this.minDate){m=this.minDate.getMonth();var minym=parseInt(this.minDate.getFullYear().toString()+(m<10?"0"+m.toString():m.toString()));if(currentym<minym)return false;}
if(this.maxDate){m=this.maxDate.getMonth();var maxym=parseInt(this.maxDate.getFullYear().toString()+(m<10?"0"+m.toString():m.toString()));if(currentym>maxym)return false;}}
return true;}
DatePickerControl.setupRange=function()
{var edit=this.inputControl;var format=edit.getAttribute("datepicker_format");var min=edit.getAttribute("datepicker_min");this.minDate=min?this.getDateFromString(min,format):null;var max=edit.getAttribute("datepicker_max");this.maxDate=max?this.getDateFromString(max,format):null;if(this.maxDate&&this.minDate){if(this.maxDate.getTime()<this.minDate.getTime()){var tmp=this.maxDate;this.maxDate=this.minDate;this.minDate=tmp;}}}
DatePickerControl.compareDates=function(d1,d2)
{var m=d1.getMonth();var d=d1.getDate();var s1=d1.getFullYear().toString()+(m<10?"0"+m.toString():m.toString())+(d<10?"0"+d.toString():d.toString());m=d2.getMonth();d=d2.getDate();var s2=d2.getFullYear().toString()+(m<10?"0"+m.toString():m.toString())+(d<10?"0"+d.toString():d.toString());var n1=parseInt(s1);var n2=parseInt(s2);return n1-n2;}
DatePickerControl.validate=function(strdate,format)
{var dateRegExp;var separator;var d,m,y;var od=this.currentDay,om=this.month,oy=this.year;if(strdate=="")return false;if(format.substring(0,1).toUpperCase()=="D"){dateRegExp=/^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2,4}$/}
else if(format.substring(0,1).toUpperCase()=="Y"){dateRegExp=/^\d{2,4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/}
else if(format.substring(0,1).toUpperCase()=="M"){dateRegExp=/^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2,4}$/}
if(!dateRegExp.test(strdate)){return false;}
separator=(strdate.indexOf("/")>1)?"/":((strdate.indexOf("-")>1)?"-":".");var datearray=strdate.split(separator);if(format.substring(0,1).toUpperCase()=="D"){d=parseFloat(datearray[0]);m=parseFloat(datearray[1]);y=parseFloat(datearray[2]);}
else if(format.substring(0,1).toUpperCase()=="Y"){d=parseFloat(datearray[2]);m=parseFloat(datearray[1]);y=parseFloat(datearray[0]);}
else if(format.substring(0,1).toUpperCase()=="M"){d=parseFloat(datearray[1]);m=parseFloat(datearray[0]);y=parseFloat(datearray[2]);}
if(m<1||m>12)return false;if(d>this.getDaysOfMonth(m-1,y))return false;this.month=m;this.year=y;var res=this.isInRange(d);this.month=om;this.year=oy;return res;}
DatePickerControl.isLeapYear=function(year)
{if((year%4)==0){if((year%100)==0&&(year%400)!=0){return false;}
return true;}
return false;}
function cal_onButtonClick(event)
{DatePickerControl.onButtonClick(event);}
DatePickerControl.onButtonClick=function(event)
{if(!this.displayed)
{if(event==null)event=window.event;var button=(event.srcElement)?event.srcElement:event.originalTarget;var input=document.getElementById(button.getAttribute("datepicker_inputid"));this.showForEdit(input);}
else
{this.hide();}}
function cal_onContainerClick(event){DatePickerControl.onContainerClick(event);}
DatePickerControl.onContainerClick=function(event)
{if(event==null)event=window.event;if(this.hideTimeout){clearTimeout(this.hideTimeout);this.hideTimeout=null;}
this.inputControl.focus();return false;}
function cal_onEditControlKeyUp(event){DatePickerControl.onEditControlKeyUp(event);}
DatePickerControl.onEditControlKeyUp=function(event)
{if(event==null)event=window.event;var edit=event.srcElement?event.srcElement:event.originalTarget;var kc=event.charCode?event.charCode:event.which?event.which:event.keyCode;switch(kc){case 37:this.selectPrevDay(1);break;case 38:this.selectPrevDay(7);break;case 39:this.selectNextDay(1);break;case 40:if(!this.displayed){this.showForEdit(edit);}
else{this.selectNextDay(7);break;}
break;case 27:this.hide();break;case 33:if((event.modifiers&Event.SHIFT_MASK)||(event.shiftKey)){this.build(this.month,parseInt(this.year)-1);}
else{this.buildPrev();}
break;case 34:if((event.modifiers&Event.SHIFT_MASK)||(event.shiftKey)){this.build(this.month,parseInt(this.year)+1);}
else{this.buildNext();}
break;case 13:if(this.displayed&&this.currentDay>0&&this.submitByKey){this.writeDate(this.currentDay);}
break;}
return false;}
function cal_onEditControlKeyDown(event){DatePickerControl.onEditControlKeyDown(event);}
DatePickerControl.onEditControlKeyDown=function(event)
{if(event==null)event=window.event;var edit=event.srcElement?event.srcElement:event.originalTarget;var kc=event.charCode?event.charCode:event.which?event.which:event.keyCode;if(kc>=65&&kc<=90){if(event.stopPropagation)event.stopPropagation();if(event.preventDefault)event.preventDefault();event.returnValue=false;event.cancelBubble=true;return false;}
switch(kc){case 13:this.submitByKey=true;break;case 9:case 32:if(this.displayed&&this.currentDay>0){this.writeDate(this.currentDay);}
break;}}
function cal_onEditControlKeyPress(event){DatePickerControl.onEditControlKeyPress(event);}
DatePickerControl.onEditControlKeyPress=function(event)
{if(event==null)event=window.event;var edit=event.srcElement?event.srcElement:event.originalTarget;var kc=event.charCode?event.charCode:event.which?event.which:event.keyCode;if(!((kc<32)||(kc>44&&kc<58))){if(event.stopPropagation)event.stopPropagation();if(event.preventDefault)event.preventDefault();event.returnValue=false;event.cancelBubble=true;return false;}}
function cal_onEditControlBlur(event){DatePickerControl.onEditControlBlur(event);}
DatePickerControl.onEditControlBlur=function(event)
{if(event==null)event=window.event;if(!this.hideTimeout){this.hideTimeout=setTimeout("DatePickerControl.hide()",this.HIDE_TIMEOUT);}
this.firstFocused=false;this.hideCauseBlur=true;}
function cal_onEditControlChange(event){DatePickerControl.onEditControlChange(event);}
DatePickerControl.onEditControlChange=function(event)
{}
function cal_onEditControlFocus(event){DatePickerControl.onEditControlFocus(event);}
DatePickerControl.onEditControlFocus=function(event)
{if(event==null)event=window.event;var edit=(event.srcElement)?event.srcElement:event.originalTarget;this.setupRange();if((!this.displayed||this.hideCauseBlur)&&this.autoShow&&!this.firstFocused){clearTimeout(this.hideTimeout);this.hideTimeout=null;this.firstFocused=true;if(this.hideCauseBlur){this.hideCauseBlur=false;this.hide();}
this.showForEdit(edit);}
else if(this.inputControl&&this.inputControl.id!=edit.id){this.hide();}
else if(this.hideTimeout){clearTimeout(this.hideTimeout);this.hideTimeout=null;}}
function cal_onFormSubmit(event){DatePickerControl.onFormSubmit(event);}
DatePickerControl.onFormSubmit=function(event)
{if(this.submitByKey){this.submitByKey=false;if(this.displayed&&this.currentDay>0){this.writeDate(this.currentDay);if(event==null)event=window.event;var theForm=(event.srcElement)?event.srcElement:event.originalTarget;if(event.stopPropagation)event.stopPropagation();if(event.preventDefault)event.preventDefault();event.returnValue=false;event.cancelBubble=true;return false;}}
this.reformatOnSubmit();}
DatePickerControl.reformatOnSubmit=function()
{if(this.submitFormat=="")return true;var inputControls=document.getElementsByTagName("input");var inputsLength=inputControls.length;var i;for(i=0;i<inputsLength;i++){if(inputControls[i].type.toLowerCase()=="text"){var editctrl=inputControls[i];if(editctrl.value=="")continue;var isdpc=editctrl.getAttribute("isdatepicker");if(isdpc&&isdpc=="true"){var thedate=this.getDateFromControl(editctrl);var res=this.submitFormat.replace("DD",thedate.getDate());var mo=thedate.getMonth()+1;res=res.replace("MM",mo.toString());if(this.submitFormat.indexOf("YYYY")>=0){res=res.replace("YYYY",thedate.getFullYear());}
else{res=res.replace("YY",thedate.getFullYear());}
editctrl.value=res;}}}
return true;}
function cal_formSubmit()
{var res=DatePickerControl.reformatOnSubmit();if(this.submitOrig){res=this.submitOrig();}
return res;}
function cal_onWindowResize(event){DatePickerControl.onWindowResize(event);}
DatePickerControl.onWindowResize=function(event)
{this.relocate();}
DatePickerControl.relocateButtons=function()
{return;var divElements=document.getElementsByTagName("div");for(key in divElements){if(divElements[key].id&&divElements[key].id.indexOf(this.buttonIdPrefix)==0){var calButton=divElements[key];if(calButton.style.display=='none')continue;var input=document.getElementById(calButton.getAttribute("datepicker_inputid"));if(input.style.display=='none'||input.offsetTop==0)continue;var nTop=getObject.getSize("offsetTop",input);var nLeft=getObject.getSize("offsetLeft",input);calButton.style.top=(nTop+Math.floor((input.offsetHeight-calButton.offsetHeight)/2)+this.buttonOffsetY)+"px";var btnOffX=Math.floor((input.offsetHeight-calButton.offsetHeight)/2);if(this.buttonPosition=="in"){calButton.style.left=(nLeft+input.offsetWidth-calButton.offsetWidth-btnOffX+this.buttonOffsetX)+"px";}
else{calButton.style.left=(nLeft+input.offsetWidth+btnOffX+this.buttonOffsetX)+"px";}}}}
DatePickerControl.relocate=function()
{if(this.displayed){var input=this.inputControl;if(input==null)return;var top=getObject.getSize("offsetTop",input);var left=getObject.getSize("offsetLeft",input);this.calContainer.style.top=top+input.offsetHeight+this.offsetY+"px";this.calContainer.style.left=left+this.offsetX+"px";if(this.calBG){this.calBG.style.top=this.calContainer.style.top;this.calBG.style.left=this.calContainer.style.left;}}}
DatePickerControl.getWeekNumber=function(year,month,day)
{var when=new Date(year,month,day);var newYear=new Date(year,0,1);var offset=7+1-newYear.getDay();if(offset==8)offset=1;var daynum=((Date.UTC(y2k(year),when.getMonth(),when.getDate(),0,0,0)-Date.UTC(y2k(year),0,1,0,0,0))/1000/60/60/24)+1;var weeknum=Math.floor((daynum-offset+7)/7);if(weeknum==0){year--;var prevNewYear=new Date(year,0,1);var prevOffset=7+1-prevNewYear.getDay();if(prevOffset==2||prevOffset==8)weeknum=53;else weeknum=52;}
return weeknum;}
function y2k(number){return(number<1000)?number+1900:number;}
function getObject(sId)
{if(bw.dom){this.hElement=document.getElementById(sId);this.hStyle=this.hElement.style;}
else if(bw.ns4){this.hElement=document.layers[sId];this.hStyle=this.hElement;}
else if(bw.ie){this.hElement=document.all[sId];this.hStyle=this.hElement.style;}}
getObject.getSize=function(sParam,hLayer)
{nPos=0;while((hLayer.tagName)&&!(/(body|html)/i.test(hLayer.tagName))){nPos+=eval('hLayer.'+sParam);if(sParam=='offsetTop'){if(hLayer.clientTop){nPos+=hLayer.clientTop;}}
if(sParam=='offsetLeft'){if(hLayer.clientLeft){nPos+=hLayer.clientLeft;}}
hLayer=hLayer.offsetParent;}
return nPos;}
function writeLayer(ID,parentID,sText)
{if(document.layers){var oLayer;if(parentID){oLayer=eval('document.'+parentID+'.document.'+ID+'.document');}
else{oLayer=document.layers[ID].document;}
oLayer.open();oLayer.write(sText);oLayer.close();}
else if(document.all){document.all[ID].innerHTML=sText;}
else{document.getElementById(ID).innerHTML=sText;}}
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
var INPUT_NAME_PREFIX='text';var TABLE_NAME='table_zig_listing_filters';var ROW_BASE=0;function filters_myRowObject(one,two,three,four)
{this.one=one;this.two=two;this.three=three;this.four=four;}
function filters_insertRowToTable()
{var tbl=document.getElementById(TABLE_NAME);var rowToInsertAt=tbl.tBodies[0].rows.length;for(var i=0;i<tbl.tBodies[0].rows.length;i++){if(tbl.tBodies[0].rows[i].myRow&&tbl.tBodies[0].rows[i].myRow.four.getAttribute('type')=='radio'&&tbl.tBodies[0].rows[i].myRow.four.checked){rowToInsertAt=i;break;}}
filters_addRowToTable(rowToInsertAt);filters_reorderRows(tbl,rowToInsertAt);}
function filters_addRowToTable(num)
{var w=document.form_zig_filters.zig_filter_select.selectedIndex;if(w)
{var tbl=document.getElementById(TABLE_NAME);var nextRow=tbl.tBodies[0].rows.length;var iteration=nextRow+ROW_BASE;if(num==null)
{num=nextRow;}
else
{iteration=num+ROW_BASE;}
var row=tbl.tBodies[0].insertRow(num);row.className='classy'+(iteration%2);var selected_label=document.form_zig_filters.zig_filter_select.options[w].text;var selected_text=document.form_zig_filters.zig_filter_select.value;var cell0=row.insertCell(0);cell0.innerHTML="&nbsp;<a href='javascript: return void(0) ;' onclick='filters_deleteCurrentRow(this) ;'>remove</a>&nbsp;";var cell1=row.insertCell(1);var textNode=document.createTextNode(selected_label);cell1.appendChild(textNode);var cell2=row.insertCell(2);cell2.innerHTML="<select name='op_"+selected_text+"'><option value='='>=</option><option value='<'>&lt;</option><option value='>'>&gt;</option><option value='<='>&le;</option><option value='>='>&ge;</option><option value='!='>&ne;</option><option value='LIKE'>contains</option></select>";var cell3=row.insertCell(3);var txtInp=document.createElement('input');txtInp.setAttribute('type','text');txtInp.setAttribute('name',selected_text);txtInp.setAttribute('size','20');txtInp.setAttribute('value','');cell3.appendChild(txtInp);document.getElementById('zig_include').value=document.getElementById('zig_include').value?document.getElementById('zig_include').value+","+selected_text:selected_text;document.form_zig_filters.zig_filter_select.selectedIndex=0;row.myRow=new filters_myRowObject(textNode,txtInp,cbEl,raEl);}}
function filters_deleteRows(rowObjArray)
{for(var i=0;i<rowObjArray.length;i++)
{var rIndex=rowObjArray[i].sectionRowIndex;rowObjArray[i].parentNode.deleteRow(rIndex);document.getElementById('zig_include').value=document.getElementById('zig_include').value.replace(","+rowObjArray[i].parentNode.value,"");document.getElementById('zig_include').value=document.getElementById('zig_include').value.replace(rowObjArray[i].parentNode.value,"");}}
function filters_deleteCurrentRow(obj)
{var delRow=obj.parentNode.parentNode;var tbl=delRow.parentNode.parentNode;var rIndex=delRow.sectionRowIndex;var rowArray=new Array(delRow);filters_deleteRows(rowArray);filters_reorderRows(tbl,rIndex);}
function filters_reorderRows(tbl,startingIndex)
{if(tbl.tBodies[0].rows[startingIndex])
{var count=startingIndex+ROW_BASE;for(var i=startingIndex;i<tbl.tBodies[0].rows.length;i++)
{tbl.tBodies[0].rows[i].myRow.one.data=count;tbl.tBodies[0].rows[i].myRow.two.name=INPUT_NAME_PREFIX+count;var tempVal=tbl.tBodies[0].rows[i].myRow.two.value.split(' ');tbl.tBodies[0].rows[i].myRow.two.value=count+' was'+tempVal[0];tbl.tBodies[0].rows[i].myRow.four.value=count;tbl.tBodies[0].rows[i].className='classy'+(count%2);count++;}}}
var config=new Object();var tt_Debug=true
var tt_Enabled=true
var TagsToTip=true
config.Above=false
config.BgColor='#E2E7FF'
config.BgImg=''
config.BorderColor='#003099'
config.BorderStyle='solid'
config.BorderWidth=1
config.CenterMouse=false
config.ClickClose=false
config.ClickSticky=false
config.CloseBtn=false
config.CloseBtnColors=['#990000','#FFFFFF','#DD3333','#FFFFFF']
config.CloseBtnText='&nbsp;X&nbsp;'
config.CopyContent=true
config.Delay=400
config.Duration=0
config.FadeIn=0
config.FadeOut=0
config.FadeInterval=30
config.Fix=null
config.FollowMouse=true
config.FontColor='#000044'
config.FontFace='Verdana,Geneva,sans-serif'
config.FontSize='8pt'
config.FontWeight='normal'
config.Height=0
config.JumpHorz=false
config.JumpVert=true
config.Left=false
config.OffsetX=14
config.OffsetY=8
config.Opacity=100
config.Padding=3
config.Shadow=false
config.ShadowColor='#C0C0C0'
config.ShadowWidth=5
config.Sticky=false
config.TextAlign='left'
config.Title=''
config.TitleAlign='left'
config.TitleBgColor=''
config.TitleFontColor='#FFFFFF'
config.TitleFontFace=''
config.TitleFontSize=''
config.TitlePadding=2
config.Width=0
function Tip()
{tt_Tip(arguments,null);}
function TagToTip()
{var t2t=tt_GetElt(arguments[0]);if(t2t)
tt_Tip(arguments,t2t);}
function UnTip()
{tt_OpReHref();if(tt_aV[DURATION]<0)
tt_tDurt.Timer("tt_HideInit()",-tt_aV[DURATION],true);else if(!(tt_aV[STICKY]&&(tt_iState&0x2)))
tt_HideInit();}
var tt_aElt=new Array(10),tt_aV=new Array(),tt_sContent,tt_scrlX=0,tt_scrlY=0,tt_musX,tt_musY,tt_over,tt_x,tt_y,tt_w,tt_h;function tt_Extension()
{tt_ExtCmdEnum();tt_aExt[tt_aExt.length]=this;return this;}
function tt_SetTipPos(x,y)
{var css=tt_aElt[0].style;tt_x=x;tt_y=y;css.left=x+"px";css.top=y+"px";if(tt_ie56)
{var ifrm=tt_aElt[tt_aElt.length-1];if(ifrm)
{ifrm.style.left=css.left;ifrm.style.top=css.top;}}}
function tt_HideInit()
{if(tt_iState)
{tt_ExtCallFncs(0,"HideInit");tt_iState&=~0x4;if(tt_flagOpa&&tt_aV[FADEOUT])
{tt_tFade.EndTimer();if(tt_opa)
{var n=Math.round(tt_aV[FADEOUT]/(tt_aV[FADEINTERVAL]*(tt_aV[OPACITY]/tt_opa)));tt_Fade(tt_opa,tt_opa,0,n);return;}}
tt_tHide.Timer("tt_Hide();",1,false);}}
function tt_Hide()
{if(tt_db&&tt_iState)
{tt_OpReHref();if(tt_iState&0x2)
{tt_aElt[0].style.visibility="hidden";tt_ExtCallFncs(0,"Hide");}
tt_tShow.EndTimer();tt_tHide.EndTimer();tt_tDurt.EndTimer();tt_tFade.EndTimer();if(!tt_op&&!tt_ie)
{tt_tWaitMov.EndTimer();tt_bWait=false;}
if(tt_aV[CLICKCLOSE]||tt_aV[CLICKSTICKY])
tt_RemEvtFnc(document,"mouseup",tt_OnLClick);tt_ExtCallFncs(0,"Kill");if(tt_t2t&&!tt_aV[COPYCONTENT])
{tt_t2t.style.display="none";tt_MovDomNode(tt_t2t,tt_aElt[6],tt_t2tDad);}
tt_iState=0;tt_over=null;tt_ResetMainDiv();if(tt_aElt[tt_aElt.length-1])
tt_aElt[tt_aElt.length-1].style.display="none";}}
function tt_GetElt(id)
{return(document.getElementById?document.getElementById(id):document.all?document.all[id]:null);}
function tt_GetDivW(el)
{return(el?(el.offsetWidth||el.style.pixelWidth||0):0);}
function tt_GetDivH(el)
{return(el?(el.offsetHeight||el.style.pixelHeight||0):0);}
function tt_GetScrollX()
{return(window.pageXOffset||(tt_db?(tt_db.scrollLeft||0):0));}
function tt_GetScrollY()
{return(window.pageYOffset||(tt_db?(tt_db.scrollTop||0):0));}
function tt_GetClientW()
{return(document.body&&(typeof(document.body.clientWidth)!=tt_u)?document.body.clientWidth:(typeof(window.innerWidth)!=tt_u)?window.innerWidth:tt_db?(tt_db.clientWidth||0):0);}
function tt_GetClientH()
{return(document.body&&(typeof(document.body.clientHeight)!=tt_u)?document.body.clientHeight:(typeof(window.innerHeight)!=tt_u)?window.innerHeight:tt_db?(tt_db.clientHeight||0):0);}
function tt_GetEvtX(e)
{return(e?((typeof(e.pageX)!=tt_u)?e.pageX:(e.clientX+tt_scrlX)):0);}
function tt_GetEvtY(e)
{return(e?((typeof(e.pageY)!=tt_u)?e.pageY:(e.clientY+tt_scrlY)):0);}
function tt_AddEvtFnc(el,sEvt,PFnc)
{if(el)
{if(el.addEventListener)
el.addEventListener(sEvt,PFnc,false);else
el.attachEvent("on"+sEvt,PFnc);}}
function tt_RemEvtFnc(el,sEvt,PFnc)
{if(el)
{if(el.removeEventListener)
el.removeEventListener(sEvt,PFnc,false);else
el.detachEvent("on"+sEvt,PFnc);}}
var tt_aExt=new Array(),tt_db,tt_op,tt_ie,tt_ie56,tt_bBoxOld,tt_body,tt_ovr_,tt_flagOpa,tt_maxPosX,tt_maxPosY,tt_iState=0,tt_opa,tt_bJmpVert,tt_bJmpHorz,tt_t2t,tt_t2tDad,tt_elDeHref,tt_tShow=new Number(0),tt_tHide=new Number(0),tt_tDurt=new Number(0),tt_tFade=new Number(0),tt_tWaitMov=new Number(0),tt_bWait=false,tt_u="undefined";function tt_Init()
{tt_MkCmdEnum();if(!tt_Browser()||!tt_MkMainDiv())
return;tt_IsW3cBox();tt_OpaSupport();tt_AddEvtFnc(window,"scroll",tt_OnScrl);tt_AddEvtFnc(window,"resize",tt_OnScrl);tt_AddEvtFnc(document,"mousemove",tt_Move);if(TagsToTip||tt_Debug)
tt_SetOnloadFnc();tt_AddEvtFnc(window,"unload",tt_Hide);}
function tt_MkCmdEnum()
{var n=0;for(var i in config)
eval("window."+i.toString().toUpperCase()+" = "+n++);tt_aV.length=n;}
function tt_Browser()
{var n,nv,n6,w3c;n=navigator.userAgent.toLowerCase(),nv=navigator.appVersion;tt_op=(document.defaultView&&typeof(eval("w"+"indow"+"."+"o"+"p"+"er"+"a"))!=tt_u);tt_ie=n.indexOf("msie")!=-1&&document.all&&!tt_op;if(tt_ie)
{var ieOld=(!document.compatMode||document.compatMode=="BackCompat");tt_db=!ieOld?document.documentElement:(document.body||null);if(tt_db)
tt_ie56=parseFloat(nv.substring(nv.indexOf("MSIE")+5))>=5.5&&typeof document.body.style.maxHeight==tt_u;}
else
{tt_db=document.documentElement||document.body||(document.getElementsByTagName?document.getElementsByTagName("body")[0]:null);if(!tt_op)
{n6=document.defaultView&&typeof document.defaultView.getComputedStyle!=tt_u;w3c=!n6&&document.getElementById;}}
tt_body=(document.getElementsByTagName?document.getElementsByTagName("body")[0]:(document.body||null));if(tt_ie||n6||tt_op||w3c)
{if(tt_body&&tt_db)
{if(document.attachEvent||document.addEventListener)
return true;}
else
tt_Err("wz_tooltip.js must be included INSIDE the body section,"
+" immediately after the opening <body> tag.",false);}
tt_db=null;return false;}
function tt_MkMainDiv()
{if(tt_body.insertAdjacentHTML)
tt_body.insertAdjacentHTML("afterBegin",tt_MkMainDivHtm());else if(typeof tt_body.innerHTML!=tt_u&&document.createElement&&tt_body.appendChild)
tt_body.appendChild(tt_MkMainDivDom());if(window.tt_GetMainDivRefs&&tt_GetMainDivRefs())
return true;tt_db=null;return false;}
function tt_MkMainDivHtm()
{return('<div id="WzTtDiV"></div>'+
(tt_ie56?('<iframe id="WzTtIfRm" src="javascript:false" scrolling="no" frameborder="0" style="filter:Alpha(opacity=0);position:absolute;top:0px;left:0px;display:none;"></iframe>'):''));}
function tt_MkMainDivDom()
{var el=document.createElement("div");if(el)
el.id="WzTtDiV";return el;}
function tt_GetMainDivRefs()
{tt_aElt[0]=tt_GetElt("WzTtDiV");if(tt_ie56&&tt_aElt[0])
{tt_aElt[tt_aElt.length-1]=tt_GetElt("WzTtIfRm");if(!tt_aElt[tt_aElt.length-1])
tt_aElt[0]=null;}
if(tt_aElt[0])
{var css=tt_aElt[0].style;css.visibility="hidden";css.position="absolute";css.overflow="hidden";return true;}
return false;}
function tt_ResetMainDiv()
{var w=(window.screen&&screen.width)?screen.width:10000;tt_SetTipPos(-w,0);tt_aElt[0].innerHTML="";tt_aElt[0].style.width=(w-1)+"px";tt_h=0;}
function tt_IsW3cBox()
{var css=tt_aElt[0].style;css.padding="10px";css.width="40px";tt_bBoxOld=(tt_GetDivW(tt_aElt[0])==40);css.padding="0px";tt_ResetMainDiv();}
function tt_OpaSupport()
{var css=tt_body.style;tt_flagOpa=(typeof(css.filter)!=tt_u)?1:(typeof(css.KhtmlOpacity)!=tt_u)?2:(typeof(css.KHTMLOpacity)!=tt_u)?3:(typeof(css.MozOpacity)!=tt_u)?4:(typeof(css.opacity)!=tt_u)?5:0;}
function tt_SetOnloadFnc()
{tt_AddEvtFnc(document,"DOMContentLoaded",tt_HideSrcTags);tt_AddEvtFnc(window,"load",tt_HideSrcTags);if(tt_body.attachEvent)
tt_body.attachEvent("onreadystatechange",function(){if(tt_body.readyState=="complete")
tt_HideSrcTags();});if(/WebKit|KHTML/i.test(navigator.userAgent))
{var t=setInterval(function(){if(/loaded|complete/.test(document.readyState))
{clearInterval(t);tt_HideSrcTags();}},10);}}
function tt_HideSrcTags()
{if(!window.tt_HideSrcTags||window.tt_HideSrcTags.done)
return;window.tt_HideSrcTags.done=true;if(!tt_HideSrcTagsRecurs(tt_body))
tt_Err("There are HTML elements to be converted to tooltips.\nIf you"
+" want these HTML elements to be automatically hidden, you"
+" must edit wz_tooltip.js, and set TagsToTip in the global"
+" tooltip configuration to true.",true);}
function tt_HideSrcTagsRecurs(dad)
{var ovr,asT2t;var a=dad.childNodes||dad.children||null;for(var i=a?a.length:0;i;)
{--i;if(!tt_HideSrcTagsRecurs(a[i]))
return false;ovr=a[i].getAttribute?(a[i].getAttribute("onmouseover")||a[i].getAttribute("onclick")):(typeof a[i].onmouseover=="function")?(a[i].onmouseover||a[i].onclick):null;if(ovr)
{asT2t=ovr.toString().match(/TagToTip\s*\(\s*'[^'.]+'\s*[\),]/);if(asT2t&&asT2t.length)
{if(!tt_HideSrcTag(asT2t[0]))
return false;}}}
return true;}
function tt_HideSrcTag(sT2t)
{var id,el;id=sT2t.replace(/.+'([^'.]+)'.+/,"$1");el=tt_GetElt(id);if(el)
{if(tt_Debug&&!TagsToTip)
return false;else
el.style.display="none";}
else
tt_Err("Invalid ID\n'"+id+"'\npassed to TagToTip()."
+" There exists no HTML element with that ID.",true);return true;}
function tt_Tip(arg,t2t)
{if(!tt_db)
return;if(tt_iState)
tt_Hide();if(!tt_Enabled)
return;tt_t2t=t2t;if(!tt_ReadCmds(arg))
return;tt_iState=0x1|0x4;tt_AdaptConfig1();tt_MkTipContent(arg);tt_MkTipSubDivs();tt_FormatTip();tt_bJmpVert=false;tt_bJmpHorz=false;tt_maxPosX=tt_GetClientW()+tt_scrlX-tt_w-1;tt_maxPosY=tt_GetClientH()+tt_scrlY-tt_h-1;tt_AdaptConfig2();tt_OverInit();tt_ShowInit();tt_Move();}
function tt_ReadCmds(a)
{var i;i=0;for(var j in config)
tt_aV[i++]=config[j];if(a.length&1)
{for(i=a.length-1;i>0;i-=2)
tt_aV[a[i-1]]=a[i];return true;}
tt_Err("Incorrect call of Tip() or TagToTip().\n"
+"Each command must be followed by a value.",true);return false;}
function tt_AdaptConfig1()
{tt_ExtCallFncs(0,"LoadConfig");if(!tt_aV[TITLEBGCOLOR].length)
tt_aV[TITLEBGCOLOR]=tt_aV[BORDERCOLOR];if(!tt_aV[TITLEFONTCOLOR].length)
tt_aV[TITLEFONTCOLOR]=tt_aV[BGCOLOR];if(!tt_aV[TITLEFONTFACE].length)
tt_aV[TITLEFONTFACE]=tt_aV[FONTFACE];if(!tt_aV[TITLEFONTSIZE].length)
tt_aV[TITLEFONTSIZE]=tt_aV[FONTSIZE];if(tt_aV[CLOSEBTN])
{if(!tt_aV[CLOSEBTNCOLORS])
tt_aV[CLOSEBTNCOLORS]=new Array("","","","");for(var i=4;i;)
{--i;if(!tt_aV[CLOSEBTNCOLORS][i].length)
tt_aV[CLOSEBTNCOLORS][i]=(i&1)?tt_aV[TITLEFONTCOLOR]:tt_aV[TITLEBGCOLOR];}
if(!tt_aV[TITLE].length)
tt_aV[TITLE]=" ";}
if(tt_aV[OPACITY]==100&&typeof tt_aElt[0].style.MozOpacity!=tt_u&&!Array.every)
tt_aV[OPACITY]=99;if(tt_aV[FADEIN]&&tt_flagOpa&&tt_aV[DELAY]>100)
tt_aV[DELAY]=Math.max(tt_aV[DELAY]-tt_aV[FADEIN],100);}
function tt_AdaptConfig2()
{if(tt_aV[CENTERMOUSE])
{tt_aV[OFFSETX]-=((tt_w-(tt_aV[SHADOW]?tt_aV[SHADOWWIDTH]:0))>>1);tt_aV[JUMPHORZ]=false;}}
function tt_MkTipContent(a)
{if(tt_t2t)
{if(tt_aV[COPYCONTENT])
tt_sContent=tt_t2t.innerHTML;else
tt_sContent="";}
else
tt_sContent=a[0];tt_ExtCallFncs(0,"CreateContentString");}
function tt_MkTipSubDivs()
{var sCss='position:relative;margin:0px;padding:0px;border-width:0px;left:0px;top:0px;line-height:normal;width:auto;',sTbTrTd=' cellspacing="0" cellpadding="0" border="0" style="'+sCss+'"><tbody style="'+sCss+'"><tr><td ';tt_aElt[0].innerHTML=(''
+(tt_aV[TITLE].length?('<div id="WzTiTl" style="position:relative;z-index:1;">'
+'<table id="WzTiTlTb"'+sTbTrTd+'id="WzTiTlI" style="'+sCss+'">'
+tt_aV[TITLE]
+'</td>'
+(tt_aV[CLOSEBTN]?('<td align="right" style="'+sCss
+'text-align:right;">'
+'<span id="WzClOsE" style="position:relative;left:2px;padding-left:2px;padding-right:2px;'
+'cursor:'+(tt_ie?'hand':'pointer')
+';" onmouseover="tt_OnCloseBtnOver(1)" onmouseout="tt_OnCloseBtnOver(0)" onclick="tt_HideInit()">'
+tt_aV[CLOSEBTNTEXT]
+'</span></td>'):'')
+'</tr></tbody></table></div>'):'')
+'<div id="WzBoDy" style="position:relative;z-index:0;">'
+'<table'+sTbTrTd+'id="WzBoDyI" style="'+sCss+'">'
+tt_sContent
+'</td></tr></tbody></table></div>'
+(tt_aV[SHADOW]?('<div id="WzTtShDwR" style="position:absolute;overflow:hidden;"></div>'
+'<div id="WzTtShDwB" style="position:relative;overflow:hidden;"></div>'):''));tt_GetSubDivRefs();if(tt_t2t&&!tt_aV[COPYCONTENT])
{tt_t2tDad=tt_t2t.parentNode||tt_t2t.parentElement||tt_t2t.offsetParent||null;if(tt_t2tDad)
{tt_MovDomNode(tt_t2t,tt_t2tDad,tt_aElt[6]);tt_t2t.style.display="block";}}
tt_ExtCallFncs(0,"SubDivsCreated");}
function tt_GetSubDivRefs()
{var aId=new Array("WzTiTl","WzTiTlTb","WzTiTlI","WzClOsE","WzBoDy","WzBoDyI","WzTtShDwB","WzTtShDwR");for(var i=aId.length;i;--i)
tt_aElt[i]=tt_GetElt(aId[i-1]);}
function tt_FormatTip()
{var css,w,h,pad=tt_aV[PADDING],padT,wBrd=tt_aV[BORDERWIDTH],iOffY,iOffSh,iAdd=(pad+wBrd)<<1;if(tt_aV[TITLE].length)
{padT=tt_aV[TITLEPADDING];css=tt_aElt[1].style;css.background=tt_aV[TITLEBGCOLOR];css.paddingTop=css.paddingBottom=padT+"px";css.paddingLeft=css.paddingRight=(padT+2)+"px";css=tt_aElt[3].style;css.color=tt_aV[TITLEFONTCOLOR];if(tt_aV[WIDTH]==-1)
css.whiteSpace="nowrap";css.fontFamily=tt_aV[TITLEFONTFACE];css.fontSize=tt_aV[TITLEFONTSIZE];css.fontWeight="bold";css.textAlign=tt_aV[TITLEALIGN];if(tt_aElt[4])
{css=tt_aElt[4].style;css.background=tt_aV[CLOSEBTNCOLORS][0];css.color=tt_aV[CLOSEBTNCOLORS][1];css.fontFamily=tt_aV[TITLEFONTFACE];css.fontSize=tt_aV[TITLEFONTSIZE];css.fontWeight="bold";}
if(tt_aV[WIDTH]>0)
tt_w=tt_aV[WIDTH];else
{tt_w=tt_GetDivW(tt_aElt[3])+tt_GetDivW(tt_aElt[4]);if(tt_aElt[4])
tt_w+=pad;if(tt_aV[WIDTH]<-1&&tt_w>-tt_aV[WIDTH])
tt_w=-tt_aV[WIDTH];}
iOffY=-wBrd;}
else
{tt_w=0;iOffY=0;}
css=tt_aElt[5].style;css.top=iOffY+"px";if(wBrd)
{css.borderColor=tt_aV[BORDERCOLOR];css.borderStyle=tt_aV[BORDERSTYLE];css.borderWidth=wBrd+"px";}
if(tt_aV[BGCOLOR].length)
css.background=tt_aV[BGCOLOR];if(tt_aV[BGIMG].length)
css.backgroundImage="url("+tt_aV[BGIMG]+")";css.padding=pad+"px";css.textAlign=tt_aV[TEXTALIGN];if(tt_aV[HEIGHT])
{css.overflow="auto";if(tt_aV[HEIGHT]>0)
css.height=(tt_aV[HEIGHT]+iAdd)+"px";else
tt_h=iAdd-tt_aV[HEIGHT];}
css=tt_aElt[6].style;css.color=tt_aV[FONTCOLOR];css.fontFamily=tt_aV[FONTFACE];css.fontSize=tt_aV[FONTSIZE];css.fontWeight=tt_aV[FONTWEIGHT];css.background="";css.textAlign=tt_aV[TEXTALIGN];if(tt_aV[WIDTH]>0)
w=tt_aV[WIDTH];else if(tt_aV[WIDTH]==-1&&tt_w)
w=tt_w;else
{w=tt_GetDivW(tt_aElt[6]);if(tt_aV[WIDTH]<-1&&w>-tt_aV[WIDTH])
w=-tt_aV[WIDTH];}
if(w>tt_w)
tt_w=w;tt_w+=iAdd;if(tt_aV[SHADOW])
{tt_w+=tt_aV[SHADOWWIDTH];iOffSh=Math.floor((tt_aV[SHADOWWIDTH]*4)/3);css=tt_aElt[7].style;css.top=iOffY+"px";css.left=iOffSh+"px";css.width=(tt_w-iOffSh-tt_aV[SHADOWWIDTH])+"px";css.height=tt_aV[SHADOWWIDTH]+"px";css.background=tt_aV[SHADOWCOLOR];css=tt_aElt[8].style;css.top=iOffSh+"px";css.left=(tt_w-tt_aV[SHADOWWIDTH])+"px";css.width=tt_aV[SHADOWWIDTH]+"px";css.background=tt_aV[SHADOWCOLOR];}
else
iOffSh=0;tt_SetTipOpa(tt_aV[FADEIN]?0:tt_aV[OPACITY]);tt_FixSize(iOffY,iOffSh);}
function tt_FixSize(iOffY,iOffSh)
{var wIn,wOut,h,add,pad=tt_aV[PADDING],wBrd=tt_aV[BORDERWIDTH],i;tt_aElt[0].style.width=tt_w+"px";tt_aElt[0].style.pixelWidth=tt_w;wOut=tt_w-((tt_aV[SHADOW])?tt_aV[SHADOWWIDTH]:0);wIn=wOut;if(!tt_bBoxOld)
wIn-=(pad+wBrd)<<1;tt_aElt[5].style.width=wIn+"px";if(tt_aElt[1])
{wIn=wOut-((tt_aV[TITLEPADDING]+2)<<1);if(!tt_bBoxOld)
wOut=wIn;tt_aElt[1].style.width=wOut+"px";tt_aElt[2].style.width=wIn+"px";}
if(tt_h)
{h=tt_GetDivH(tt_aElt[5]);if(h>tt_h)
{if(!tt_bBoxOld)
tt_h-=(pad+wBrd)<<1;tt_aElt[5].style.height=tt_h+"px";}}
tt_h=tt_GetDivH(tt_aElt[0])+iOffY;if(tt_aElt[8])
tt_aElt[8].style.height=(tt_h-iOffSh)+"px";i=tt_aElt.length-1;if(tt_aElt[i])
{tt_aElt[i].style.width=tt_w+"px";tt_aElt[i].style.height=tt_h+"px";}}
function tt_DeAlt(el)
{var aKid;if(el)
{if(el.alt)
el.alt="";if(el.title)
el.title="";aKid=el.childNodes||el.children||null;if(aKid)
{for(var i=aKid.length;i;)
tt_DeAlt(aKid[--i]);}}}
function tt_OpDeHref(el)
{if(!tt_op)
return;if(tt_elDeHref)
tt_OpReHref();while(el)
{if(el.hasAttribute("href"))
{el.t_href=el.getAttribute("href");el.t_stats=window.status;el.removeAttribute("href");el.style.cursor="hand";tt_AddEvtFnc(el,"mousedown",tt_OpReHref);window.status=el.t_href;tt_elDeHref=el;break;}
el=el.parentElement;}}
function tt_OpReHref()
{if(tt_elDeHref)
{tt_elDeHref.setAttribute("href",tt_elDeHref.t_href);tt_RemEvtFnc(tt_elDeHref,"mousedown",tt_OpReHref);window.status=tt_elDeHref.t_stats;tt_elDeHref=null;}}
function tt_OverInit()
{if(window.event)
tt_over=window.event.target||window.event.srcElement;else
tt_over=tt_ovr_;tt_DeAlt(tt_over);tt_OpDeHref(tt_over);}
function tt_ShowInit()
{tt_tShow.Timer("tt_Show()",tt_aV[DELAY],true);if(tt_aV[CLICKCLOSE]||tt_aV[CLICKSTICKY])
tt_AddEvtFnc(document,"mouseup",tt_OnLClick);}
function tt_Show()
{var css=tt_aElt[0].style;css.zIndex=Math.max((window.dd&&dd.z)?(dd.z+2):0,1010);if(tt_aV[STICKY]||!tt_aV[FOLLOWMOUSE])
tt_iState&=~0x4;if(tt_aV[DURATION]>0)
tt_tDurt.Timer("tt_HideInit()",tt_aV[DURATION],true);tt_ExtCallFncs(0,"Show")
css.visibility="visible";tt_iState|=0x2;if(tt_aV[FADEIN])
tt_Fade(0,0,tt_aV[OPACITY],Math.round(tt_aV[FADEIN]/tt_aV[FADEINTERVAL]));tt_ShowIfrm();}
function tt_ShowIfrm()
{if(tt_ie56)
{var ifrm=tt_aElt[tt_aElt.length-1];if(ifrm)
{var css=ifrm.style;css.zIndex=tt_aElt[0].style.zIndex-1;css.display="block";}}}
function tt_Move(e)
{if(e)
tt_ovr_=e.target||e.srcElement;e=e||window.event;if(e)
{tt_musX=tt_GetEvtX(e);tt_musY=tt_GetEvtY(e);}
if(tt_iState&0x04)
{if(!tt_op&&!tt_ie)
{if(tt_bWait)
return;tt_bWait=true;tt_tWaitMov.Timer("tt_bWait = false;",1,true);}
if(tt_aV[FIX])
{var iY=tt_aV[FIX][1];if(tt_aV[ABOVE])
iY-=tt_h;tt_iState&=~0x4;tt_SetTipPos(tt_aV[FIX][0],tt_aV[FIX][1]);}
else if(!tt_ExtCallFncs(e,"MoveBefore"))
tt_SetTipPos(tt_Pos(0),tt_Pos(1));tt_ExtCallFncs([tt_musX,tt_musY],"MoveAfter")}}
function tt_Pos(iDim)
{var iX,bJmpMode,cmdAlt,cmdOff,cx,iMax,iScrl,iMus,bJmp;if(iDim)
{bJmpMode=tt_aV[JUMPVERT];cmdAlt=ABOVE;cmdOff=OFFSETY;cx=tt_h;iMax=tt_maxPosY;iScrl=tt_scrlY;iMus=tt_musY;bJmp=tt_bJmpVert;}
else
{bJmpMode=tt_aV[JUMPHORZ];cmdAlt=LEFT;cmdOff=OFFSETX;cx=tt_w;iMax=tt_maxPosX;iScrl=tt_scrlX;iMus=tt_musX;bJmp=tt_bJmpHorz;}
if(bJmpMode)
{if(tt_aV[cmdAlt]&&(!bJmp||tt_CalcPosAlt(iDim)>=iScrl+16))
iX=tt_PosAlt(iDim);else if(!tt_aV[cmdAlt]&&bJmp&&tt_CalcPosDef(iDim)>iMax-16)
iX=tt_PosAlt(iDim);else
iX=tt_PosDef(iDim);}
else
{iX=iMus;if(tt_aV[cmdAlt])
iX-=cx+tt_aV[cmdOff]-(tt_aV[SHADOW]?tt_aV[SHADOWWIDTH]:0);else
iX+=tt_aV[cmdOff];}
if(iX>iMax)
iX=bJmpMode?tt_PosAlt(iDim):iMax;if(iX<iScrl)
iX=bJmpMode?tt_PosDef(iDim):iScrl;return iX;}
function tt_PosDef(iDim)
{if(iDim)
tt_bJmpVert=tt_aV[ABOVE];else
tt_bJmpHorz=tt_aV[LEFT];return tt_CalcPosDef(iDim);}
function tt_PosAlt(iDim)
{if(iDim)
tt_bJmpVert=!tt_aV[ABOVE];else
tt_bJmpHorz=!tt_aV[LEFT];return tt_CalcPosAlt(iDim);}
function tt_CalcPosDef(iDim)
{return iDim?(tt_musY+tt_aV[OFFSETY]):(tt_musX+tt_aV[OFFSETX]);}
function tt_CalcPosAlt(iDim)
{var cmdOff=iDim?OFFSETY:OFFSETX;var dx=tt_aV[cmdOff]-(tt_aV[SHADOW]?tt_aV[SHADOWWIDTH]:0);if(tt_aV[cmdOff]>0&&dx<=0)
dx=1;return((iDim?(tt_musY-tt_h):(tt_musX-tt_w))-dx);}
function tt_Fade(a,now,z,n)
{if(n)
{now+=Math.round((z-now)/n);if((z>a)?(now>=z):(now<=z))
now=z;else
tt_tFade.Timer("tt_Fade("
+a+","+now+","+z+","+(n-1)
+")",tt_aV[FADEINTERVAL],true);}
now?tt_SetTipOpa(now):tt_Hide();}
function tt_SetTipOpa(opa)
{tt_SetOpa(tt_aElt[5],opa);if(tt_aElt[1])
tt_SetOpa(tt_aElt[1],opa);if(tt_aV[SHADOW])
{opa=Math.round(opa*0.8);tt_SetOpa(tt_aElt[7],opa);tt_SetOpa(tt_aElt[8],opa);}}
function tt_OnScrl()
{tt_scrlX=tt_GetScrollX();tt_scrlY=tt_GetScrollY();}
function tt_OnCloseBtnOver(iOver)
{var css=tt_aElt[4].style;iOver<<=1;css.background=tt_aV[CLOSEBTNCOLORS][iOver];css.color=tt_aV[CLOSEBTNCOLORS][iOver+1];}
function tt_OnLClick(e)
{e=e||window.event;if(!((e.button&&e.button&2)||(e.which&&e.which==3)))
{if(tt_aV[CLICKSTICKY]&&(tt_iState&0x4))
{tt_aV[STICKY]=true;tt_iState&=~0x4;}
else if(tt_aV[CLICKCLOSE])
tt_HideInit();}}
function tt_Int(x)
{var y;return(isNaN(y=parseInt(x))?0:y);}
Number.prototype.Timer=function(s,iT,bUrge)
{if(!this.value||bUrge)
this.value=window.setTimeout(s,iT);}
Number.prototype.EndTimer=function()
{if(this.value)
{window.clearTimeout(this.value);this.value=0;}}
function tt_SetOpa(el,opa)
{var css=el.style;tt_opa=opa;if(tt_flagOpa==1)
{if(opa<100)
{if(typeof(el.filtNo)==tt_u)
el.filtNo=css.filter;var bVis=css.visibility!="hidden";css.zoom="100%";if(!bVis)
css.visibility="visible";css.filter="alpha(opacity="+opa+")";if(!bVis)
css.visibility="hidden";}
else if(typeof(el.filtNo)!=tt_u)
css.filter=el.filtNo;}
else
{opa/=100.0;switch(tt_flagOpa)
{case 2:css.KhtmlOpacity=opa;break;case 3:css.KHTMLOpacity=opa;break;case 4:css.MozOpacity=opa;break;case 5:css.opacity=opa;break;}}}
function tt_MovDomNode(el,dadFrom,dadTo)
{if(dadFrom)
dadFrom.removeChild(el);if(dadTo)
dadTo.appendChild(el);}
function tt_Err(sErr,bIfDebug)
{if(tt_Debug||!bIfDebug)
alert("Tooltip Script Error Message:\n\n"+sErr);}
function tt_ExtCmdEnum()
{var s;for(var i in config)
{s="window."+i.toString().toUpperCase();if(eval("typeof("+s+") == tt_u"))
{eval(s+" = "+tt_aV.length);tt_aV[tt_aV.length]=null;}}}
function tt_ExtCallFncs(arg,sFnc)
{var b=false;for(var i=tt_aExt.length;i;)
{--i;var fnc=tt_aExt[i]["On"+sFnc];if(fnc&&fnc(arg))
b=true;}
return b;}
tt_Init();config.Balloon=true
config.BalloonImgPath="../zig-api/jscripts/wz_tooltip/tip_balloon/"
config.BalloonEdgeSize=6
config.BalloonStemWidth=15
config.BalloonStemHeight=19
config.BalloonStemOffset=-7
var balloon=new tt_Extension();balloon.OnLoadConfig=function()
{if(tt_aV[BALLOON])
{balloon.padding=Math.max(tt_aV[PADDING]-tt_aV[BALLOONEDGESIZE],0);balloon.width=tt_aV[WIDTH];tt_aV[BORDERWIDTH]=0;tt_aV[WIDTH]=0;tt_aV[PADDING]=0;tt_aV[BGCOLOR]="";tt_aV[BGIMG]="";tt_aV[SHADOW]=false;if(tt_aV[BALLOONIMGPATH].charAt(tt_aV[BALLOONIMGPATH].length-1)!='/')
tt_aV[BALLOONIMGPATH]+="/";return true;}
return false;};balloon.OnCreateContentString=function()
{if(!tt_aV[BALLOON])
return false;var aImg,sImgZ,sCssCrn,sVaT,sVaB,sCssImg;if(tt_aV[BALLOONIMGPATH]==config.BalloonImgPath)
aImg=balloon.aDefImg;else
aImg=Balloon_CacheImgs(tt_aV[BALLOONIMGPATH]);sCssCrn=' style="position:relative;width:'+tt_aV[BALLOONEDGESIZE]+'px;padding:0px;margin:0px;overflow:hidden;line-height:0px;';sVaT='vertical-align:top;" valign="top"';sVaB='vertical-align:bottom;" valign="bottom"';sCssImg='padding:0px;margin:0px;border:0px;';sImgZ='" style="'+sCssImg+'" />';tt_sContent='<table border="0" cellpadding="0" cellspacing="0" style="width:auto;padding:0px;margin:0px;left:0px;top:0px;"><tr>'
+'<td'+sCssCrn+sVaB+'>'
+'<img src="'+aImg[1].src+'" width="'+tt_aV[BALLOONEDGESIZE]+'" height="'+tt_aV[BALLOONEDGESIZE]+sImgZ
+'</td>'
+'<td valign="bottom" style="position:relative;padding:0px;margin:0px;overflow:hidden;">'
+'<img id="bALlOOnT" style="position:relative;top:1px;z-index:1;display:none;'+sCssImg+'" src="'+aImg[9].src+'" width="'+tt_aV[BALLOONSTEMWIDTH]+'" height="'+tt_aV[BALLOONSTEMHEIGHT]+'" />'
+'<div style="position:relative;z-index:0;padding:0px;margin:0px;overflow:hidden;width:auto;height:'+tt_aV[BALLOONEDGESIZE]+'px;background-image:url('+aImg[2].src+');">'
+'</div>'
+'</td>'
+'<td'+sCssCrn+sVaB+'>'
+'<img src="'+aImg[3].src+'" width="'+tt_aV[BALLOONEDGESIZE]+'" height="'+tt_aV[BALLOONEDGESIZE]+sImgZ
+'</td>'
+'</tr><tr>'
+'<td style="position:relative;padding:0px;margin:0px;width:'+tt_aV[BALLOONEDGESIZE]+'px;overflow:hidden;background-image:url('+aImg[8].src+');">'
+'<img width="'+tt_aV[BALLOONEDGESIZE]+'" height="100%" src="'+aImg[8].src+sImgZ
+'</td>'
+'<td id="bALlO0nBdY" style="position:relative;line-height:normal;'
+';background-image:url('+aImg[0].src+')'
+';color:'+tt_aV[FONTCOLOR]
+';font-family:'+tt_aV[FONTFACE]
+';font-size:'+tt_aV[FONTSIZE]
+';font-weight:'+tt_aV[FONTWEIGHT]
+';text-align:'+tt_aV[TEXTALIGN]
+';padding:'+balloon.padding+'px'
+';width:'+((balloon.width>0)?(balloon.width+'px'):'auto')
+';">'+tt_sContent+'</td>'
+'<td style="position:relative;padding:0px;margin:0px;width:'+tt_aV[BALLOONEDGESIZE]+'px;overflow:hidden;background-image:url('+aImg[4].src+');">'
+'<img width="'+tt_aV[BALLOONEDGESIZE]+'" height="100%" src="'+aImg[4].src+sImgZ
+'</td>'
+'</tr><tr>'
+'<td'+sCssCrn+sVaT+'>'
+'<img src="'+aImg[7].src+'" width="'+tt_aV[BALLOONEDGESIZE]+'" height="'+tt_aV[BALLOONEDGESIZE]+sImgZ
+'</td>'
+'<td valign="top" style="position:relative;padding:0px;margin:0px;overflow:hidden;">'
+'<div style="position:relative;left:0px;top:0px;padding:0px;margin:0px;overflow:hidden;width:auto;height:'+tt_aV[BALLOONEDGESIZE]+'px;background-image:url('+aImg[6].src+');"></div>'
+'<img id="bALlOOnB" style="position:relative;top:-1px;left:2px;z-index:1;display:none;'+sCssImg+'" src="'+aImg[10].src+'" width="'+tt_aV[BALLOONSTEMWIDTH]+'" height="'+tt_aV[BALLOONSTEMHEIGHT]+'" />'
+'</td>'
+'<td'+sCssCrn+sVaT+'>'
+'<img src="'+aImg[5].src+'" width="'+tt_aV[BALLOONEDGESIZE]+'" height="'+tt_aV[BALLOONEDGESIZE]+sImgZ
+'</td>'
+'</tr></table>';return true;};balloon.OnSubDivsCreated=function()
{if(tt_aV[BALLOON])
{balloon.iStem=tt_aV[ABOVE]*1;balloon.aStem=[tt_GetElt("bALlOOnT"),tt_GetElt("bALlOOnB")];balloon.aStem[balloon.iStem].style.display="inline";if(balloon.width<-1)
Balloon_MaxW();return true;}
return false;};balloon.OnMoveAfter=function()
{if(tt_aV[BALLOON])
{var iStem=(tt_aV[ABOVE]!=tt_bJmpVert)*1;if(iStem!=balloon.iStem)
{balloon.aStem[balloon.iStem].style.display="none";balloon.aStem[iStem].style.display="inline";balloon.iStem=iStem;}
balloon.aStem[iStem].style.left=Balloon_CalcStemX()+"px";return true;}
return false;};function Balloon_CalcStemX()
{var x=tt_musX-tt_x+tt_aV[BALLOONSTEMOFFSET]-tt_aV[BALLOONEDGESIZE];return Math.max(Math.min(x,tt_w-tt_aV[BALLOONSTEMWIDTH]-(tt_aV[BALLOONEDGESIZE]<<1)-2),2);}
function Balloon_CacheImgs(sPath)
{var asImg=["background","lt","t","rt","r","rb","b","lb","l","stemt","stemb"],n=asImg.length,aImg=new Array(n),img;while(n)
{--n;img=aImg[n]=new Image();img.src=sPath+asImg[n]+".gif";}
return aImg;}
function Balloon_MaxW()
{var bdy=tt_GetElt("bALlO0nBdY");if(bdy)
{var iAdd=tt_bBoxOld?(balloon.padding<<1):0,w=tt_GetDivW(bdy);if(w>-balloon.width+iAdd)
bdy.style.width=(-balloon.width+iAdd)+"px";}}
function Balloon_PreCacheDefImgs()
{if(config.BalloonImgPath.charAt(config.BalloonImgPath.length-1)!='/')
config.BalloonImgPath+="/";balloon.aDefImg=Balloon_CacheImgs(config.BalloonImgPath);}
Balloon_PreCacheDefImgs();function zig_droplist_filter(droplist_filter_id,droplist_id,droplist_options_string)
{var selected_filter=document.getElementById(droplist_filter_id).options[document.getElementById(droplist_filter_id).selectedIndex].text;var first_character="";var counter=document.getElementById(droplist_id).length;while(counter>0)
{counter--;document.getElementById(droplist_id).remove(counter);}
var droplist_options=droplist_options_string.split(",");var droplist_options_length=droplist_options.length;counter=0;while(counter<droplist_options_length)
{first_character=droplist_options[counter].substr(0,1);if(selected_filter==first_character.toLowerCase()||(droplist_options[counter]==""&&counter==0)||selected_filter=="all"||selected_filter=="")
{var new_option=document.createElement('option');new_option.text=droplist_options[counter];try
{document.getElementById(droplist_id).add(new_option,null);}
catch(ex)
{document.getElementById(droplist_id).add(new_option.text);}}
counter++;}}