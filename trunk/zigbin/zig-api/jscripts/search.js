function searchKeypress(searchInputElement,zigHash,uniqueString,event)
{
	switch(zig_keycode(event))
	{
		case 13:
		{
			zig('divList_' + uniqueString,zigHash,'','','','zig_keyword=' + searchInputElement.value) ;
			event.returnValue = false ;
			return false ;
		}
	}
}

function searchLabelFocus(searchInputElement)
{
	switch(searchInputElement.className=="inputBasicSearchLabel")
	{
		case true:
		{
			searchInputElement.value = "" ;
			searchInputElement.className = "inputBasicSearch" ;
		}
	}
}

function searchLabelBlur(searchInputElement)
{
	switch(searchInputElement.value=="")
	{
		case true:
		{
			searchInputElement.className = "inputBasicSearchLabel" ;
			searchInputElement.value = "Search Records" ;
			break ;
		}
	}
}