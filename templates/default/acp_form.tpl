<form action="{form_action}" method="POST">

	<div class="form-group">
    <label for="InputDate">{label_psw}</label>
    <div class="input-group">
    	<input type="text" class="form-control" name="siteLock_psw" value="">
			<div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
    </div>
    {psw_helptext}
  </div>

	<div class="form-group">
		<label for="InputTeaser">{label_notes}</label>
		<textarea name="siteLock_notes" class="form-control" rows="4">{siteLock_notes}</textarea>
	</div>
	
	<input type="hidden" name="stored_psw" value="{stored_psw}">
	<input type="submit" name="save" value="{btn_value}" class="btn btn-success">
	<input type="hidden" name="edit_id" value="{edit_id}">
</form>

<hr>