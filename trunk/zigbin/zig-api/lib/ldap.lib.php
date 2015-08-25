<?php

class zig_ldap
{
	function ldap($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$method = $arg1 ;
			$data = $arg2 ;
			//$search_dn = $arg3 ;
			$password = $arg3;
		}
		else if(is_array($parameters))
		{
			$method = array_key_exists("method",$parameters) ? $parameters['method'] : NULL ;
			$data = array_key_exists("data",$parameters) ? $parameters['data'] : NULL ;
			$search_dn = array_key_exists("search_dn",$parameters) ? $parameters['search_dn'] : NULL ;
			$filter = array_key_exists("filter",$parameters) ? $parameters['filter'] : NULL ;
			$model_filter = array_key_exists("model_filter",$parameters) ? $parameters['model_filter'] : NULL ;
			$key = array_key_exists("key",$parameters) ? $parameters['key'] : NULL ;
			
		}
		
		$host_return = zig("config","ldap") ;
		if(!is_array($host_return))
		{
			$host[] = $host_return ;
		}
		else
		{
			$host = $host_return ;
		}
		
		$base_dn_return = zig("config","ldap_dn") ;
		if(!is_array($base_dn_return))
		{
			$base_dn[] = $base_dn_return ;
		}
		else
		{
			$base_dn = $base_dn_return ;
		}
		
		$user = "uid=" . $data . ",cn=users," . $base_dn_return;
		
		$counter = 0 ;
		foreach($host as $value)
		{
			$resource_link = @ldap_connect($value) or die("error-104 : unable to connect to LDAP server") ;
			ldap_set_option($resource_link, LDAP_OPT_PROTOCOL_VERSION, 3) ;
			ldap_get_option($resource_link, LDAP_OPT_PROTOCOL_VERSION, $get_return); 
			ldap_get_option($resource_link, LDAP_OPT_PROTOCOL_VERSION, $get_return); 
			
			$ldap_connect++ ;
			switch($method)
			{
				case $method=="bind":
				{
					//$zig_result['value'] = @ldap_bind($resource_link, $filter.",".$base_dn[$counter], $password) ;
					$zig_result['value'] = @ldap_bind($resource_link, $user, $password) ;
					if($zig_result['value'] != NULL or $zig_result['value'] != 0)
					{
						$sql_check_permissions = "SELECT users FROM zig_permissions WHERE users='$data'";
						$check_permissions['value'] = zig("query", $sql_check_permissions);
					
						if($check_permissions['value'] == NULL or $check_permissions['value'] == 0)
						{			
							$sql_insert_permissions = "INSERT INTO zig_permissions (`zig_user`, `users`, `module`, `tab`, `action`, `fieldset`, `field_name`, `field_value`, `permission`, `frequency`, `total_frequency`)
													   VALUES ('admin', '$data', 'zig-assets', 'all', 'all', 'all', 'all', 'all', 'allow', '-1', '-1'),
													   		  ('admin', '$data', 'zig-home', 'all', 'all', 'all', 'all', 'all', 'allow', '-1', '-1')	
													   	";
							zig("query", $sql_insert_permissions);
						}
					}
					break;
				}
				
				case $method=="synchronize":
				{
					$synchronize_parameters = array
					(
							'function'		=>	"synchronize",
							'resource_link'	=>	$resource_link,
							'base_dn'		=>	$search_dn.",".$base_dn[$counter],
							'table'			=>	$data,
							'filter'		=>	$filter,
							'model_filter'	=>	$model_filter,
							'key'			=>	$key
						
					) ;
					$zig_result['value'] = $this->ldap_synchronize($synchronize_parameters) ;
				}
				
			}
					
			if($zig_result['value'])
			{
				break ;
			}
			ldap_close($resource_link) ;
			$counter++ ;
		}

		if(!$ldap_connect)
		{
			die("error-104 : unable to connect to LDAP server") ;
		}
		
		$zig_result['return'] = 1 ;
		return $zig_result ;
	}

	function ldap_synchronize($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$resource_link = $arg1 ;
			$base_dn = $arg2 ;
			$table = $arg3 ;
		}
		else if(is_array($parameters))
		{
			$resource_link = array_key_exists("resource_link",$parameters) ? $parameters['resource_link'] : NULL ;
			$base_dn = array_key_exists("base_dn",$parameters) ? $parameters['base_dn'] : NULL ;
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$filter = array_key_exists("filter",$parameters) ? $parameters['filter'] : NULL ;
			$model_filter = array_key_exists("model_filter",$parameters) ? $parameters['model_filter'] : NULL ;
			$key = array_key_exists("key",$parameters) ? $parameters['key'] : NULL ;
			$filter_string = $filter ? "&&".$filter : NULL ;
		}

		$ldap_result = ldap_search($resource_link,$base_dn,$model_filter) ;
		$ldap_fetch_array = ldap_get_entries($resource_link,$ldap_result) ;
		$ldap_fetch = $ldap_fetch_array[0] ;
		$counter = 0 ;
		$ldap_array_size = sizeof($ldap_fetch) ;
		while($counter<$ldap_fetch['count'])
		{
			$ldap_fields[] = $ldap_fetch[$counter] ;
			$counter++ ;
		}

		$sql = "SHOW COLUMNS FROM $table" ;
		$result = zig("query",$sql) ;
		while($fetch=$result->fetchRow())
		{
			if(in_array($fetch['Field'],$ldap_fields))
			{
				$sql_fields[] = $fetch['Field'] ;
				$fields = $fields ? $fields.",`".$fetch['Field']."`" : "`".$fetch['Field']."`" ;
			}
		}

		$ldap_result = ldap_search($resource_link,$base_dn,$filter) ;
		$ldap_fetch_array = ldap_get_entries($resource_link,$ldap_result) ;

		$counter = 0 ;
		while($counter<$ldap_fetch_array['count'])
		{
			$ldap_fetch = $ldap_fetch_array[$counter] ;
			$ldap_key_value = is_array($ldap_fetch[$key]) ? $ldap_fetch[$key][0] : $ldap_fetch[$key] ;
			$ldap_key_value = addslashes(utf8_decode($ldap_key_value)) ;
			if($ldap_key_value)
			{
				$sql = "SELECT $fields FROM $table WHERE `$key`='$ldap_key_value' LIMIT 1" ;
				$result = zig("query",$sql,"ldap.lib.php") ;
				if($result->RecordCount())
				{
					// -- start update
					$fetch = $result->fetchRow() ;
					foreach($sql_fields as $field_name)
					{
						$ldap_value = is_array($ldap_fetch[$field_name]) ? $ldap_fetch[$field_name][0] : $ldap_fetch[$field_name] ;
						$ldap_value = utf8_decode($ldap_value) ;
						if(strcmp($ldap_value,$fetch[$field_name]))
						{
							$ldap_value = addslashes($ldap_value) ;
							$set = $set ? $set.",`$field_name`='$ldap_value'" : "`$field_name`='$ldap_value'" ;
						}
					}
					if($set)
					{
						zig("update",$table,$set,"WHERE `$key`='$ldap_key_value' LIMIT 1") ;
						unset($set) ;
					}
					// -- end update
				}
				else
				{
					// -- start insert
					foreach($sql_fields as $field_name)
					{
						$ldap_value = is_array($ldap_fetch[$field_name]) ? $ldap_fetch[$field_name][0] : $ldap_fetch[$field_name] ;
						$ldap_value = addslashes(utf8_decode($ldap_value)) ;
						$values.= ",'".$ldap_value."'" ;
					}
					zig("insert",$table,"zig_created,zig_user,".$fields,"NOW(),'ldap.lib.php'".$values) ;
					unset($values) ;
					// -- end insert
				}
				$ldap_where = $ldap_where ? $ldap_where." AND `$key`<>'$ldap_key_value'" : "WHERE `zig_status`<>'deleted' AND `$key`<>'$ldap_key_value'" ;
			}
			$counter++ ;
		}

		// -- start remove entries
		zig("update",$table,"`zig_status`='deleted'",$ldap_where) ;
		// -- end remove entries
		
		$zig_result = $fields ? true : false ;
		return $zig_result ;
	}
}

?>