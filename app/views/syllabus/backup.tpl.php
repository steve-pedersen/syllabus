<form action="{$smarty.server.REQUEST_URI}" method="post">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus.syllabus_id}" />

	<div class="message info">
		<p>
		Backing up a syllabus will create a text file that you can store on your computer.  This file can then later be
		used to restore a syllabus to it's previous form.  Likewise, you can restore the syllabus into a different course and
		use the backup as a starting point for your new syllabus.
		</p>
		<p>
		Would you like to create the backup file?
		</p>
		<div class="save_row">
			<input type="submit" name="command[backupSyllabus]" class="button submitButton" value="Create Backup" />
			<a href="syllabus/{$syllabus.syllabus_id}/edit" class="cancel_link">Cancel</a>
		</div>
	</div>
</form>

