function zig_empty_trash(container,zig_hash)
{
	response = confirm("Records in Trash will be deleted permanently") ;
	if(response)
	{
		zig(container,zig_hash) ;
	}
}