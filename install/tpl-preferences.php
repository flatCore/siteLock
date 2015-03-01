<?php

/**
 * siteLock Database-Scheme
 * install/update the table for preferences
 * 
 */

$database = "siteLock";
$table_name = "prefs";

$cols = array(
	"prefs_id"  => 'INTEGER NOT NULL PRIMARY KEY',
	"prefs_template" => 'VARCHAR',
	"prefs_status"  => 'VARCHAR',
	"prefs_version" => 'VARCHAR'
  );
  
  
 
?>
