<a href="admin">Administrate</a> &gt; <a href="admin/roles">Roles and access levels</a> &gt; <a href="admin/roles/{$role->id}">{$role->name|escape}</a>
<h1>Delete role &ldquo;{$role->name|escape}&rdquo;?</h1>

<form method="post" action="{$smarty.server.REQUEST_URI|escape}">
	<p class="opportunity">
		Are you sure you want to delete this role? Any members of the role will have this role and all of its
		permissions removed from their account. This will almost certainly result in users not being able to
		do things they formerly were able to do.
	</p>
	
	<p>
        {generate_form_post_key}
        
        <input class="btn btn-danger" type="submit" name="command[delete]" id="command-delete" value="Delete">
        <a href="admin/roles/{$role->id}" class="btn btn-link">Cancel</a>
    </p>
</form>
