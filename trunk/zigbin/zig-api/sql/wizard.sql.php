<?php

class zig_wizard
{
	function wizard($parameters,$arg1='',$arg2='',$arg3='')
	{
		$module = array_key_exists("module",$_GET) ? $_GET['module'] : $GLOBALS['zig']['current']['module'] ;
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
			$search_sql = $arg2 ;
			$id = $arg3 ;
			$unserialize = false ;
			$zigjax = false ;
		}
		if(is_array($parameters))
		{
			$table = array_key_exists("table",$parameters) ? $parameters['table'] : NULL ;
			$search_sql = array_key_exists("search_sql",$parameters) ? $parameters['search_sql'] : NULL ;
			$id = array_key_exists("id",$parameters) ? $parameters['id'] : NULL ;
			$action = array_key_exists("action",$parameters) ? $parameters['action'] : NULL ;
			$zigjax = array_key_exists("zigjax",$parameters) ? $parameters['zigjax'] : (isset($zigjax) ? $zigjax : false) ;
			$unserialize = array_key_exists("unserialize",$parameters) ? $parameters['unserialize'] : (isset($unserialize) ? $unserialize : false) ;
			$parentId = array_key_exists("parentId",$parameters) ? $parameters['parentId'] : NULL ;
			$parentTable = array_key_exists("parentTable",$parameters) ? $parameters['parentTable'] : NULL ;
			$module = array_key_exists("module",$parameters) ? $parameters['module'] : $module ;
		}

		if(!is_array($_GET))
		{
			$_GET = array() ;
		}
		if(!is_array($_POST))
		{
			$_POST = array() ;
		}
		$zig_keyword = NULL ;
		$page = NULL ;
		$load_point = array_key_exists("load_point",$_GET) ? $_GET['load_point'] : NULL ;
		switch($load_point)
		{
			case "":
			case NULL:
			{
				zig("security") ;
				break ;
			}
			default:
			{
				$zig_result['load_point'] = $_GET['load_point'] ;
				break ;
			}
		}
		$zig_hash = isset($_GET['zig_hash']) ? $_GET['zig_hash'] : (isset($_POST['zig_hash']) ? $_POST['zig_hash'] : '') ;
		$zig_hash_result = $zig_hash ? zig("hash","vars_decode",$zig_hash) : zig("config","zig_action") ;

		if(is_array($zig_hash_result))
		{
			$action = $zig_hash_result['action'] ? $zig_hash_result['action'] : $action ;
			$id = $zig_hash_result['id'] ? $zig_hash_result['id'] : $id ;
			$page = zig("checkArray",$zig_hash_result,"page") ;
			$zig_keyword = (array_key_exists('zig_keyword',$zig_hash_result) and $zig_hash_result['zig_keyword']) ? trim($zig_hash_result['zig_keyword']) : (isset($_GET['zig_keyword']) ? trim($_GET['zig_keyword']) : (isset($_POST['zig_keyword']) ? trim($_POST['zig_keyword']) : '') ) ;
			$zig_attach = (array_key_exists('zig_attach',$zig_hash_result) and $zig_hash_result['zig_attach']) ? $zig_hash_result['zig_attach'] : (isset($_GET['zig_attach']) ? $_GET['zig_attach'] : (isset($_POST['zig_attach']) ? $_POST['zig_attach'] : '') ) ;
		}
		else
		{
			$action = $zig_hash_result ;
			$zig_basic_search = array_key_exists("zig_basic_search",$_GET) ? ($_GET['zig_basic_search'] ? $_GET['zig_basic_search'] : NULL) : NULL ;
			$zig_basic_search = $zig_basic_search ? $zig_basic_search : (array_key_exists("zig_basic_search",$_POST) ? ($_POST['zig_basic_search'] ? $_POST['zig_basic_search'] : NULL) : NULL) ;
			if($zig_basic_search)
			{
				$zig_keyword = isset($_GET['zig_keyword']) ? trim($_GET['zig_keyword']) : (isset($_POST['zig_keyword']) ? trim($_POST['zig_keyword']) : '') ;
			}
			$zig_attach = isset($_GET['zig_attach']) ? $_GET['zig_attach'] : (isset($_POST['zig_attach']) ? $_POST['zig_attach'] : '') ;
		}
		$zig_keyword = stripslashes($zig_keyword) ;
		$zig_action = isset($_GET['zig_action']) ? $_GET['zig_action'] : (isset($_POST['zig_action']) ? $_POST['zig_action'] : NULL) ;
		$action = $zig_action ? zig("hash","decrypt",$zig_action) : $action ;
		$zig_hash_vars = zig("hash","encrypt","action=search,zig_keyword=${zig_keyword},zig_attach=${zig_attach},page=${page}") ;

		if($action<>"search" and $action<>"view" and $action<>"edit" and $action<>"copy" and $action<>"add" and $action<>"print" and $action<>"export" and $action<>"delete" and $action<>"void")
		{
			$action = "search" ;
		}

		// Start Permission Check
		$zig_permission = zig("permissions","","{any}") ;
		if(!$zig_permission)
		{
			header("Location: ../zig-api/index.php") ;
			exit() ;
		}
		// End Permission Check

		$zig_global_database = zig("config","global_database") ;
		$module_pre = zig("config","pre",$GLOBALS['zig']['current']['module']) ;
		
		// Start remove the database name on the table
		$semi_stripped_table = str_replace($zig_global_database.".","",$table) ;
		// End remove the database name on the table

		// Start stripped table name
		$stripped_table = str_replace($module_pre,"",$semi_stripped_table) ;
		// End stripped table name

		// -- Start Define Exclude List
		$zig_user = zig("info","user") ;
		$zig_group = zig("info","group") ;

		$pre = zig("config","pre") ;
		$permission_sql = "SELECT action,field_name,field_value,permission FROM `${zig_global_database}`.`${pre}permissions` WHERE (users='$zig_user' OR users='$zig_group') AND (field_name<>'' OR field_value<>'') AND field_name<>'all'" ;
		$permission_result = zig("query",$permission_sql) ;
		$field_value_permissions = array() ;
		while($permission_fetch = $permission_result->fetchRow())
		{
			if($permission_fetch['field_value'] and $permission_fetch['field_value']<>"all")
			{
				$field_value_permissions[$permission_fetch['field_name']][$permission_fetch['action']][$permission_fetch['field_value']] = $permission_fetch['permission'] ;
			}
			else
			{
				$field_value_permissions[$permission_fetch['field_name']][$permission_fetch['action']] = $permission_fetch['permission'] ;
			}
		}

		$exclude = zig("config","exclude") ;
		if($action == "void" or $action == "delete")
		{
			if($action=="void")
			{
				$set = " `zig_status`='void' " ;
			}
			else if($action=="delete")
			{
				$set = " `zig_status`='deleted' " ;
			}
			$where = " WHERE `id`='$id' " ;
			$parameters = array
			(
				'function'	=>	'update',
				'table'		=>	$table,
				'set'		=>	$set,
				'where'		=>	$where,
				'limit'		=>	'LIMIT 1'
			) ;
			
			zig($parameters) ;
			unset($parameters) ;
			$zig_info['action'] = $action ;
			$action = "search" ;

			// -- Start moving data
			zig("trash",$table,$id) ;
			// -- End moving data
		}

		switch($action)
		{
			case "search":
			case "print":
			{
				foreach($exclude as $key => $value)
				{
					if($value=="id")
					{
						unset($exclude[$key]) ;
					}
				}
				break ;
			}
		}

		$permission_sql = "SELECT action,field_name,field_value,permission FROM `${zig_global_database}`.`${pre}permissions` WHERE (users='$zig_user' OR users='$zig_group') AND (field_name<>'' OR field_value<>'') AND action='$action' AND permission='deny'" ;
		$permission_result = zig("query",$permission_sql) ;
		
		while($permission_fetch = $permission_result->fetchRow())
		{
			if(!in_array($permission_fetch['field_name'],$exclude) and !($field_value_permissions[$permission_fetch['field_name']]['view']=="allow" and ($action=="add" or $action=="edit" or $action=="copy")))
			{
				$exclude[] = $permission_fetch['field_name'] ;
			}
		}
		// -- End Field Value Exclude List

		if($action=="search" or $action=="print")
		{
			$filter_variables['table_name'] = $table ;
			$filter_variables['semi_stripped_table'] = $semi_stripped_table ;
			$filter_variables['stripped_table'] = $stripped_table ;
			$filter_variables['fieldset'] = "Main" ;
			$filter_variables['sql'] = $search_sql ? $search_sql : NULL ;
			$filter_parameters_holder[] = $filter_variables ;
			if($action == "search")
			{
				$relationship_sql = "SELECT `child_table`,`fieldset`,`sql_statement` FROM `zig_relationships` WHERE (`parent_table`='$table' OR `parent_table`='$semi_stripped_table' OR `parent_table`='$stripped_table' OR `parent_table`='all tables') AND `child_table`<>'' AND `zig_status`<>'deleted' ORDER BY `zig_weight`,`fieldset`,`child_table`" ;
				$relationship_result = zig("query",$relationship_sql) ;
				while($relationship_fetch=$relationship_result->fetchRow())
				{
					$filter_variables['semi_stripped_table'] = str_replace($zig_global_database.".","",$relationship_fetch['child_table']) ;
					$filter_variables['stripped_table'] = str_replace($pre.".","",$filter_variables['semi_stripped_table']) ;
					$filter_variables['table_name'] = "${zig_global_database}.${pre}".$filter_variables['stripped_table'] ;
					$filter_variables['fieldset'] = $relationship_fetch['fieldset'] ? $relationship_fetch['fieldset'] : zig("string_format",str_replace("_"," ",$filter_variables['stripped_table']),"titlecase") ;
					$filter_variables['sql'] = $relationship_fetch['sql_statement'] ;
					$filter_parameters[] = $filter_variables ;
				}
			}
			
			foreach($filter_parameters_holder as $filter_settings)
			{

			$sql = "SELECT `field` FROM `zig_fields` WHERE (table_name='$filter_settings[table_name]' OR table_name='$filter_settings[semi_stripped_table]' OR table_name='$filter_settings[stripped_table]') AND attribute='hidden'" ;
			$result = zig("query",$sql) ;
			while($fetch=$result->fetchRow())
			{
				$exclude[] = $fetch['field'] ;
			}

			$sql = "SHOW COLUMNS FROM $filter_settings[table_name]" ;
			$result = zig("query",$sql) ;
			$fields = "" ;
			while($fetch=$result->fetchRow())
			{
				if(!in_array($fetch['Field'],$exclude))
				{
					$fields.= $fields ? ",`${module_pre}$filter_settings[stripped_table]`.`".$fetch['Field']."`" : "`${module_pre}$filter_settings[stripped_table]`.`".$fetch['Field']."`" ;
					$search_fields[] = $fetch['Field'] ;
				}
			}

			// -- Start Field Value Exclude List
			$permission_sql = "SELECT field_name,field_value FROM `${zig_global_database}`.`${pre}permissions` WHERE (users='$zig_user' OR users='$zig_group') AND field_value<>'' AND field_value<>'all' AND permission='deny'" ;
			$permission_result = zig("query",$permission_sql) ;

			$exclude_field_value = "" ;
			while($permission_fetch = $permission_result->fetchRow())
			{
				if(in_array($permission_fetch['field_name'],$search_fields))
				{
					if($permission_fetch['field_name']=="all")
					{
//						$exclude_field_value.= " AND $permission_fetch[field_name]<>'$permission_fetch[field_value]' " ;
					}
					else
					{
						$exclude_field_value.= " AND $permission_fetch[field_name]<>'$permission_fetch[field_value]' " ;
					}
				}
			}
			// -- End Field Value Exclude List

			$filter_settings['sql'] = $filter_settings['sql'] ? $filter_settings['sql'] : "SELECT $fields FROM $filter_settings[table_name] WHERE `zig_status`<>'deleted' $exclude_field_value ORDER BY `zig_updated` DESC" ;
			$filter_parameters[] = $filter_settings ;

			// -- Start check if there is record on this query else change action to Add
			if($filter_settings['fieldset']=="Main")
			{
				$search_sql = $filter_settings['sql'] ;
			}
			// -- End check if there is record on this query else change action to Add

			}
		}

		if($action=="add")
		{
			$parameters = array
			(
				'function'		=>	'add',
				'module'		=>	$module,
				'table'			=>	$table,
				'exclude'		=>	$exclude,
				'parentId'		=>	$parentId,
				'parentTable'	=>	$parentTable,
				'permissions'	=>	$field_value_permissions
			) ;
		}
		else if($action=="view" or $action=="edit" or $action=="copy" or ($action=="print" and $id))
		{
			switch($action)
			{
				case "print":
				{
					$function = "print_preview" ;
				}
				default:
				{
					$function = $action ;
				}
			}
			$parameters = array
			(
				'function'		=>	$function,
				'module'		=>	$module,
				'table'			=>	$table,
				'exclude'		=>	$exclude,
				'id'			=>	$id,
				'print_view'	=>	true,
				'permissions'	=>	$field_value_permissions
			) ;
		}
		else if($action == "export")
		{
			$search_sql = $search_sql ? $search_sql : $id ? "SELECT * FROM $table WHERE `id`='$id' LIMIT 1" : "SELECT * FROM $table WHERE `zig_status`<>'deleted'"  ;
			zig("export",$search_sql) ;
			exit() ;
		}
		else if($action == "print" and !$id)
		{
			$parameters = array
			(
				'function'			=>	"listing" ,
				'module'			=>	$module,
				'table'				=>	$table,
				'filter_sql'		=>	$search_sql,
				'sql'				=>	$search_sql ,
				'zig_keyword'		=>	$zig_keyword,
				'zig_attach'		=>	$zig_attach,
				'row_limit'			=>	0,
				'print_view'		=>	true
			) ;
			if($unserialize)
			{
				$parameters['unserialize'] = $unserialize ;
			}
		}

		if($action == "search")
		{
			$parameters = array
			(
				'function'			=>	"search",
				'module'			=>	$module,
				'table'				=>	$table,
				'filter_sql'		=>	$search_sql,
				'sql'				=>	$search_sql,
				'zig_keyword'		=>	$zig_keyword,
				'zig_attach'		=>	$zig_attach,
				'page'				=>	$page,
				'trigger_list'		=>	true,
			) ;

			if($unserialize)
			{
				$parameters['unserialize'] = $unserialize ;
			}
		}

		$zig_return = zig($parameters) ;
		switch($action)
		{
			case "print":
			{
				$zig_result['buffer'] = $zig_return ;
				$zig_result['print_view'] = true ;
				break ;
			}
			case "add":
			case "search":
			{
				$zig_result['buffer'] = $zig_return ;
				break ;
			}
			case "edit":
			{
				$zig_info['revisions'] = $zig_return['revisions'] ;
				$zig_info['data_added'] = $zig_return['data_added'] ;
				break ;
			}
		}
		if($zigjax)
		{
			$zig_result['return'] = 1 ;
			$zig_result['value'] = $zig_return ;
		}
		else
		{
			$zig_info['action'] = ( isset($zig_info['action']) and $zig_info['action'] ) ? $zig_info['action'] : $action ;
			$zig_info['id'] = ($action=="add" or $action=="copy") ? $zig_return : $id ;
			$zig_result['value'] = $zig_info ;
		}

		return $zig_result ;
	}
}

?>