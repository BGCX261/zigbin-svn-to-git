<?php

class zig_update_database {
	function update_database($parameters,$arg1,$arg2,$arg3) {
		$newLine = "<br />" ;
		$type = "show" ;
		$filter = false ;
		$connection = false ;
		$mode = "get" ;
		if($arg1 or $arg2 or $arg3) {
			$mode = $arg1 ? $arg1 : $mode ;
			$type = $arg2 ? $arg2 : $type ;
			$connection = $arg3 ? $arg3 : $connection ;
		}
		if(is_array($parameters)) {
			$type = array_key_exists("type",$parameters) ? $parameters['type'] : $type ;
			$filter = array_key_exists("filter",$parameters) ? $parameters['filter'] : $filter ;
			$connection = array_key_exists("connection",$parameters) ? $parameters['connection'] : $connection ;
			$mode = array_key_exists("mode",$parameters) ? $parameters['mode'] : $mode ;
		}
		$filter = true ;
		$updateMessages = "" ;
		$apiUpdatesDbConfig = $this->getConfigVariables("zig-api") ;
		$applicationDirectories = zig("dbTableApplications","getApplicationDirectories") ;
		foreach($applicationDirectories as $applicationDirectory) {
			switch($applicationDirectory) {
				case "zig-api": {
					$applicationUpdatesDbConfig = $apiUpdatesDbConfig ;
					break ;
				}
				default: {
					$applicationUpdatesDbConfig = $this->getConfigVariables($applicationDirectory) ;
				}
			}
			if($applicationUpdatesDbConfig==false) {
				$updateMessages.= "-- ${applicationDirectory} up to date!<br />" ;
				continue ;
			}
			$updateSql = "" ;
			$parameters = array
			(
				"function"	=>	"db_connect",
				"host"		=>	$applicationUpdatesDbConfig['dbHost'],
				"database"	=>	$applicationUpdatesDbConfig['dbName'],
				"username"	=>	$applicationUpdatesDbConfig['dbUsername'],
				"password"	=>	$applicationUpdatesDbConfig['dbPassword']
			) ;

			$zig_adodb = zig($parameters) ;
			$connected = is_object($zig_adodb) ? $zig_adodb->IsConnected() : false ;
			$systemDbConnection = $connection ;
			switch($mode) {
				case "push": {
					$connectionHolder = $systemDbConnection ;
					$systemDbConnection = $zig_adodb ;
					$zig_adodb = $connectionHolder ;
					break ;	
				}
			}
		if($connected) {
			$sql = $filter ? "SHOW TABLE STATUS WHERE (`Comment` = '$applicationUpdatesDbConfig[dbMark]' OR `Comment` = '$apiUpdatesDbConfig[dbMark]')" : 
					"SHOW TABLE STATUS" ;
			$sql_parameters = array (
				"function"	=>	"query",
				"move"		=>	false,
				"log"		=>	false,
				"sql"		=>	$sql,
				"connection"=>	$zig_adodb
			) ;
			$result = zig($sql_parameters) ;
			if($result->RecordCount()) {
				$systems_sql = array (
					"function"	=>	"query",
					"move"		=>	false,
					"log"		=>	false,
					"connection"=>	$systemDbConnection
				) ;

				$systemsTableInfo = array() ;
				$systems_sql['sql'] = $filter ? 
										"SHOW TABLE STATUS WHERE (`Comment` = '$applicationUpdatesDbConfig[dbMark]' OR `Comment` = '$apiUpdatesDbConfig[dbMark]')" : 
										"SHOW TABLE STATUS" ;
				$systems_table_result = zig($systems_sql) ;
				while($systems_table_fetch=$systems_table_result->fetchRow()) {
					$systemsTableInfo[$systems_table_fetch['Name']] = $systems_table_fetch ;
				}
				// -- start structure update
				while($fetch=$result->fetchRow()) {
					set_time_limit(20) ;
					$originColumnsInfo = $destinationColumnsInfo = array() ;
					$table_name = $fetch['Name'] ;
					$sql_parameters['sql'] = "SHOW FULL COLUMNS FROM `${table_name}`" ;
					$destinationColumnsResult = zig($sql_parameters) ;
					if(in_array($table_name,array_keys($systemsTableInfo))) {
						while($destinationColumnsFetch=$destinationColumnsResult->fetchRow()) {
							$originColumnsInfo[$destinationColumnsFetch['Field']] = $destinationColumnsFetch ;
						}
						$systems_sql['sql'] = "SHOW FULL COLUMNS FROM `${table_name}`" ;
						$systems_columns_result = zig($systems_sql) ;
						while($systems_columns_fetch=$systems_columns_result->fetchRow()) {
							$destinationColumnsInfo[$systems_columns_fetch['Field']] = $systems_columns_fetch ;
						}
						// -- start update table
						foreach($originColumnsInfo as $updates_columns_fetch) {
							if(in_array($updates_columns_fetch['Field'],array_keys($destinationColumnsInfo))) {
								$differ = false ;
								if($updates_columns_fetch['Field']<>$destinationColumnsInfo[$updates_columns_fetch['Field']]['Field']) {
									$differ = true ;
								}
								else if($updates_columns_fetch['Type']<>$destinationColumnsInfo[$updates_columns_fetch['Field']]['Type']) {
									$differ = true ;										
								}
								else if($updates_columns_fetch['Collation']<>$destinationColumnsInfo[$updates_columns_fetch['Field']]['Collation']) {
									$differ = true ;										
								}
								else if($updates_columns_fetch['Null']<>$destinationColumnsInfo[$updates_columns_fetch['Field']]['Null']) {
									$differ = true ;										
								}
								else if($updates_columns_fetch['Default']<>$destinationColumnsInfo[$updates_columns_fetch['Field']]['Default']) {
									$differ = true ;										
								}
								else if($updates_columns_fetch['Extra']<>$destinationColumnsInfo[$updates_columns_fetch['Field']]['Extra']) {
									$differ = true ;										
								}
								else if($updates_columns_fetch['Comment']<>$destinationColumnsInfo[$updates_columns_fetch['Field']]['Comment']) {
									$differ = true ;										
								}

								if($differ) {
									$old_field = $updates_columns_fetch['Field'] ;
								}
							}
							else {
								$alter_type = "ADD" ;
							}

							if(isset($old_field) or isset($alter_type)) {
								$alter_type = isset($alter_type) ? $alter_type : "CHANGE" ;
								$old_field = ($alter_type=="CHANGE" and isset($old_field)) ? "`".$old_field."`" : NULL ;
								$alter_table = "ALTER TABLE `${table_name}` ${alter_type} ${old_field} " ;
								$alter_table.= zig("describe_column",$updates_columns_fetch) ;
								$alter_table.= ($previous_field and $alter_type=="ADD") ? " AFTER `${previous_field}` " : NULL ;
								$updateSql.= $alter_table.";${newLine}" ;
							}
							$previous_field = $updates_columns_fetch['Field'] ;
							unset($old_field,$alter_type) ;
						}
						switch($mode) {
							case "push": {
								//$updateSql.= $this->alterIndex("alterIndex",$table_name,$systems_sql,$sql_parameters,$newLine) ;
								$updateSql.= $this->alterIndex("alterIndex",$table_name,$sql_parameters,$systems_sql,$newLine) ;
								break ;
							}
							default: {
								$updateSql.= $this->alterIndex("alterIndex",$table_name,$sql_parameters,$systems_sql,$newLine) ;
							}
						}
						// -- start auto index update
						if($systemsTableInfo[$table_name]['Auto_increment']<1000) {
							$updateSql.= $alter_table = "ALTER TABLE `${table_name}` AUTO_INCREMENT=1001;${newLine}" ;
						}
						// -- end auto index update

						// -- end update table
						switch($table_name) {
							case "zig_configs": {
								$key = "CONCAT(`module`,`tab`,`action`,`users`,`name`,`value`)" ;
								break ;
							}
							case "zig_relationships": {
								$key = "CONCAT(`parent_table`,`fieldset`)" ;
								break ;
							}
							case "zig_fields": {
								$key = "CONCAT(`table_name`,`field`)" ;
								break ;
							}
							case "zig_field_hashed_variables": {
								$key = "CONCAT(`variable`)" ;
								break ;
							}
							case "zig_reports": {
								$key = "CONCAT(`application`,`report_name`)" ;
								break ;
							}
							case "zig_report_filters": {
								$key = "CONCAT(`report_name`,`filter_name`)" ;
								break ;
							}
							case "zig_tabs": {
								$key = "CONCAT(`module`,`name`)" ;
								break ;
							}
							default: {
								$key = $this->getUniqueKey("getUniqueKey",$table_name,$sql_parameters) ;
								switch($key<>"") {
									case false: {
										switch(array_key_exists("name",$destinationColumnsInfo)) {
											case true: {
												$key = "name" ;
												break ;
											}
											default: {
												$key = false ;
											}
										}
										break ;
									}
								}
								break ;
							}
						}
					}
					else {
						$sql_parameters['sql'] = "SHOW TABLE STATUS WHERE `Name` = '${table_name}'" ;
						$commentResult = zig($sql_parameters) ;
						$commentFetch = $commentResult->fetchRow() ;
						switch($commentFetch['Comment']==$applicationUpdatesDbConfig['dbMark']) {
							case false: {
								$sql_parameters['sql'] = "SELECT `id` FROM `${table_name}` WHERE `zig_user` = '$applicationUpdatesDbConfig[dbMark]' LIMIT 1" ;
								$applicationRecordResult = zig($sql_parameters) ;
								if(!$applicationRecordResult->RecordCount()) {
									break ;
								}
							}
							default: {
								// -- start create table
								$add_table = "" ;
								$createKeys = $this->createKeys("createKey",$table_name,$sql_parameters,$newLine) ;
								while($updates_columns_fetch=$destinationColumnsResult->fetchRow()) {
									$add_table.= $add_table ? ", ".$newLine.zig("describe_column",$updates_columns_fetch) : 
										$newLine.zig("describe_column",$updates_columns_fetch) ;
									$originColumnsInfo[$updates_columns_fetch['Field']] = $updates_columns_fetch ;
								}
								$updateSql.= $add_table ? "CREATE TABLE IF NOT EXISTS `${table_name}` ( ${add_table}${createKeys} ) 
															ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='$fetch[Comment]' AUTO_INCREMENT=1001 ;${newLine}" : "" ;
								$updateSql.= $this->insertTableRecordsSql($sql_parameters,$originColumnsInfo,$applicationUpdatesDbConfig['dbMark'],$table_name,$newLine) ;
								// -- end create table
							}
						}
						$key = false ;
					}

					// -- start value update
					if($key) {
						$sql_parameters['sql'] = "SELECT ${key} AS `zigTableIndex`,`a`.* FROM `${table_name}` `a` 
													WHERE `zig_user`='$applicationUpdatesDbConfig[dbMark]'" ;
						$updates_values_result = zig($sql_parameters) ;
						if($updates_values_result->RecordCount()) {
							$systems_sql['sql'] = "SELECT ${key} AS `zigTableIndex`,`a`.* FROM `${table_name}` `a` 
													WHERE `zig_user`='$applicationUpdatesDbConfig[dbMark]'" ;
							$system_value_result = zig($systems_sql) ;
							$systemValueArray = array() ;
							while($system_value_fetch=$system_value_result->fetchRow()) {
								//$systemKeyValue[] = $system_value_fetch[$key] ;
								$systemValueArray[$system_value_fetch['zigTableIndex']] = $system_value_fetch ;
							}
							while($updates_values_fetch=$updates_values_result->fetchRow()) {
								$valueAction = $updates_fields = $updates_values = "" ;
								switch(in_array($updates_values_fetch['zigTableIndex'],array_keys($systemValueArray))) {
									case true: {
										$matched = true ;
										foreach($systemValueArray as $systemKey => $systemValue) {
											if($updates_values_fetch['zigTableIndex']==$systemKey)
											{
												foreach($originColumnsInfo as $updates_fields_fetch)
												{
													if($updates_fields_fetch['Field']<>"id" and $updates_fields_fetch['Field']<>"zig_version" and $updates_fields_fetch['Field']<>"zig_created" and $updates_fields_fetch['Field']<>"zig_updated" and $updates_values_fetch[$updates_fields_fetch['Field']]<>"")
													{
														if($systemValue[$updates_fields_fetch['Field']]<>$updates_values_fetch[$updates_fields_fetch['Field']]) {
															$matched = false ;
															break ;
														}
													}
												}
											}
											if(!$matched) {
												break ;
											}
										}
										switch($matched) {
											case false: {
												$valueAction = "update" ;
											}
										}
										break ;
									}
									default: {
										$valueAction = "add" ;
									}
								}
								switch($valueAction) {
									case "add": {
										$updateSql.= $this->insertRecordSql($originColumnsInfo,$updates_values_fetch,$table_name,$newLine) ;
										break ;
									}
									case "update": {
										foreach($originColumnsInfo as $updates_fields_fetch) {
											if($updates_fields_fetch['Field']<>"id" and $updates_fields_fetch['Field']<>"zig_version" and 
												$updates_fields_fetch['Field']<>"zig_created" and $updates_fields_fetch['Field']<>"zig_updated" and 
												$updates_fields_fetch['Field']<>"") {
												if($systemValueArray[$updates_values_fetch['zigTableIndex']][$updates_fields_fetch['Field']]<>$updates_values_fetch[$updates_fields_fetch['Field']]) {
													$updates_values.= $updates_values ? "," : "" ;
													$updates_values.= "`".$updates_fields_fetch['Field']."`='".addslashes($updates_values_fetch[$updates_fields_fetch['Field']])."'" ;
												}
											}
										}
										switch($updates_values<>"") {
											case true: {
												$updateSql.= "UPDATE `${table_name}` SET ${updates_values} WHERE `id`='".$systemValueArray[$updates_values_fetch['zigTableIndex']]['id']."' LIMIT 1 ;${newLine}" ;
											}
										}
										break ;
									}
								} // -- end of switch

							} // -- end while loop
						} // -- end if condition if there is default values
					} // -- end if there is a key
					// -- end value update
					unset($destinationColumnsInfo,$originColumnsInfo) ;
				} // -- end loop on table

				switch($updateSql<>"") {
					case true: {
						switch($type) {
							case "both":
							case "show": {
								$updateMessages.= $updateSql."-- ${applicationDirectory} update successful and complete!${newLine}" ;
								//$updateMessages.= str_replace(";",";<br />",$updateSql)."-- ${applicationDirectory} update successful and complete!<br />" ;
								if($type=="show") {
									break ;
								}
							}
							case "commit": {
								if($systemDbConnection) {
									$this->runSql($updateSql,$systems_sql,$newLine) ;
								}
								else {
									$this->runSql($updateSql,array("funtion"=>"query"),$newLine) ;
								}
							}
						}
					}
					default: {
						$updateMessages.= "-- ${applicationDirectory} up to date!<br />" ;
					}
				}

			} // -- end if condition if there is a zig table
		} // -- end if condition if is connected to db
		else {
			$updateMessages.= "Check if '$applicationUpdatesDbConfig[dbName]' database exists & '$applicationUpdatesDbConfig[dbUsername]' 
								user does have privilege on this database<br />" ;
		}

		} // -- end of application loop
		switch($updateMessages<>"") {
			case true: {
				switch($type) {
					case "both":
					case "show": {
						print $updateMessages ;
					}
				}
				break ;
			}
			default: {
				print "Check Update Configuration Files" ;
			}
		}
	}
	// -- end of main function

	function runSql($sqlStatements,$sqlParameters,$newLine) {
		$splittedSqlStatements = explode($newLine,$sqlStatements) ;
		foreach($splittedSqlStatements as $fileLine) {
			if(substr($fileLine,0,2)<>"--" and $fileLine<>"") {
				$sqlParameters['sql'] = $fileLine ;
				zig($sqlParameters) ;
			}
		}
	}

	function getUniqueKey($parameters,$arg1='',$arg2='',$arg3='') {
		if($arg1 or $arg2 or $arg3) {
			$tableName = $arg1 ;
			$originDatabase = $arg2 ;
		}
		$originDatabase['sql'] = "SHOW INDEX FROM ${tableName}" ;
		$result = zig($originDatabase) ;
		$indexes = array() ;
		$key = "" ;

		while($fetch=$result->fetchRow()) {
			if($fetch['Key_name']=="PRIMARY") {
				continue ;
			}
			switch($fetch['Non_unique']) {
				case 0: {
					$indexes[] = $fetch ;
				}
			}
		}

		foreach($indexes as $index) {
			switch($indexes[0]['Key_name']==$index['Key_name']) {
				case true: {
					$key.= $key=="" ? "`".$index['Column_name']."`" : ",`".$index['Column_name']."`" ;
				}
			}
		}
		switch(count($indexes)>1) {
			case true: {
				$key = "CONCAT(${key})" ;
				break ;
			}
		}
		return $key=="" ? false : $key ;
	}

	function createKeys($parameters,$arg1='',$arg2='',$arg3='') {
		$createKeys = $previousKey = "" ;
		$keysArray = array() ;
		if($arg1 or $arg2 or $arg3) {
			$tableName = $arg1 ;
			$originDatabase = $arg2 ;
			$newLine = $arg3 ;
		}
		$originDatabase['sql'] = "SHOW INDEX FROM ${tableName}" ;
		$result = zig($originDatabase) ;
		while($fetch=$result->fetchRow()) {
			$keysArray[] = $fetch ;
		}
		foreach($keysArray as $keyInfo) {
			if($keyInfo['Key_name']==$previousKey) {
				continue ;
			}
			$currentKey = "" ;
			$previousKey = $keyInfo['Key_name'] ;
			$keyColumns = $this->buildKeyColumns("buildKeyColumns",$keysArray,$keyInfo['Key_name']) ;
			switch($keyInfo['Key_name']) {
				case "PRIMARY": {
						$currentKey = "PRIMARY KEY (${keyColumns})" ;
					break ;
				}
				default: {
					switch($keyInfo['Non_unique']) {
						case 0: {
							$currentKey = "UNIQUE KEY `$keyInfo[Key_name]` (${keyColumns})" ;
							break ;
						}
						default: {
							$currentKey = "KEY `$keyInfo[Key_name]` (${keyColumns})" ;
						}
					}
				}
			}
			$createKeys.= ", ".$newLine.$currentKey ;
		}
		return $createKeys.$newLine ;
	}

	function buildKeyColumns($parameters,$arg1='',$arg2='',$arg3='') {
		$keysArray = $arg1 ;
		$keyName = $arg2 ;
		$keyColumns = "" ;
		foreach($keysArray as $keyInfo) {
			switch($keyInfo['Key_name']==$keyName) {
				case true: {
					$keyColumns.= $keyColumns ? ", `".$keyInfo['Column_name']."`" : "`".$keyInfo['Column_name']."`" ;
				}
			}
		}
		return $keyColumns ;
	}

	function alterIndex($parameters,$arg1,$arg2,$arg3,$newLine) {
		if($arg1 or $arg2 or $arg3) {
			$tableName = $arg1 ;
			$originDatabase = $arg2 ;
			$destinationDatabase = $arg3 ;
		}
		$originDatabase['sql'] = $destinationDatabase['sql'] = "SHOW INDEX FROM ${tableName}" ;
		$result = zig($originDatabase) ;
		$originDatabaseIndexes = array() ;
		$buffer = "" ;

		// -- start save indexes
		while($fetch=$result->fetchRow()) {
			$originDatabaseIndexes[$fetch['Key_name']."--".$fetch['Column_name']] = $fetch ;
		}
		$result = zig($destinationDatabase) ;
		$destinationDatabaseIndexes = array() ;
		while($fetch=$result->fetchRow()) {
			// -- start drop duplicates
			switch(array_key_exists($fetch['Key_name']."--".$fetch['Column_name'],$destinationDatabaseIndexes)) {
				case true: {
					$buffer.= "ALTER TABLE `${tableName}` DROP INDEX `$value[Key_name]` ;${newLine}" ;
				}
			}
			// -- end drop duplicates
			$destinationDatabaseIndexes[$fetch['Key_name']."--".$fetch['Column_name']] = $fetch ;
		}
		// -- end save indexes

		$addIndex = array() ;
		for($counter=0;$counter<1;$counter++) {
			switch($counter) {
				case 1: {
					// -- start swap
					$originHolder = $destinationDatabaseIndexes ;
					$destinationHolder = $originDatabaseIndexes ;
					break ;
					// --  end swap
				}
				default: {
					$originHolder = $originDatabaseIndexes ;
					$destinationHolder = $destinationDatabaseIndexes ;
				}
			}
			foreach($originHolder as $key => $value) {
				$alterTable = false ;
				switch(array_key_exists($key,$destinationHolder)) {
					case true: {
						switch($destinationHolder[$key]['Non_unique']!=$value['Non_unique']) {
							case true: {
								//$buffer.= "ALTER TABLE `${tableName}` DROP INDEX `$value[Key_name]` ;${newLine}" ;
								$alterTable = true ;
							}
						}
						break ;
					}
					default: {
						$alterTable = true ;
					}
				}
				switch($alterTable) {
					case true: {
						switch(in_array($value['Key_name'],$addIndex)) {
							case false: {
								$addIndex[] = $value['Key_name'] ;
							}
						}
					}
				}
			}
		}

		foreach($addIndex as $keyName) {
			// -- start build column names
			$columnNames = "" ;
			foreach($originDatabaseIndexes as $value) {
				switch($keyName==$value['Key_name']) {
					case true: {
						$nonUnique = $value['Non_unique'] ;
						$columnNames.= $columnNames ? ",`$value[Column_name]`" : "`$value[Column_name]`" ; 
					}
				}
			}
			// -- end build column names

			// -- start build drop index if needed
			foreach($destinationDatabaseIndexes as $value) {
				if($keyName==$value['Key_name']) {
					$buffer.= "ALTER TABLE `${tableName}` DROP INDEX `${keyName}` ;${newLine}" ;
					break ;
				}
			}
			// -- end build drop index if needed

			if($columnNames<>"") {
				switch($keyName) {
					case "PRIMARY": {
						$buffer.= "ALTER TABLE `${tableName}` ADD PRIMARY KEY ( $columnNames ) ;$newLine}" ;
						break ;
					}
					default: {
						switch($nonUnique) {
							case 0: {
								$buffer.= "ALTER TABLE `${tableName}` ADD UNIQUE `${keyName}` ( ${columnNames} ) ;${newLine}" ;
								break ;
							}
							default: {
								$buffer.= "ALTER TABLE `${tableName}` ADD INDEX `${keyName}` ( ${columnNames} ) ;${newLine}" ;
							}
						}
					}
				}
			}
		}
		return $buffer ;
	}

	function getConfigVariables($applicationDirectory) {
		$filesPath = zig("config","files path") ;
		if(zig("cache","file_exists",$filesPath.$applicationDirectory."/configs/".$_SERVER['HTTP_HOST']."/updates.configs.php")) {
			require_once($filesPath.$applicationDirectory."/configs/".$_SERVER['HTTP_HOST']."/updates.configs.php") ;
		}
		else if(zig("cache","file_exists",$filesPath.$applicationDirectory."/configs/default/updates.configs.php")) {
			require_once($filesPath.$applicationDirectory."/configs/default/updates.configs.php") ;
		}
		else if(zig("cache","file_exists","../${applicationDirectory}/configs/default/updates.configs.php")) {
			require("../${applicationDirectory}/configs/default/updates.configs.php") ;
		}
		else {
			$updatesDbConfig = false ;
		}
		return $updatesDbConfig ;
	}

	function insertTableRecordsSql($sql_parameters,$originColumnsInfo,$dbMark,$table_name,$newLine) {
		$insertSql = "" ;
		$sql_parameters['sql'] = "SELECT * FROM `${table_name}` WHERE `zig_user` = '${dbMark}'" ;
		$result = zig($sql_parameters) ;
		while($updates_values_fetch=$result->fetchRow()) {
			$insertSql.= $this->insertRecordSql($originColumnsInfo,$updates_values_fetch,$table_name,$newLine) ;
		}
		return $insertSql ;
	}

	function insertRecordSql($originColumnsInfo,$updates_values_fetch,$table_name,$newLine) {
		$insertFields = $insertValues = "" ;
		foreach($originColumnsInfo as $updates_fields_fetch) {
			if($updates_fields_fetch['Field']<>"id" and $updates_fields_fetch['Field']<>"zig_version" and 
				$updates_fields_fetch['Field']<>"zig_updated" and $updates_values_fetch[$updates_fields_fetch['Field']]<>"") {
				$insertFields.= $insertFields ? ",`".$updates_fields_fetch['Field']."`" : "`".$updates_fields_fetch['Field']."`" ;
				switch($updates_fields_fetch['Field']) {
					case "zig_created": {
						$insertValues.= "NOW()" ;
						break ;
					}
					case "zig_user": {
						$insertValues.= ",'".$updates_values_fetch[$updates_fields_fetch['Field']]."'" ;
						break ;
					}
					default: {
						$insertValues.= ",'".addslashes($updates_values_fetch[$updates_fields_fetch['Field']])."'" ;
						break ;
					}
				}
			}
		}
		return "INSERT INTO ${table_name}(${insertFields}) VALUES(${insertValues}) ;${newLine}" ;
	}
}

?>