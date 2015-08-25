function install(installButtonObject,zig_hash,zig_hash_install_admin)
{
	disableButton(installButtonObject,"Installing") ;
	var username = document.getElementById('database_username_id').value ;
	var password = document.getElementById('database_password_id').value ;
	var host = document.getElementById('host_id').value ;
	var database = document.getElementById('database_id').value ;
	var default_database = document.getElementById('default_id').checked==true ? 1 : 0 ;
	var empty_database = document.getElementById('zig_empty_database_id') ? (document.getElementById('zig_empty_database_id').checked ? 1 : 0) : 0 ;
	var result = false ;
	if(username!="")
	{
		var container = Array() ;
		container['container'] = "div_zig_message" ;
		container['return_function'] = "install_admin_load('" + zig_hash_install_admin + "','" + installButtonObject.id + "') ;" ;
		zig(container,zig_hash,username,password,database,"host=" + host + "&default_database=" + default_database + "&empty_database=" + empty_database) ;
	}
	else
	{
		alert("Username should not be blank!") ;
	}
}

function install_admin(zig_hash)
{
	var password = document.getElementById('password_id').value ;
	var confirm_password = document.getElementById('confirm_id').value ;
	if(password==confirm_password)
	{
		var container = Array() ;
		container['container'] = "div_zig_message" ;
		container['return_function'] = "install_admin_done() ;" ;
		zig(container,zig_hash,password,"install") ;
	}
	else
	{
		alert("Passwords are not the same!") ;
	}
}

function install_admin_load(zig_hash_install_admin,buttonId) {
	switch(document.getElementById("div_zig_message").innerHTML.indexOf("Installation successful and complete!")>-1) {
		case true: {
			zig("zig_div_install_content",zig_hash_install_admin) ;
			break ;
		}
		default: {
			enableButton(document.getElementById(buttonId),"Install") ;
		}
	}
	return true ;
}

function install_admin_done()
{
	switch(document.getElementById("div_zig_message").innerHTML.indexOf("New admin password successfully installed!")>-1)
	{
		case true:
		{
			document.getElementById('div_zig_message').innerHTML+= "<br />Redirecting you now to the login page..." ;
			window.location = "../" ;
		}
	}
}

window.onload = function()
{
	if(document.getElementById("database_password_id"))
	{
		document.getElementById("database_password_id").focus() ;
	}
	else if(document.getElementById("installingId"))
	{
	var installButtonObject = document.getElemntById("installingId") ;
	var zig_hash = document.getElementById("zig_hash").value ;
	var zig_hash_install_admin = document.getElementById("zig_hash_install_admin").value ;
	zig(installButtonObject,zig_hash,zig_hash_install_admin) ;
	}
}