<?php
/**
 * siteLock | flatCore Modul
 * Configuration File
 *
 * increase $mod['version'] to run /install/installer.php
 */


$mod['name'] 					= "siteLock";
$mod['version'] 			= "0.3";
$mod['author']				= "flatCore DevTeam";
$mod['description']		= "Passwortschutz für Seiten";
$mod['database']			= "content/SQLite/siteLock.sqlite3";

$modnav[0]['link']		= "Übersicht";
$modnav[0]['title']		= "Alle Einträge auf einen Blick";
$modnav[0]['file']		= "start";

$modnav[1]['link']		= "Einstellungen";
$modnav[1]['title']		= "Einstellungen für dieses Modul";
$modnav[1]['file']		= "prefs";

?>