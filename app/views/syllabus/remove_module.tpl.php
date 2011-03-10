<h1>Remove Module</h1>

<form action="{$smarty.const.CURRENT_URL}" method="post">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus.syllabus_id}" />
    <input type="hidden" name="module_type" value="{$module_type}" />

    <div class="message warn">    
		<p>
		Removing the <strong>{$module_type}</strong> module from this syllabus will remove
		the module and delete all items created as a part of this module.  This action cannot be undone.
		</p>
		
		<p>Are you sure you want to continue?</p>
		
		<div class="save_row">
			<input type="submit" name="command[removeModule]" class="button submitButton" value="Remove Module" />
			<a href="syllabus/edit/{$syllabus.syllabus_id}" class="cancel_link">Cancel</a>
		</div>
	</div>

</form>

