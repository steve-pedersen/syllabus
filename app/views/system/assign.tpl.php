<h1>Assign User to Course</h1>

<form action="{$smarty.server.REQUEST_URI}" method="post">
	<input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
    
    <div class="message info">
    Use this form to assign a user a specific role in a specific course
	</div>
    
    <div class="label"><label for="enroll_user_id">User Id</label></div>
    <div class="input"><input type="text" name="enroll_user_id" id="enroll_user_id" style="width: 150px;" /></div>
    <div style="clear: both;"></div>
	
    <div class="label"><label for="enroll_class_id">Course Id</label></div>
    <div class="input"><input type="text" name="enroll_class_id" id="enroll_class_id" style="width: 150px;" /><span class="form_note" style="margin-left: 10px;">Use course SSID (eg. 20114-R-12345)</span></div>
    <div style="clear: both;"></div>
	
    <div class="label"><label for="enroll_role">Insert As</label></div>
    <div class="input">
		<select name="enroll_role" id="enroll_role" style="width: 160px;">
			<option value="instructor">Instructor</option>
			<option	value="student">Student</option>
		</select>
	</div>
    <div style="clear: both;"></div>

    <div class="save_row">
		<div class="label">&nbsp;</div>
		<div class="input">
			<input type="submit" name="command[assignUser]" value="Assign" class="button" />
		</div>			
    </div>
    
</form>