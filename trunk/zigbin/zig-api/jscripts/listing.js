function listing_sort(direction,column_number,table_id) {
	var column_content = new Array() ;
	var original_row_content = new Array() ;
	var original_row_node = new Array() ;
	var original_row_attributes = new Array() ;
	var original_row_attributes_name = new Array() ;
	var original_row_attributes_value = new Array() ;
	var row_length = document.getElementById(table_id).rows.length ;
	var column_length = document.getElementById(table_id).rows[0].cells.length ;
	var row=0, column=0 ;
	var counter

	for(row=1 ; row_length>row ; row++) {
		original_row_content[row] = document.getElementById(table_id).rows[row].innerHTML ;
		original_row_node[row] = document.getElementById(table_id).rows[row] ;
		original_row_attributes[row] = document.getElementById(table_id).rows[row].attributes ;
		counter = 0 ;
		original_row_attributes_name[row] = new Array() ;
		original_row_attributes_value[row] = new Array() ;
		while(counter<original_row_attributes[row].length)
		{
			original_row_attributes_name[row][counter] = original_row_attributes[row][counter].name ;
			original_row_attributes_value[row][counter] = original_row_attributes[row][counter].value ;
			// -- start attributes cleanup
			document.getElementById(table_id).rows[row].setAttribute(original_row_attributes[row][counter].name,"") ;
			// -- end attributes cleanup
			counter++ ;
		}
		column_content[row] = document.getElementById(table_id).rows[row].cells[column_number].innerHTML + "_row_" + row ;
	}

	var sorted_column = column_content.sort() ;
	var sequence = new Array() ;
	var ripped_content = new Array() ;

// Start get new row sequence
	for(row=0 ; row_length>row ; row++)
	{
		if(sorted_column[row])
		{
			ripped_content = sorted_column[row].split("_row_") ;
			sequence[row+1] = ripped_content[1] ;
		}
	}

	if(direction=="descending")
	{
		sequence.reverse() ;
		sequence.unshift("") ;
	}
// End get new row sequence

// Start parse new sorted values
	for(row=1 ; row_length>row ; row++) {
		document.getElementById(table_id).rows[row].innerHTML = original_row_content[sequence[row]] ;
		counter = 0 ;
		while(counter<original_row_attributes_name[sequence[row]].length)
		{
			if(original_row_attributes_value[sequence[row]][counter]!="" && original_row_attributes_value[sequence[row]][counter]!=null)
			{
				document.getElementById(table_id).rows[row].setAttribute(original_row_attributes_name[sequence[row]][counter],original_row_attributes_value[sequence[row]][counter]) ;
			}
			counter++ ;
		}
	}
// End parse new sorted values
}

function listing_sort_by_field(direction,field_name) {
	document.getElementById('zig_listing_sort_direction_id').value = direction ;
	document.getElementById('zig_listing_sort_field_id').value = field_name ;
	document.zig_form_listing.submit() ;
}

function listing_move(direction,column_number,table_id) {
	var original_content = new Array() ;
	var row_length = document.getElementById(table_id).rows.length ;
	var column_length = document.getElementById(table_id).rows[0].cells.length ;
	var row=0 ;

	if(direction=="left")
	{
		var other_column = Number(column_number) - 1 ;
		while(other_column>=0)
		{
			if(document.getElementById(table_id).rows[0].cells[other_column].style.display=="none")
			{
				other_column-- ;
			}
			else
			{
				break ;
			}
		}
	}
	else if(direction=="right")
	{
		var other_column = Number(column_number) + 1 ;
		while(other_column<column_length)
		{
			if(document.getElementById(table_id).rows[0].cells[other_column].style.display=="none")
			{
				other_column++ ;
			}
			else
			{
				break ;
			}
		}
	}

	if(!(column_number==0 && direction=="left") && !(column_number==(column_length-1) && direction=="right") && other_column>=0 && other_column<column_length)
	{
		// Start get original values
		for(row=0 ; row_length>row ; row++)
		{
			original_content[row] = new Array() ;
			original_content[row][column_number] = document.getElementById(table_id).rows[row].cells[column_number].innerHTML ;
			original_content[row][other_column] = document.getElementById(table_id).rows[row].cells[other_column].innerHTML ;
		}
		// End get original values

		// Start parse new values
		for(row=1 ; row_length>row ; row++)
		{
			document.getElementById(table_id).rows[row].cells[column_number].innerHTML = original_content[row][other_column] ;
			document.getElementById(table_id).rows[row].cells[other_column].innerHTML = original_content[row][column_number] ;
		}
		// End parse new values

		// Start swap column values
		var other_regexp = RegExp("zig_listing_column_"+other_column,"g") ;
		var column_regexp = RegExp("zig_listing_column_"+column_number,"g") ;
		document.getElementById(table_id).rows[0].cells[column_number].innerHTML = original_content[0][other_column].replace(other_regexp,"zig_listing_column_"+column_number) ;
		document.getElementById(table_id).rows[0].cells[other_column].innerHTML = original_content[0][column_number].replace(column_regexp,"zig_listing_column_"+other_column) ;
		// End swap column values
	}
}

function listing_truncate(method,field_name) {
	var truncate_option = method;
	if(truncate_option=="yes")
	{
		document.getElementById("zig_listing_truncate_" + field_name + "_id").value = "yes" ;
	}
	else
	{
		document.getElementById("zig_listing_truncate_" + field_name + "_id").value = "no" ;
	}
	document.zig_form_listing.submit() ;
}

function listing_alignment(alignment,column_id,column_number,table_id) {
	var row_length = document.getElementById(table_id).rows.length ;

	for(row=1 ; row_length>row ; row++)
	{
		document.getElementById(table_id).rows[row].cells[column_number].align = alignment ;
	}
}

function listing_show_hide(method,column_number,table_id) {
	var row_length = document.getElementById(table_id).rows.length ;
	var action = "" ;
	if(method=="hide")
	{
		action = "none" ;
	}

	for(row=0 ; row<row_length ; row++)
	{
		document.getElementById(table_id).rows[row].cells[column_number].style.display = action ;
	}
}

function listing_nowrap(method,column_number,table_id) {
	var row_length = document.getElementById(table_id).rows.length ;
	if(method=="nowrap")
	{
		var action = "nowrap" ;
	}
	else
	{
		var action = "" ;
	}

	for(row=1 ; row_length>row ; row++)
	{
		document.getElementById(table_id).rows[row].cells[column_number].style.whiteSpace = action ;
	}
}

function check_uncheck(listingPrefix) {
	var evaluate = document.getElementById(listingPrefix + "_all") ;
	if(evaluate.value == 0)
	{
		check_all(listingPrefix);
	}
	else
	{
		uncheck_all(listingPrefix);
	}
}

function check_all(listingPrefix) {
	var check = document.getElementById(listingPrefix + "_table").rows ;
	for(c=1; c<check.length; c++)
	{
		document.getElementById(listingPrefix + "_checkbox_" + c).checked = true;
		document.getElementById(listingPrefix + "_row_" + c).className = "zig_listing_row_highlighted";
	}
	document.getElementById(listingPrefix + "_all").value = 1 ;
}	

function uncheck_all(listingPrefix) {
	var uncheck = document.getElementById(listingPrefix + "_table").rows ;
	for(c=1; c<uncheck.length; c++)
	{
		document.getElementById(listingPrefix + "_checkbox_" + c).checked = false;
		document.getElementById(listingPrefix + "_row_" + c).className = document.getElementById(listingPrefix + "_class_" + c).value ;
	}
	document.getElementById(listingPrefix + "_all").value = 0;
}

//this manage the ie browser only and the rest goes to the css file
hover_rows = function() {				
	if (document.all && document.getElementById)
	{
		navRoot = document.getElementById('zig_listing_table');
		tbody = navRoot.childNodes[0];

		for (i = 1; i < tbody.childNodes.length; i++)
		{
			node = tbody.childNodes[i];
			if (node.nodeName == "TR")
			{
				node.onmouseover=function()
				{
					this.className = "hovered";								
				}

				node.onmouseout=function()
				{
					this.className = this.className.replace("hovered", "");
				}
			}
		}
	}
}

var timeout = 500 ;
var closetimer = 0 ;
var ddmenuitem = 0 ;
// open hidden layer

function mopen(id,current_event) {
	// cancel close timer
	mcancelclosetime();
	// close old layer
	if(ddmenuitem)
	{
		ddmenuitem.style.visibility = "hidden" ;
	}
	
	// get new layer and show it
	ddmenuitem = document.getElementById(id);
	ddmenuitem.style.visibility = "visible";
}
// close showed layer

function mclose() {
	if(ddmenuitem) ddmenuitem.style.visibility = "hidden" ;
}
// go close timer

function mclosetime() {
	closetimer = window.setTimeout(mclose, timeout);
}
// cancel close timer

function mcancelclosetime() {
	if(closetimer)
	{
		window.clearTimeout(closetimer);
		closetimer = null;
	}
}

function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}

function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp('^[" + chars + "]+', "g"), "");
}

function rtrim(str, chars)  {
	chars = chars || "\\s";
	return str.replace(new RegExp('[" + chars + "]+$', "g"), "");
}

function listingDelete(listingIDPrefix,zigHash) {
	var tableObject = document.getElementById(listingIDPrefix + "_table") ;
	var checkboxPrefix = listingIDPrefix + "_checkbox_" ;
	var id = "" ;
	var rowsToBeDeleted = new Array() ;
	switch(listingIsChecked(tableObject,checkboxPrefix)) {
		case true: {
			var response = confirm("Are you sure you want to delete these/this record(s)?") ;
			switch(response) {
				case true: {
					zigMessenger("Deleting...") ;
					var rows = tableObject.rows ;
					for(counter=1; counter<rows.length; counter++) {
						switch(document.getElementById(checkboxPrefix + counter)!=null) {
							case true: {
								switch(document.getElementById(checkboxPrefix + counter).checked) {
									case true: {
										id+= id ? "," + rows[counter].cells[1].innerHTML : rows[counter].cells[1].innerHTML ;
										break ;
									}
								}
							}
						}
					}

					var parameters = new Array() ;
					parameters['container'] = listingIDPrefix + "_div" ;
					parameters['return_function'] = function() {listingDeletePostHandler(listingIDPrefix)} ;
					zig(parameters,zigHash,"",id) ;
					break ;
				}
			}
		}
	}
}

function listingDeletePostHandler(listingIDPrefix) {
	if(zigResponseObject.data.message) {
		zigMessenger(zigResponseObject.data.message) ;
		return false ;
	}
	else {
		zigMessenger("") ;
	}
	var tableObject = document.getElementById(listingIDPrefix + "_table") ;
	var checkboxPrefix = listingIDPrefix + "_checkbox_" ;
	var id = "" ;
	var rowsToBeDeleted = new Array() ;
	var rows = tableObject.rows ;
	for(counter=1; counter<rows.length; counter++) {
		switch(document.getElementById(checkboxPrefix + counter)!=null) {
			case true: {
				switch(document.getElementById(checkboxPrefix + counter).checked) {
					case true: {
						id+= id ? "," + rows[counter].cells[1].innerHTML : rows[counter].cells[1].innerHTML ;
						rowsToBeDeleted[rowsToBeDeleted.length] = rows[counter].rowIndex ;
						break ;
					}
				}
			}
		}
	}
	for(counter=0; counter<rowsToBeDeleted.length; counter++) {
		tableObject.deleteRow(rowsToBeDeleted[counter]-(counter)) ;
	}
}

function listingIsChecked(tableObject,checkboxPrefix) {
	var rows = tableObject.rows ;
	for(counter=1; counter<rows.length; counter++)
	{
		switch(document.getElementById(checkboxPrefix + counter)!=null)
		{
			case true:
			{
				switch(document.getElementById(checkboxPrefix + counter).checked)
				{
					case true:
					{
						return true ;
					}
				}
			}
		}
	}
	return false ;
}

function listingCheckCheckbox(object,zigHash) {
	object.value==1 ? object.value=0 : object.value=1 ;
	zig("",zigHash,'','','',object.name + "=" + object.value) ;
}

function listingEnableView() {
	document.getElementById("viewFlag").value = true ;
}

function listingDisableView() {
	document.getElementById("viewFlag").value = false ;
}

function listingAddRecord(buttonObject,uniqueString,saveHash) {
	disableButton(buttonObject,"Saving") ;
	zig({loaderDiv:"spanSavingLoaderId",
		container:"divList_" + uniqueString,
		'form_id':uniqueString+'_form',
		return_function:"listingAddedRecord('" + buttonObject.id + "','" + uniqueString + "')"},
		saveHash) ;
}

function listingAddedRecord(buttonObjectId,uniqueString) {
	if(zigResponseObject.data.validation) {
		zigMessenger("Record Added") ;
		if(document.getElementById("divNoRecordsId_" + uniqueString))
		{
			document.getElementById("divAdd_" + uniqueString).innerHTML = "" ;
			document.getElementById("div_zig_triggers_" + uniqueString).style.display = "block" ;
		}
		document.getElementById("divAdd_" + uniqueString).innerHTML = document.getElementById("divAddReference_" + uniqueString).innerHTML ;
	}
	else {
		zigMessenger(zigResponseObject.data.message) ;
		enableButton(document.getElementById(buttonObjectId),"Save") ;
	}
}

function listingEditRecord(buttonObject,uniqueString,saveHash) {
	disableButton(buttonObject,"Saving") ;
	zig({loaderDiv:"spanSavingLoaderId",
		container:"divList_" + uniqueString,
		'form_id':uniqueString+'_form',
		return_function:"listingEditedRecord('" + buttonObject.id + "','" + uniqueString + "')"},
		saveHash) ;
}

function listingEditedRecord(buttonObjectId,uniqueString) {
	if(zigResponseObject.data.validation) {
		zigMessenger("Record Updated") ;
	}
	else {
		zigMessenger(zigResponseObject.data.message) ;
	}
	enableButton(document.getElementById(buttonObjectId),"Save") ;
}

function listingView(uniqueString,zig_hash) {
	switch(document.getElementById("viewFlag").value) {
		case "true": {
			updatePageTitle("mode","View") ;
			var divID = "divView_" + uniqueString ;
			zig(divID,zig_hash) ;
			if(document.getElementById("div_zig_filters_" + uniqueString)) {
				document.getElementById("div_zig_filters_" + uniqueString).style.display = "none" ;
			}
			document.getElementById("divList_" + uniqueString).style.display = "none" ;
			document.getElementById(divID).style.display = "block" ;
		}
	}
}

function listingAdd(uniqueString,zigHash)  {
	updatePageTitle("mode","Add") ;
	var divID = "divAdd_" + uniqueString ;
	switch(document.getElementById(divID).innerHTML) {
		case "": {
			zig({"container":""+divID+"","return_function":"listingAddReference('"+uniqueString+"')"},zigHash) ;
		}
		default: {
			if(document.getElementById("div_zig_filters_" + uniqueString)) {
				document.getElementById("div_zig_filters_" + uniqueString).style.display = "none" ;
			}
			document.getElementById("divList_" + uniqueString).style.display = "none" ;
			document.getElementById(divID).style.display = "block" ;
		}
	}
}

function listingAddReference(uniqueString) {
	document.getElementById("divAddReference_" + uniqueString).innerHTML = document.getElementById("divAdd_" + uniqueString).innerHTML ;
}

function listingBackToList(uniqueString) {
	updatePageTitle("mode","Search") ;
	if(document.getElementById("div_zig_filters_" + uniqueString)) {
		document.getElementById("div_zig_filters_" + uniqueString).style.display = "block" ;
	}
	document.getElementById("divList_" +uniqueString).style.display = "block" ;
	document.getElementById("divAdd_" + uniqueString).style.display = "none" ;
	document.getElementById("divView_" + uniqueString).style.display = "none" ;
}

function listingViewEdit() {
	switch(document.getElementById("listingViewEdit").value) {
		case "Edit": {
			updatePageTitle("mode","Edit") ;
			document.getElementById("listingViewEdit").value = "View" ;
			document.getElementById("divViewFields").style.display = "none" ;
			document.getElementById("divEditFields").style.display = "block" ;
			break ;
		}
		case "View": {
			updatePageTitle("mode","View") ;
			document.getElementById("listingViewEdit").value = "Edit" ;
			document.getElementById("divEditFields").style.display = "none" ;
			document.getElementById("divViewFields").style.display = "block" ;
			break ;
		}
	}
}