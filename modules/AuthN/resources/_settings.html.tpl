
{if $pAdmin}
	<fieldset class="field">
		<legend>Roles</legend>
		<ul class="list-group">
{foreach item="role" from=$roleList}
			<li>
				<label for="account-role-{$role->id}">
				<input type="checkbox" name="role[{$role->id}]" id="account-role-{$role->id}" class="account-role-{$role->name}" 
				{if $account->roles->has($role)}checked aria-checked="true"{else}aria-checked="false"{/if} />
				{$role->name|escape}</label>
			</li>
{/foreach}
		</ul>
	</fieldset>
{/if}

<fieldset class="field">
	<legend>Notify user</legend>
	<label for="notify">
		<input type="checkbox" name="notify" id="notify" checked aria-checked="true" value=true />
		Notify user of account
	</label>
</fieldset>

