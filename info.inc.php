<?php
/**
 * siteLock | flatCore Modul
 * Configuration File
 *
 * increase $mod['version'] to run /install/installer.php
 */

if(FC_SOURCE == 'backend') {
	$mod_root = '../modules/';
} else {
	$mod_root = 'modules/';
}

include($mod_root.'siteLock.mod/lang/en.php');

if(is_file($mod_root.'siteLock.mod/lang/'.$languagePack.'.php')) {
	include($mod_root.'siteLock.mod/lang/'.$languagePack.'.php');
}

$mod['name'] 					= "siteLock";
$mod['version'] 			= "0.4.1";
$mod['author']				= "flatCore DevTeam";
$mod['description']		= "Password Protection for pages";
$mod['database']			= "content/SQLite/siteLock.sqlite3";


$modnav[] = array('link' => $mod_lang['nav_overview'], 'title' => $mod_lang['nav_overview_title'], 'file' => "start");
$modnav[] = array('link' => $mod_lang['nav_prefs'], 'title' => $mod_lang['nav_prefs_title'], 'file' => "prefs");

?>