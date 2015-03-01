<?php

/**
 * modul:		siteLock
 * version: 0.2
 * licence:	GPL 
 */


include("info.inc.php");
parse_str($page_modul_query);
$mod_db = $mod['database'];
$mod_tpl = 'default';
$show_contents = false;

if(isset($_GET['logout'])) {
	unset($_SESSION['siteLock']);
}

if(is_file("$mod_db")) {
	
	//$lock is set by $page_modul_query (lock={integer})
	
	$lock = (int) $lock;
	$mod_db = $mod['database'];
	$dbh = new PDO("sqlite:$mod_db");
	$sql = "SELECT siteLock_psw, siteLock_editdate FROM entries WHERE siteLock_id = '$lock' ";
	
	$sql_prefs = "SELECT * FROM prefs WHERE prefs_status = 'active' ";
	$sl_prefs = $dbh->query($sql_prefs)->fetch();

	$siteLock = $dbh->query($sql);
	$siteLock = $siteLock->fetch(PDO::FETCH_ASSOC);
	
	$siteLock_psw = $siteLock['siteLock_psw'];
	$siteLock_date = $siteLock['siteLock_editdate'];
	
	if(($sl_prefs['prefs_template'] != '') || ($sl_prefs['prefs_template'] != 'default')) {
		$mod_tpl = basename($sl_prefs['prefs_template']);
	}
	
	if(isset($_POST['siteLock'])) {
		if(md5($siteLock_date.$_POST['siteLock']) == $siteLock_psw) {
			$session_str = $_SESSION['siteLock'].','.$siteLock_psw;
			$_SESSION['siteLock'] = $session_str;
		}
	}
	
	
	$form = file_get_contents('modules/siteLock.mod/templates/'.$mod_tpl.'/enter.tpl');
	$form = str_replace('{formaction}', "/$fct_slug", $form);
 
}

/* unset contents if user isn't locked in */
if(isset($_SESSION['siteLock'])) {
	$session_parts = explode(',',$_SESSION['siteLock']);
	if(in_array($siteLock_psw, $session_parts)) {
		$sl_footer = file_get_contents('modules/siteLock.mod/templates/'.$mod_tpl.'/footer.tpl');
		$sl_footer = str_replace('{href_logout}', "/$fct_slug?logout", $sl_footer);
		$modul_content = $sl_footer;
		$show_contents = true;
	} else {
		$show_contents = false;
	}

} else {
	$show_contents = false;
}

if($show_contents !== true) {
	$page_content = '';
	$page_extracontent = '';
	$modul_content = $form;
}

?>