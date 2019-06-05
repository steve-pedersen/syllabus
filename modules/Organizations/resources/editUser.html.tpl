<div class="col mt-5">
	<form method="post" action="{$smarty.server.REQUEST_URI|escape}">

		<h4>Roles in this organization ({$organization->name})</h4>	
	{foreach $roles as $role => $name}
		<div class="form-check py-1">
			{if $role == 'member'}
				<input disabled name="roles[{$role}]" class="form-check-input" type="checkbox" id="{$role}" aria-describedby="{$role}HelpText" {if $organization->userHasRole($account, $role)}checked{/if}>
			{else}
				<input name="roles[{$role}]" class="form-check-input" type="checkbox" id="{$role}" aria-describedby="{$role}HelpText" {if $organization->userHasRole($account, $role)}checked{/if}>
			{/if}
			<label class="form-check-label" for="{$role}">
				{$name} - 
				<span id="{$role}HelpText" class="text-muted">
					{$organization->getRoleHelpText($role)}
				</span>
			</label>
		</div>
	{/foreach}
		
		<div class="form-group my-5">
			{generate_form_post_key}
			<button type="submit" class="command-button btn btn-primary" name="command[save]">Save Settings</button>
			<a class="btn btn-default" href="{$returnTo}">Cancel</a>
			{if $canRemove}
				<button type="submit" class="command-button btn btn-danger" name="command[remove]">Remove from organization</button>
			{/if}
		</div>
	</form>
</div>
