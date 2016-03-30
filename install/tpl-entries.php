<?php

/**
 * siteLock Database-Scheme
 * install/update the table for logs
 * 
 */

$database = "siteLock";
$table_name = "entries";

$cols = array(
	"siteLock_id"  => 'INTEGER NOT NULL PRIMARY KEY',
	"siteLock_psw" => 'VARCHAR',
	"siteLock_notes" => 'VARCHAR',
	"siteLock_entrydate" => 'VARCHAR',
	"siteLock_editdate" => 'VARCHAR'
  );
  


 
?>