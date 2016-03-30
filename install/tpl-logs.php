<?php

/**
 * siteLock Database-Scheme
 * install/update the table for entries
 * 
 */

$database = "siteLock";
$table_name = "logs";

$cols = array(
	"log_id"  => 'INTEGER NOT NULL PRIMARY KEY',
	"log_time" => 'VARCHAR',
	"log_username" => 'VARCHAR',
	"log_ip" => 'VARCHAR',
	"log_msg" => 'VARCHAR'
  );
  


 
?>