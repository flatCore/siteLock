<?php

echo '<h3>'.$mod_name.' '.$mod_version.' <small>| Übersicht</small></h3>';

include('../modules/'.$mod_name.'.mod/install/installer.php');

$dbh = new PDO("sqlite:$mod_db");

/* delete */
if(is_numeric($_REQUEST['delete'])){
	$delete = (int) $_REQUEST['delete'];
	$sql = "DELETE FROM entries WHERE siteLock_id = $delete";
	$cnt_changes = $dbh->exec($sql);

	if($cnt_changes > 0) {
		echo '<div id="alert alert-success">Eintrag wurde gelöscht</div>';
	}
} /* eo delete */


/* save/update entry */
if(isset($_POST['save'])) {

	if(!is_numeric($_POST['edit_id'])) {
		$modus = 'new';
	} else {
		$modus = 'update';
		$edit_id = (int) $_POST['edit_id'];
	}
	
	$saveTime = time();
	$siteLockSalt = $saveTime . $_POST['siteLock_psw'];
	$siteLock_psw = md5($siteLockSalt);
	$siteLock_notes = strip_tags($_POST['siteLock_notes']);
	
	$sql_new = "INSERT INTO entries (
			siteLock_id, siteLock_psw, siteLock_notes, siteLock_entrydate, siteLock_editdate
			) VALUES (
			NULL, :siteLock_psw, :siteLock_notes, :siteLock_entrydate, :siteLock_editdate ) ";

	$sql_update = "UPDATE entries
				SET	siteLock_psw = :siteLock_psw,
					siteLock_notes = :siteLock_notes,
					siteLock_editdate = :siteLock_editdate
				WHERE siteLock_id = $edit_id ";
				
	if($modus == "new")	{				
		if($sth = $dbh->prepare($sql_new)) {
			$sth->bindParam(':siteLock_psw', $siteLock_psw, PDO::PARAM_STR);
			$sth->bindParam(':siteLock_notes', $siteLock_notes, PDO::PARAM_STR);
			$sth->bindParam(':siteLock_entrydate', $saveTime, PDO::PARAM_STR);
			$sth->bindParam(':siteLock_editdate', $saveTime, PDO::PARAM_STR);
			$edit_id = $dbh->lastInsertId();
		} else {
			print_r($dbh->errorInfo());
		}
	}
	
	if($modus == "update") {
		$sth = $dbh->prepare($sql_update);
		if($_POST['siteLock_psw'] != '') {
			$sth->bindParam(':siteLock_psw', $siteLock_psw, PDO::PARAM_STR);
		} else {
			$sth->bindParam(':siteLock_psw', $_POST['stored_psw'], PDO::PARAM_STR);
		}
		$sth->bindParam(':siteLock_notes', $siteLock_notes, PDO::PARAM_STR);
		$sth->bindParam(':siteLock_editdate', $saveTime, PDO::PARAM_STR);
	}

	$cnt_changes = $sth->execute();

	if($cnt_changes == TRUE){
		$sys_message = "{OKAY} Der Eintrag wurde gespeichert";
	} else {
		$sys_message = "{error} Der Eintrag wurde nicht gespeichert";
		echo '<hr><pre>';
		print_r($dbh->errorInfo());
		echo '</pre><hr>';
	}
	
	print_sysmsg("$sys_message");
	
}


$sql = "SELECT * FROM entries";

foreach ($dbh->query($sql) as $row) {
 $siteLocks[] = $row;
}

$dbh = null;

if(!is_numeric($_REQUEST['edit_id'])) {
	$btn_value = 'Speichern';
	$lockPSW = '';
	$lockNotes = '';
	$psw_helptext = '';
} else {
	$psw_helptext = '<span class="help-block text-danger">Nur ausfüllen, wenn das Passwort geändert werden soll.</span>';
	$btn_value = 'Aktualisieren';
	$edit_id = (int) $_REQUEST['edit_id'];
	$dbh = new PDO("sqlite:$mod_db");
	$sql = "SELECT * FROM entries WHERE siteLock_id = $edit_id";
	$get_lock = $dbh->query($sql);
	$get_lock = $get_lock->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	foreach($get_lock as $k => $v) {
	   $$k = stripslashes($v);
	}
	
	$siteLock_date = date("Y-m-d",$siteLock_date);

}


/* form */

$tplform = file_get_contents("../modules/siteLock.mod/templates/default/acp_form.tpl");
$tplform = str_replace('{form_action}', "acp.php?tn=moduls&sub=siteLock.mod&a=start", $tplform);
$tplform = str_replace('{btn_value}', $btn_value, $tplform);
$tplform = str_replace('{siteLock_psw}', $siteLock_psw, $tplform);
$tplform = str_replace('{stored_psw}', $siteLock_psw, $tplform);
$tplform = str_replace('{siteLock_notes}', $siteLock_notes, $tplform);
$tplform = str_replace('{label_psw}', 'Passwort', $tplform);
$tplform = str_replace('{psw_helptext}', $psw_helptext, $tplform);
$tplform = str_replace('{label_notes}', 'Notizen', $tplform);
$tplform = str_replace('{edit_id}', $edit_id, $tplform);

echo $tplform;





/* list siteLock entries */

$cnt_siteLocks = count($siteLocks);

$tplfile = file_get_contents("../modules/siteLock.mod/templates/default/acp_list.tpl");

echo '<table class="table table-condensed">';

echo '<thead><tr>';
echo '<th style="width:200px;">Datum</th><th>Notes</th><th style="width:200px;"></th>';
echo '</tr></thead>';

if($cnt_siteLocks < 1) {
	echo '<td>' . date("Y-m-d",time()) . '</td><td colspan="2"><p class="alert alert-info">Keine Einträge vorhanden.</p></td>';
}

for($i=0;$i<$cnt_siteLocks;$i++) {

	$item_id = $siteLocks[$i]['siteLock_id'];
	$item_entrydate = date("Y-m-d",$siteLocks[$i]['siteLock_entrydate']);
	$item_editdate = date("Y-m-d",$siteLocks[$i]['siteLock_editdate']);
	$item_notes = stripslashes($siteLocks[$i]['siteLock_notes']);
	$item_modul_query = 'lock='.$item_id;
	
	$link_edit = "<a class='btn btn-default btn-sm' href='$_SERVER[PHP_SELF]?tn=moduls&sub=siteLock.mod&a=start&edit_id=$item_id'>Bearbeiten</a>";
	$link_delete = "<a class='btn btn-danger btn-sm' href='$_SERVER[PHP_SELF]?tn=moduls&sub=siteLock.mod&a=start&delete=$item_id' onclick=\"return confirm('$lang[confirm_delete_data]')\">Löschen</a>";
	
	$tpl = $tplfile;
	$tpl = str_replace("{item_notes}", $item_notes, $tpl);
	$tpl = str_replace("{item_entrydate}", $item_entrydate, $tpl);
	$tpl = str_replace("{item_editdate}", $item_editdate, $tpl);
	$tpl = str_replace("{modul_query}", $item_modul_query, $tpl);
	$tpl = str_replace("{btn_edit}", "$link_edit", $tpl);
	$tpl = str_replace("{btn_delete}", "$link_delete", $tpl);
	
	echo "$tpl";

}

echo '</table>';

?>