<?php
	
if(!defined('FC_INC_DIR')) {
	die("No access");
}

echo '<h3>'.$mod_name.' '.$mod_version.' <small>| Einstellungen</small></h3>';

if(isset($_POST['saveprefs'])) {
	
	$sl_prefs_template = basename($_POST['sl_prefs_template']);
	
	$dbh = new PDO("sqlite:$mod_db");
	$sql = "UPDATE prefs
					SET prefs_template = '$sl_prefs_template'
					WHERE prefs_status = 'active' ";
					
	$cnt_changes = $dbh->exec($sql);
	
	if($cnt_changes > 0){
		$sys_message = '{OKAY} '.$lang['db_changed'];
	} else {
		$sys_message = '{ERROR} '.$lang['db_not_changed'];
	}
	
	$dbh = null;
	
	if($sys_message != ""){
		print_sysmsg("$sys_message");
	}	

}



$dbh = new PDO("sqlite:$mod_db");

$sql = "SELECT * FROM prefs WHERE prefs_status = 'active' ";

$sl_prefs = $dbh->query($sql);
$sl_prefs = $sl_prefs->fetch(PDO::FETCH_ASSOC);

$dbh = null;

foreach($sl_prefs as $k => $v) {
   $$k = stripslashes($v);
}


echo'<form action="acp.php?tn=moduls&sub=siteLock.mod&a=prefs" class="form-horizontal" method="POST">';

echo '<fieldset>';
echo '<legend>Layout</legend>';

echo '<div class="form-group">
				<label class="col-md-3 control-label">Template</label>';
				
				$tpl_folders = list_template_folders();
				
				echo '<div class="col-md-9">';
				echo '<select class="form-control" name="sl_prefs_template">';
				echo '<option value="default">Standard</option>';
				
				foreach ($tpl_folders as $tpl) {
					unset($sel);
					if($prefs_template == $tpl) {
						$sel = "selected";
					}					
					echo "<option $sel value='$tpl'>$tpl</option>";
				}
				echo '</select>';

				echo '</div>';

echo '</fieldset>';

echo '<input type="submit" class="btn btn-success" name="saveprefs" value="' . $lang['save'] . '">';

echo '</form>';



echo '<hr><pre>';
print_r($mod);
echo '</pre>';




function list_template_folders() {

	$tpl_folders = array();
	
	$directory = "../modules/siteLock.mod/templates";
	
	if(is_dir($directory)) {
	
		$all_folders = glob("$directory/*");
		
		foreach($all_folders as $v) {
			if((is_dir("$v") && $v != "$directory/default")) {
				$tpl_folders[] = basename($v);
			}
		}
	
	 }
	 
	 return $tpl_folders;
}

?>