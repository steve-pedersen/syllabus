<h1>{$page_header}</h1>

<form action="{$smarty.server.REQUEST_URI}" method="post">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus_id}" />
	<input type="hidden" name="module_type" value="{$module.id}" />
	<input type="hidden" name="item_id" value="{$item_id}" />


	<div class="message warn">
		<p>
		Removing this {$module.singular} will delete the item from the database.
		This action cannot be undone.
		</p>
		
		<p>Are you sure you want to continue?</p>
		
		<div class="save_row">
			<input type="submit" name="command[{$command}]" class="button" value="Remove Item" />
			<a href="{$cancel}" class="cancel_link">Cancel</a>
		</div>
	</div>

</form>

