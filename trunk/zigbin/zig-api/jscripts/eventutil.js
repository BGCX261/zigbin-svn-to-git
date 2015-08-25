// for filters.sql.php
var INPUT_NAME_PREFIX = 'text'; 
var TABLE_NAME = 'table_zig_listing_filters'; 
var ROW_BASE = 0; 
function filters_myRowObject(one, two, three, four)
{
	this.one = one; 
	this.two = two; 
	this.three = three; 
	this.four = four;
}

function filters_insertRowToTable()
{
	var tbl = document.getElementById(TABLE_NAME);
	var rowToInsertAt = tbl.tBodies[0].rows.length;
	for (var i=0; i<tbl.tBodies[0].rows.length; i++) {
		if (tbl.tBodies[0].rows[i].myRow && tbl.tBodies[0].rows[i].myRow.four.getAttribute('type') == 'radio' && tbl.tBodies[0].rows[i].myRow.four.checked) {
			rowToInsertAt = i;
			break;
		}
	}
	filters_addRowToTable(rowToInsertAt);
	filters_reorderRows(tbl, rowToInsertAt);
}

function filters_addRowToTable(num)
{
	var w = document.form_zig_filters.zig_filter_select.selectedIndex;
	if(w)
	{
		var tbl = document.getElementById(TABLE_NAME);
		var nextRow = tbl.tBodies[0].rows.length;
		var iteration = nextRow + ROW_BASE;

		if(num == null)
		{ 
			num = nextRow;
		}
		else
		{
			iteration = num + ROW_BASE;
		}
		
		var row = tbl.tBodies[0].insertRow(num);
		row.className = 'classy' + (iteration % 2);
 		var selected_label = document.form_zig_filters.zig_filter_select.options[w].text ;
 		var selected_text = document.form_zig_filters.zig_filter_select.value ;
		var cell0 = row.insertCell(0);
		cell0.innerHTML = "&nbsp;<a href='javascript: return void(0) ;' onclick='filters_deleteCurrentRow(this) ;'>remove</a>&nbsp;" ;
		
		var cell1 = row.insertCell(1);
		var textNode = document.createTextNode(selected_label);
		cell1.appendChild(textNode);	
		
		var cell2 = row.insertCell(2);
  		cell2.innerHTML = "<select name='op_"+ selected_text +"'><option value='='>=</option><option value='<'>&lt;</option><option value='>'>&gt;</option><option value='<='>&le;</option><option value='>='>&ge;</option><option value='!='>&ne;</option><option value='LIKE'>contains</option></select>" ;
		
		var cell3 = row.insertCell(3);
		var txtInp = document.createElement('input');
		txtInp.setAttribute('type', 'text');
		txtInp.setAttribute('name', selected_text);
		txtInp.setAttribute('size', '20');
		txtInp.setAttribute('value', '');
		cell3.appendChild(txtInp);
		
//		var x = (document.getElementById('zig_temp').value = document.getElementById('zig_temp').value ? document.getElementById('zig_temp').value + "," + selected_text : selected_text) ;
		document.getElementById('zig_include').value = document.getElementById('zig_include').value ? document.getElementById('zig_include').value + "," + selected_text : selected_text ;
		document.form_zig_filters.zig_filter_select.selectedIndex = 0 ;
		row.myRow = new filters_myRowObject(textNode, txtInp, cbEl, raEl) ;
	}
}

function filters_deleteRows(rowObjArray)
{
	for (var i=0; i<rowObjArray.length; i++)
	{
		var rIndex = rowObjArray[i].sectionRowIndex ;
		rowObjArray[i].parentNode.deleteRow(rIndex) ;
		document.getElementById('zig_include').value = document.getElementById('zig_include').value.replace("," + rowObjArray[i].parentNode.value,"") ;
		document.getElementById('zig_include').value = document.getElementById('zig_include').value.replace(rowObjArray[i].parentNode.value,"") ;
	}
}

function filters_deleteCurrentRow(obj)
{
	var delRow = obj.parentNode.parentNode;
	var tbl = delRow.parentNode.parentNode;
	var rIndex = delRow.sectionRowIndex;
	var rowArray = new Array(delRow);
	filters_deleteRows(rowArray);
	filters_reorderRows(tbl, rIndex);
}

function filters_reorderRows(tbl, startingIndex)
{
	if (tbl.tBodies[0].rows[startingIndex])
	{
		var count = startingIndex + ROW_BASE;
		for (var i=startingIndex; i<tbl.tBodies[0].rows.length; i++)
		{
			tbl.tBodies[0].rows[i].myRow.one.data = count;
			tbl.tBodies[0].rows[i].myRow.two.name = INPUT_NAME_PREFIX + count;
			var tempVal = tbl.tBodies[0].rows[i].myRow.two.value.split(' ');
			tbl.tBodies[0].rows[i].myRow.two.value = count + ' was' + tempVal[0]; 
			tbl.tBodies[0].rows[i].myRow.four.value = count; 
			tbl.tBodies[0].rows[i].className = 'classy' + (count % 2);
			count++;
		}
	}
}