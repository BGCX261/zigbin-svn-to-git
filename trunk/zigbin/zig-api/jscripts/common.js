function checkdate (m, d, y) {
    // Returns true(1) if it is a valid date in gregorian calendar  
    return m > 0 && m < 13 && y > 0 && y < 32768 && d > 0 && d <= (new Date(y, m, 0)).getDate();
}

// -- start countdown
function countdown(span_id,element_id,maximum,tolerance,minimum)
{
	if(maximum)
	{
		var characters = document.getElementById(element_id).value.length ;
		var remaining = maximum - characters ;
		var percentage_tolerance = maximum*(tolerance/100) ;
		if(remaining<percentage_tolerance || minimum>=remaining)
		{
			document.getElementById(span_id).innerHTML = "[" + remaining + "]" ;
		}
		else
		{
			document.getElementById(span_id).innerHTML = "" ;
		}
	}
}
// -- end countdown

//-- start enable button
function enableButton(button,newValue)
{
	button.value = newValue ;
	button.disabled = false ;
}
// -- end enable button

// -- start disable button
function disableButton(button,newValue)
{
	button.value = newValue ;
	button.disabled = true ;
}
// -- end disable button

function mktime () {
    var d = new Date(),
        r = arguments,
        i = 0,
        e = ['Hours', 'Minutes', 'Seconds', 'Month', 'Date', 'FullYear'];
 
    for (i = 0; i < e.length; i++) {
        if (typeof r[i] === 'undefined') {
            r[i] = d['get' + e[i]]();
            r[i] += (i === 3); // +1 to fix JS months.
        } else {
            r[i] = parseInt(r[i], 10);
            if (isNaN(r[i])) {
                return false;
            }
        }
    }
 
    // Map years 0-69 to 2000-2069 and years 70-100 to 1970-2000.
    r[5] += (r[5] >= 0 ? (r[5] <= 69 ? 2e3 : (r[5] <= 100 ? 1900 : 0)) : 0);
 
    // Set year, month (-1 to fix JS months), and date.
    // !This must come before the call to setHours!
    d.setFullYear(r[5], r[3] - 1, r[4]);
 
    // Set hours, minutes, and seconds.
    d.setHours(r[0], r[1], r[2]);
 
    // Divide milliseconds by 1000 to return seconds and drop decimal.
    // Add 1 second if negative or it'll be off from PHP by 1 second.
    return (d.getTime() / 1e3 >> 0) - (d.getTime() < 0);
}

// -- start fieldset
function zig_fieldset(fieldset_id,div_id,callback)
{
	var zig_fieldset = document.getElementById(fieldset_id) ;
	var zig_div_fieldset = document.getElementById(div_id) ;
	var fieldset_class = zig_fieldset.className ;
	if(fieldset_class=="zig_fieldset_displayed")
	{
		zig_fieldset.className = "zig_fieldset_collapsed" ;
		zig_div_fieldset.className = "zig_invisible" ;
	}
	else
	{
		zig_fieldset.className = "zig_fieldset_displayed" ;
		zig_div_fieldset.className = "" ;
	}
	if(callback)
	{
		setTimeout(callback,0) ;
	}
}
// -- end fieldset

// -- start keycode
function zig_keycode(e)
{	
	//e is event object passed from function invocation
	var characterCode ; //literal character code will be stored in this variable
	if(e && e.which)
	{
		//if which property of event object is supported (NN4)
		e = e ;
		characterCode = e.which ; //character code is contained in NN4's which property
	}
	else
	{
		e = event ;
		characterCode = e.keyCode ; //character code is contained in IE's keyCode property
	}
	return characterCode ;
}
// -- end keycode

// -- start suggest
var suggest_div_id ;
function suggest_toggle(id,action)
{
	action = action ? action : (document.getElementById(id).style.display=="block" ? "hide" : "show" ) ;
	if(document.getElementById(id))
	{
		switch(action)
		{
			case "show":
			{
				if(document.getElementById(suggest_div_id))
				{
					document.getElementById(id).setAttribute("pointer","out") ;
					document.getElementById(suggest_div_id).style.display = "none" ;
				}
				document.getElementById(id).style.display = "block" ;
				break ;
			}
			default:
			{
				document.getElementById(id).setAttribute("pointer","out") ;
				document.getElementById(id).style.display = "none" ;
				break ;
			}
		}
		suggest_div_id = id ;
	}
}

function suggest_toggle_hide()
{
	if(document.getElementById(suggest_div_id))
	{
		switch(document.getElementById(suggest_div_id).getAttribute("pointer"))
		{
			case "over":
			{
				break ;
			}
			default:
			{
				document.getElementById(suggest_div_id).setAttribute("pointer","over") ;
				suggest_toggle(suggest_div_id,"hide") ;
			}
		}
	}
}

function suggest_select(id,input_id,selected_value)
{
	document.getElementById(input_id).value = selected_value ;
	suggest_toggle(id,"hide") ;
}

function suggest_keyboard(id,input_id,e)
{
	var keycode = zig_keycode(e) ;
	var div_elements = document.getElementById(id).getElementsByTagName("div") ;
	var number_of_options = document.getElementById(id).getElementsByTagName("div").length ;
	var current_option = -1 ;
	var counter = 0 ;
	while(counter<number_of_options)
	{
		if(div_elements.item(counter).className.search("_selected")!=-1)
		{
			current_option = counter ;
			break ;
		}
		counter++ ;
	}

	switch(keycode)
	{
		case 13: // return
		{
			suggest_select(id,input_id,div_elements.item(current_option).innerHTML) ;
			break ;
		}
		case 38: // arrow up
		{
			switch(current_option)
			{
				case -1:
				case 0:
				{
					var new_option = number_of_options - 1 ;
					break ;
				}
				default:
				{
					var new_option = current_option - 1 ;
					break ;
				}
			}
			break ;
		}
		case 40: // arrow down
		{
			switch(current_option)
			{
				case (number_of_options - 1):
				{
					var new_option = 0 ;
					break ;
				}
				default:
				{
					var new_option = current_option + 1 ;
					break ;
				}
			}
			break ;
		}
	}

	switch(current_option)
	{
		case -1:
		{
			break ;
		}
		default:
		{
			div_elements.item(current_option).className = div_elements.item(current_option).className.replace("_selected","") ;
			break ;
		}
	}
	div_elements.item(new_option).className = div_elements.item(new_option).className + "_selected" ;
}

function suggest_filter(id,input_id,droplist_options_string)
{
	var input_filter = document.getElementById(input_id).value.toLowerCase() ;	
	var first_character = "" ;
	var droplist_options = droplist_options_string.split(",") ;
	var droplist_options_length = droplist_options.length ;
	var new_div_options = "" ;
	var new_div_options_counter = 0 ;
	var counter = 0 ;
	var number_of_options = document.getElementById(id).getElementsByTagName("div").length ;

	while(counter<droplist_options_length)
	{
		first_character = droplist_options[counter].substr(0,input_filter.length) ;
		if(input_filter==first_character.toLowerCase() || (droplist_options[counter]=="" && counter==0) || input_filter=="")
		{
			new_div_options+= "<div class='zig_droplist_suggest_div_option' onclick=\"suggest_select('" + id + "','" + input_id + "','" + droplist_options[counter] + "') ;\">" + droplist_options[counter] + "</div>" ;
			new_div_options_counter++ ;
		}
		counter++ ;
	}

	if((new_div_options.length && counter!=new_div_options_counter) || (counter==new_div_options_counter && number_of_options!=new_div_options_counter))
	{
		document.getElementById(id).innerHTML = new_div_options ;
		suggest_toggle(id,"show") ;
	}
	else if(!new_div_options.length)
	{
		suggest_toggle(id,"hide") ;
	}
}
zig_listener(document.body,"click",suggest_toggle_hide,false) ;
// -- end suggest

// -- start total
function total_update(div_id,field_id)
{
	var total = 0 ;
	var counter = 1 ;
	var splitted_field_id = field_id.split("_") ;
	var row_number = splitted_field_id[splitted_field_id.length - 2] ;
	var finding_field_pre = field_id.split(row_number + "_id") ;
	var field_pre = finding_field_pre[0] ;

	while(document.getElementById(field_pre + counter + "_id"))
	{
		total = total + Number(document.getElementById(field_pre + counter + "_id").value) ;
		counter++ ;
	}
	document.getElementById(div_id).innerHTML = addCommas(total.toFixed(2)) ;
}

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1))
	{
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}
// -- end total

// -- start trigger
function zig_confirmation(action,redirection_link)
{
	var response = confirm("Are you sure you want to " + action + " this item(s)?") ;
	if(response)
	{
		window.location = redirection_link ;
	}
	return false ;
}
// -- end trigger

function isArray(obj)
{
	return(typeof(obj.length)=="undefined") ? false : true ;
}

// -- start zigjax
function zig(container,parameters,arg1,arg2,arg3,arg4)
{
	if(!arg1 && !arg2 && !arg3 && !parameters && container && !((container instanceof Array) || (typeof(container)=='object')))
	{
		var url = container ;
		container = null ;
	}
	else if(parameters && !(parameters instanceof Array))	
	{
		var arguments = "" ;
		if(arg1)
		{
			arguments = "&arg1=" + arg1 ;
		}
		if(arg2)
		{
			arguments+= "&arg2=" + arg2 ;
		}
		if(arg3)
		{
			arguments+= "&arg3=" + arg3 ;
		}
		if(arg4)
		{
			arguments+= "&" + arg4 ;
		}
		var url = "../zig-api/decoder.php?zig_hash=" + parameters + arguments ;
		if((container instanceof Array) || (container!==null && typeof(container)=='object'))
		{
			container['zig_hash'] = parameters ;
		}
	}
	else if((container instanceof Array) || (container!==null && typeof(container)=='object'))
	{
		var url = container['file'] ;
	}
	var splittedFile = url.split("?") ;
	var content = "" ;
	switch(splittedFile.length>1)
	{
		case true:
		{
			url = splittedFile[0] ;
			content = splittedFile[1] ;
			break ;
		}
	}
	return zigjax(url,container,content) ;
}

/*
zigjax - usually called by the function zig
string url=NULL required - url to be requested
array parameters 
	string $container=NULL not required
	string form_id=NULL not required
	string loaderDiv=NULL not required
	boolean loader=true [true,false] not required
	string $method="get" [post,get] not required
	string $return_function=NULL not required
	string zig_hash=NULL
string content=NULL not required - data to send
*/ 
function zigjax(url,parameters,content)
{
	var method = content ? "post" : "get" ;
	var custom_parameters = new Array() ;
	if((parameters instanceof Array) || (parameters!==null && typeof(parameters)=='object'))
	{
		if(parameters['form_id']!=undefined && parameters['form_id']!="")
		{
			var content = zigjax_form_encoder(parameters['form_id']) ;
			content = parameters['zig_hash']!=undefined ? "zig_hash=" + parameters['zig_hash'] + "&" + content : content ;
			method = "post" ;
		}
		else
		{
			method = (parameters['method'] && parameters['method']!=undefined) ? parameters['method'] : method ;
		}
		if(parameters['loader']==undefined)
		{
			var loader = true ;
		}
		else
		{
			var loader = parameters['loader'] ;
		}
		if(parameters['loaderDiv']==undefined)
		{
			var loaderDiv = parameters['container'] ;
		}
		else
		{
			var loaderDiv = parameters['loaderDiv'] ;
		}
		var container = parameters['container'] ;
		custom_parameters['return_function'] = parameters['return_function'] ;
	}
	else
	{
		var container = parameters ;
		var loaderDiv = container ;
		var loader = true ;
	}
	if(document.getElementById(loaderDiv) && loader)
	{
		document.getElementById(loaderDiv).innerHTML = "<div id='zig_div_ajax_loader'><img src='../zig-api/gui/themes/default/img/16x16/actions/ajax-loader.gif' /></div>" ;
	}
	else if(document.getElementById(container))
	{
		document.getElementById(container).innerHTML = "&nbsp;" ;
	}
	custom_parameters['loaderDiv'] = loaderDiv ;
	custom_parameters['container'] = container ;
	zigjax_request(url,method,content,custom_parameters) ;
}

function zigjax_form_encoder(form_id)
{
	var encoded_form = "" ;
	var elementValue = "" ;
	if(document.getElementById(form_id))
	{
		var elements = document.getElementById(form_id).elements ;
		var counter = 0 ;
		while(counter<elements.length)
		{
			if(elements[counter].name!=undefined)
			{
				switch(elements[counter].type)
				{
					case "file":
					{
						if(document.getElementById(elements[counter].id+"_zigHiddenFileName"))
						{
							if(document.getElementById(elements[counter].id+"_zigHiddenFileName").value!="")
							{
								elementValue = document.getElementById(elements[counter].id+"_zigHiddenFileName").value ;
								break ;
							}
						}
					}
					default:
					{
						elementValue = elements[counter].value ;
					}
				}
				encoded_form = encoded_form ? encoded_form + "&" : encoded_form ;
				encoded_form += elements[counter].name + "=" + encodeURI(elementValue) ;
			}
			counter++ ;
		}
	}
	return encoded_form ;
}

var zigResponse = "" ;
var zigResponseObject = "" ;
function zigjax_request(file,method,content,custom_parameters)
{
	var xmlHttp ;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest() ;
	}
	catch (e)
	{
		// Internet Explorer
		try
	    {
    		xmlHttp=new ActiveXObject("Msxml2.XMLHTTP") ;
	    }
		catch (e)
	    {
    		try
	    	{
	    		xmlHttp=new ActiveXObject("Microsoft.XMLHTTP") ;
			}
	    	catch (e)
			{
				alert("Your browser does not support AJAX!") ;
				return false ;
			}
	    }
	}

	xmlHttp.onreadystatechange=function()
	{
		// -- Start ajax optional response handler
		if(xmlHttp.readyState==4)
		{
			zigResponse = xmlHttp.responseText ;
			var loaderDiv = custom_parameters['loaderDiv'] ;
			var container = custom_parameters['container'] ;
			var return_function = custom_parameters['return_function'] ;

			if(document.getElementById(loaderDiv))
			{
				document.getElementById(loaderDiv).innerHTML = "&nbsp;" ;
			}

			// -- Start print response to container
			try {
				zigResponseObject = JSON.parse(zigResponse) ;
				if(zigResponseObject.html) {
					document.getElementById(container).innerHTML = zigResponseObject.html ;
				}
				else if(document.getElementById(container)) {
					document.getElementById(container).innerHTML = zigResponse ;
				}
			}
			catch(error) {
				if(document.getElementById(container)) {
					document.getElementById(container).innerHTML = zigResponse ;
				}	
			}
			// -- End print response to container

			// -- Start trigger return function
			if(return_function)
			{
				setTimeout(return_function,0) ;
			}
			// -- End trigger return function
		}
		// -- End ajax optional response handler
	}

	switch(method)
	{
		case "post":
		{
			xmlHttp.open("POST",file,true) ;
			xmlHttp.setRequestHeader("Content-type","application/x-www-form-urlencoded") ;
			xmlHttp.send(content) ;
			break ;
		}
		default:
		{
			xmlHttp.open("GET",file,true) ;
			xmlHttp.send(null) ;
			break ;
		}
	}
}
// --end zigjax

function updatePageTitle(type,newString) {
	var explodedTitle = document.title.split(" | ") ;
	var application = explodedTitle[0] ;
	var tab = explodedTitle[1] ;
	var mode = explodedTitle[2] ;
	switch(type) {
		case "application": {
			application = newString ;
			break ;
		}
		case "tab": {
			tab = newString ;
			break ;
		}
		case "mode": {
			mode = newString ;
			break ;
		}
	}
	var newTitle = application ;
	if(tab != "") {
		newTitle = newTitle + " | " + tab ;
	}
	if(mode != "") {
		newTitle = newTitle + " | " + mode ;
	}
	document.title = newTitle ;
}

function zig_tabs(table_id,cell_number,tab_link) {
	var counter = 0 ;
	var cells_length = document.getElementById(table_id).rows[0].cells.length ;
	updatePageTitle("tab",document.getElementById("zig_tab_link_name_" + cell_number).innerHTML) ;
	while(counter<cells_length) {
		switch(document.getElementById(table_id).rows[0].cells[counter].className) {
			case "zig_td_tab": {
				break ;
			}
			default: {
				document.getElementById(table_id).rows[0].cells[counter].className = "zig_td_tab" ;
				document.getElementById("zig_tab_link_name_" + counter).className = "" ;
			}
		}
		counter++ ;
	}
	document.getElementById(table_id).rows[0].cells[cell_number].className = "zig_tab_active" ;
	document.getElementById("zig_tab_link_name_" + cell_number).className = "zig_tab_div_active" ;
	zigjax(tab_link,'div_zig_body_wrapper') ;
}

function zig_hidden_iframe(zig_hash)
{
	var iframe = document.createElement("iframe") ;
	iframe.src = "../zig-api/decoder.php?zig_hash=" + zig_hash ;
	iframe.style.display = "none" ;
	document.body.appendChild(iframe) ;
}

function zig_editField(viewFieldContainerID,editFieldContainerID,editFieldID,fileLinksID,uploadLinksID)
{
	switch(document.getElementById(viewFieldContainerID).className)
	{
		case "zig_invisible":
		{
			break ;
		}
		default:
		{
			switch(document.getElementById(editFieldID).type)
			{
				case "file":
				{
					if(document.getElementById(viewFieldContainerID).innerHTML.replace(/&nbsp;/g,"")!="")
					{
						break ;
					}
				}
				default:
				{
					resetToEdit(viewFieldContainerID,editFieldContainerID,editFieldID,fileLinksID,uploadLinksID) ;
				}
			}
		}
	}
}

function zig_viewField(viewFieldContainerID,editFieldContainerID,editFieldObject,zigHash,fieldName,fileLinksID,uploadLinksID,passedUniqueString)
{
	switch(document.getElementById(viewFieldContainerID).className)
	{
		case "zig_display":
		{
			break ;
		}
		default:
		{
			switch(editFieldObject.tagName.toLowerCase())
			{
				case "select":
				{
					viewValue = editFieldObject.options[editFieldObject.selectedIndex].text ;
					break ;
				}
				default:
				{
					viewValue = editFieldObject.value ;
				}
			}
			var viewValue = viewValue=="" ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : viewValue ;
			switch(document.getElementById(viewFieldContainerID).innerHTML!=viewValue)
			{
				case true:
				{
					switch(editFieldObject.type)
					{
						case "file":
						{
							switch(editFieldObject.value!="")
							{
								case true:
								{
									document.getElementById(viewFieldContainerID).innerHTML = "Uploading..." ;
									uploadFile(editFieldObject,zigHash,fieldName,viewFieldContainerID,"divList_" + passedUniqueString) ;
									break ;
								}
							}
							break ;
						}
						case "password":
						{
							switch(editFieldObject.value!="")
							{
								case true:
								{
									zig("divList_" + passedUniqueString,zigHash,'','','',fieldName + "=" + editFieldObject.value) ;
									editFieldObject.value = "" ;
								}
							}
							break ;
						}
						default:
						{
							document.getElementById(viewFieldContainerID).innerHTML = viewValue ;
							//zig("divList_" + passedUniqueString,zigHash,'','','',fieldName + "=" + editFieldObject.value) ;
							zig({"container":"divList_" + passedUniqueString, 
								"return_function":"modifiedRecord()"},zigHash,'','','', 
								fieldName + "=" + editFieldObject.value) ;
						}
					}
				}
			}
			resetToView(viewFieldContainerID,editFieldContainerID,fileLinksID,uploadLinksID) ;
		}
	}
}

function modifiedRecord() {
	if(zigResponseObject.data.validation) {
		zigMessenger(zigResponseObject.data.message) ;
	}
}

function resetToView(viewFieldContainerID,editFieldContainerID,fileLinksID,uploadLinksID)
{
	document.getElementById(viewFieldContainerID).className = "zig_display" ;
	document.getElementById(editFieldContainerID).className = "zig_invisible" ;
	if(document.getElementById(uploadLinksID))
	{
		document.getElementById(uploadLinksID).className = "zig_invisible" ;
	}
	if(document.getElementById(viewFieldContainerID).innerHTML.replace(/&nbsp;/g,"")!="")
	{
		if(document.getElementById(fileLinksID))
		{
			document.getElementById(fileLinksID).className = "zig_display" ;
		}
	}
}

function resetToEdit(viewFieldContainerID,editFieldContainerID,editFieldID,fileLinksID,uploadLinksID)
{
	document.getElementById(viewFieldContainerID).className = "zig_invisible" ;
	document.getElementById(editFieldContainerID).className = "zig_display" ;
	document.getElementById(editFieldID).focus() ;
	if(document.getElementById(fileLinksID))
	{
		document.getElementById(fileLinksID).className = "zig_invisible" ;
	}
	if(document.getElementById(uploadLinksID))
	{
		document.getElementById(uploadLinksID).className = "zig_display" ;
	}
}

function removeFile(viewFieldContainerID,editFieldContainerID,editFieldObject,zigHash,fieldName,fileLinksID,uploadLinksID)
{
	switch(confirm("Are you sure you want to remove this file?"))
	{
		case true:
		{
			document.getElementById(viewFieldContainerID).innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ;
			resetToView(viewFieldContainerID,editFieldContainerID,fileLinksID,uploadLinksID) ;
			document.getElementById(fileLinksID).className = "zig_invisible" ;
			zig("",zigHash,'','','',fieldName + "=") ;
		}
	}
}

function viewCalendarField(viewFieldContainerID,editFieldContainerID,editFieldObject,zigHash,fieldName)
{
	delay(50) ;
	switch(document.getElementById("CalendarPickerControl").style.visibility=="visible")
	{
		case false:
		{
			zig_viewField(viewFieldContainerID,editFieldContainerID,editFieldObject,zigHash,fieldName) ;
		}
	}
}

function delay(milliseconds) 
{
	var startDate = new Date() ;
	var currentDate = null ;

	do
	{
		currentDate = new Date() ; 
	} 
	while(currentDate-startDate<milliseconds) ;
}

function loadReportFilters(zigHash,reportName)
{
	document.getElementById("divReportContent").innerHTML = "" ;
	document.getElementById("divPrintIconHolderId").style.visibility = "hidden" ;
	zig('divReportFilters',zigHash,reportName) ;
}

function startReport(file)
{
	document.getElementById("divPrintIconHolderId").style.visibility = "visible" ;
	zig({'file':file,'container':'divReportContent','form_id':'formReportFilters'}) ;
}

function zigMessenger(message)
{
	document.getElementById("div_zig_message").innerHTML = message ;
	setTimeout(function(){document.getElementById("div_zig_message").innerHTML="";},4000) ;
}