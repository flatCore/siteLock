<?php

/* INSTALL */

if(!is_file("$mod_db")) {

	$sql_entries_table = generate_sql_query("../modules/siteLock.mod/install/tpl-entries.php");
	$sql_prefs_table = generate_sql_query("../modules/siteLock.mod/install/tpl-preferences.php");
	
	$dbh = new PDO("sqlite:$mod_db");
	
	$dbh->query($sql_entries_table);
	$dbh->query($sql_prefs_table);
	
	$sql_insert = "INSERT INTO prefs (
			prefs_id,
			prefs_status,
			prefs_version
				) VALUES (
			NULL,
			'active',
			'$mod[version]'
			)";
	
	$dbh->query($sql_insert);
	$dbh = null;
	
	@chmod("$mod_db", 0775);

}



/* CHECK DATABASE */


if(!is_file("$mod_db")) {
	echo'<div class="alert alert-error">Die Datenbank existiert nicht.</div>';
}


/* UPDATE MAYBE */


$dbh = new PDO("sqlite:$mod_db");

$sql = "SELECT * FROM prefs WHERE prefs_status = 'active' ";

$prefs = $dbh->query($sql);
$prefs = $prefs->fetch(PDO::FETCH_ASSOC);

$dbh = null;

foreach($prefs as $k => $v) {
   $$k = stripslashes($v);
}

if($prefs_version < $mod['version']) {

	echo '<p class="alert alert-info">an update is running ...</p>';

	/* build an array from all tpl-xxx.php files in folder supplies */
	$all_tables = glob("../modules/siteLock.mod/install/tpl-*.php");
	
	for($i=0;$i<count($all_tables);$i++) {
	
		unset($db_path,$table_name);
		include("$all_tables[$i]"); // returns $cols and $table_name
		
		$db_path = "$mod_db";
	
		$is_table = table_exists("$db_path","$table_name");
	
		if($is_table < 1) {
			add_table("$db_path","$table_name",$cols);
			$table_updates[] = "<div class='alert alert-info'>New Table: <b>$table_name</b> in Database <b>$database</b></div>";
		}
	
		$existing_cols = get_collumns("$db_path","$table_name");
	
		foreach ($cols as $k => $v) {
			if(!array_key_exists("$k", $existing_cols)) {
				//update_table -> column, type, table, database
				update_table("$k","$cols[$k]","$table_name","$db_path");
				$col_updates[] = "<div class='alert alert-info'>New Column: <b>$k</b> in table <b>$table_name</b></div>";	
			}  
		}
	
		/* updates are done, check all columns again */
			
		$existing_cols = get_collumns("$db_path","$table_name");
	
		foreach ($cols as $b => $x) {     
	  	if(!array_key_exists("$b", $existing_cols)) {
	  		$fails[] = "<div class='alert alert-error'>Missing Column: <b>$b</b> - table: <b>$table_name</b></div>";  	
	  	} else {
	  		$wins[] = "<div class='alert alert-success'>Column <b>$b</b> in table <b>$table_name</b> is ready</div>";
	  	}
		}
	
	
	} // EO $i


	/* increase version number in $mod_db */

	$dbh = new PDO("sqlite:$mod_db");
	$sql = "UPDATE prefs
					SET prefs_version = '$mod[version]'
					WHERE prefs_status = 'active' ";
	$cnt_changes = $dbh->exec($sql);
	$dbh = null;

}


/* echo fails and wins */

if(is_array($fails)) {
	foreach ($fails as $value) {
		echo"$value";
	}
	
}


if(is_array($wins)) {
	
	if(is_array($table_updates)) {
		foreach ($table_updates as $value) {
			echo"$value";
		}
	}
	
	if(is_array($col_updates)) {
		foreach ($col_updates as $value) {
			echo"$value";
		}
	}
	
}






/* returns all cols and types of a existung database/table */
function get_collumns($db,$table_name) {

	$dbh = new PDO("sqlite:$db");

	$result = $dbh->query("PRAGMA table_info(" . $table_name . ")");
	$result->setFetchMode(PDO::FETCH_ASSOC);
	$meta = array();
	foreach ($result as $row) {
		$meta[$row['name']] = $row['type'];
	}

	return $meta;

}



/*  check if table exists - returns the number of existing tables */
function table_exists($db,$table_name) {

	$dbh = new PDO("sqlite:$db");

  $result = $dbh->query("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='$table_name'")->fetch();
	$cnt_tables = $result[0];
	
  return $cnt_tables;
}



/* generate an sql query from templates (php files) */
function generate_sql_query($file) {

	include("$file");

	foreach ($cols as $k => $v) {
    	$string .= "$k $v,\r"; 
	}

	$string = substr(trim("$string"), 0,-1); // cut last commata and returns

	$sql_string = "
		CREATE TABLE $table_name (
		$string
	)
	";

  /* return the sql string */
  return $sql_string;
}




function update_table($col_name,$type,$table_name,$db) {

	$dbh = new PDO("sqlite:$db");
	
	$sql = "ALTER TABLE $table_name ADD $col_name $type";
	$dbh->exec($sql);
	
	$dbh = null;
	
}


function add_table($db,$table_name,$cols) {

	foreach ($cols as $k => $v) {
		$cols_string .= "$k $cols[$k],\r";
	}
	
	$cols_string = substr(trim("$cols_string"), 0,-1); // cut last commata and returns
	
	$dbh = new PDO("sqlite:$db");
	
		$sql = "CREATE TABLE $table_name (
							$cols_string
							)";
		$dbh->exec($sql);
	
	$dbh = null;

}



?>