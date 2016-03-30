<?php
	
/**
 * log user
 * timestring, username, ip adress, message
 */

function sl_log() {
	
	global $fct_slug;
	global $mod_db;
	
	$time = time();
	$username = $_SESSION['user_nick'];
	$ip = $_SERVER['REMOTE_ADDR'];
	$msg = 'Page: '. $fct_slug;
	
	

	$dbh = new PDO("sqlite:$mod_db");
	$sql = "INSERT INTO logs (
        log_id, log_time, log_username, log_ip, log_msg
        ) VALUES (
        NULL, :log_time, :log_username, :log_ip, :log_msg ) ";
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':log_time', $time, PDO::PARAM_STR);
	$sth->bindValue(':log_username', $username, PDO::PARAM_STR);
	$sth->bindValue(':log_ip', $ip, PDO::PARAM_STR);
	$sth->bindValue(':log_msg', $msg, PDO::PARAM_STR);
	
	$cnt_changes = $sth->execute();
	$dbh = null;
	
	sl_clear_log();
	
}

function sl_clear_log() {
	
	global $mod_db;
	$dbh = new PDO("sqlite:$mod_db");
	$interval = time() - (30 * 86400); // 30 days
	$count = $dbh->exec("DELETE FROM logs WHERE log_time < '$interval'");
	$dbh = null;
	
}


?>