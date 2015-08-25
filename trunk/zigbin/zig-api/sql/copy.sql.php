<?php

class zig_copy
{
	function copy($parameters,$arg1='',$arg2='',$arg3='')
	{
		if($arg1 or $arg2 or $arg3)
		{
			$table = $arg1 ;
			$defaults = $arg2 ;
			$id = $arg3 ;
		}
		else
		{
			$table = $parameters['table'] ;
			$id = $parameters['id'] ;
			$defaults = $parameters['defaults'] ;
			$exclude = $parameters['exclude'] ;
			$permissions = $parameters['permissions'] ;
		}
		
		$pre = zig("config","pre") ;
		$zig_global_database = zig("config","global_database") ;
		$zig_save = isset($_GET['zig_save']) ? $_GET['zig_save'] : (isset($_POST['zig_save']) ? $_POST['zig_save'] : '') ;

		// Start remove the database name on the table
		$semi_stripped_table = str_replace($zig_global_database.".","",$table) ;
		// End remove the database name on the table

		// Start stripped table name
		$stripped_table = str_replace($pre,"",$semi_stripped_table) ;
		// End stripped table name

		$sql = "SELECT * FROM `$zig_global_database`.`${pre}relationships` WHERE (`parent_table`='$table' OR `parent_table`='$semi_stripped_table' OR `parent_table`='$stripped_table' OR `parent_table`='all tables') AND `child_table`<>'' AND `zig_status`<>'deleted' ORDER BY `zig_weight`,`fieldset`,`child_table`" ;
		$field_result = zig("query",$sql) ;

		if($zig_save)
		{
			$save_parameters = array
			(
				'function'		=>	'save',
				'table'			=>	$table,
				'exclude'		=>	$exclude,
				'method'		=>	'parent',
//				'id'			=>	$id,
				'mode'			=>	'copy',
			) ;
			$zig_copy_result = zig($save_parameters) ;
			$zig_result['message'] = $zig_copy_result['message'] ;
			$zig_result['validation'] = $zig_copy_result['validation'] ;

			if($zig_result['validation'])
			{
				while($field_fetch=$field_result->fetchRow())
				{
					$field_fetch['child_table'] = str_replace($zig_global_database.".","",$field_fetch['child_table']) ;
					$field_fetch['child_table'] = str_replace($pre,"",$field_fetch['child_table']) ;
					$field_fetch['child_table'] = $zig_global_database.".".$pre.$field_fetch['child_table'] ;

					// -- Start process child table
					$save_parameters = array
					(
						'function'			=>	'save',
						'table'				=>	$field_fetch['child_table'],
						'exclude'			=>	$exclude,
						'method'			=>	'child',
						'parent_id'			=>	$id,
						'parent_table'		=>	$table,
						'mode'				=>	'copy'
					) ;
					$zig_copy_result = zig($save_parameters) ;
					$zig_result['message'].= $zig_copy_result['message'] ? "<br />".$zig_copy_result['message'] : NULL ;
					$zig_result['validation']*= $zig_copy_result['validation'] ;
					// -- End process child table
				}
			}
			$field_result->MoveFirst() ;
		}

		$data_saved = ($zig_result['validation'] and  $zig_save) ? true : false ;

		$wizard_parameters = array
		(
			'function'		=>	'fields',
			'method'		=>	'parent',
			'mode'			=>	'copy',
			'table'			=>	$table,
			'exclude'		=>	$exclude,
			'id'			=>	$id,
			'data_saved'	=>	$data_saved,
			'permissions'	=>	$permissions
		) ;

		$buffer.= zig($wizard_parameters) ;

		// -- Start process child table			
		while($field_fetch=$field_result->fetchRow())
		{

			$field_fetch['child_table'] = str_replace($zig_global_database.".","",$field_fetch['child_table']) ;
			$field_fetch['child_table'] = str_replace($pre,"",$field_fetch['child_table']) ;
			$field_fetch['child_table'] = $zig_global_database.".".$pre.$field_fetch['child_table'] ;

			$wizard_parameters = array
			(
				'function'		=>	'fields',
				'method'		=>	'child',
				'mode'			=>	'copy',
				'table'			=>	$field_fetch['child_table'],
				'parent_table'	=>	$table,
				'exclude'		=>	$exclude,
				'parent_id'		=>	$id,
				'data_saved'	=>	$data_saved,
				'permissions'	=>	$permissions
			) ;
		
			$buffer.= zig($wizard_parameters) ;
		}
		// -- End process child table
		
		
		$template = zig("template","file","view") ;
		$buffer.= zig("trigger","copy",$id) ;
		$buffer = str_replace("{view}",$buffer,$template) ;
		$buffer = strpos($buffer," type='file' ") ? str_replace("{enctype}","enctype='multipart/form-data'",$buffer) : str_replace("{enctype}","enctype='multipart/form-data'",$buffer) ;
		$zig_result['buffer'] = $buffer ;
		return $zig_result ;
	}

}

?>