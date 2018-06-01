<div class="dynamic-tab-container">
	<a href="admin">Administrate</a> &gt; <a href="admin/roles">Roles and access levels</a>
	
	<div class="top">
		<h1>{if $role->inDataSource}Edit role &ldquo;{$role->name|escape}&rdquo;{else}Add new role{/if}.</h1>
	</div>

{if $role->inDataSource && !$role->isSystemRole}
	<p class="opportunity">
		You are editing an internal role. This is probably a bad idea. I recommend you <a href="admin/roles">cancel</a>.
	</p>
{/if}

	<form method="post" action="{$smarty.server.REQUEST_URI|escape}" class="form-horizontal">
		<div class="dynamic-tab-panel" title="About">
			<div class="form-group">
				<div class="col-xs-3">
					<label for="role-name">Name</label>
					<input type="text" class="form-control" id="role-name" name="name" value="{$role->name|escape}">
					{foreach item="error" from=$errorMap.name}<div class="error">{$error}</div>{/foreach}
				</div>
			</div>

			<div class="form-group">
				<div class="col-xs-6">
					<label for="role-description">Description</label>
					<textarea class="text-field form-control" id="role-description" name="description" rows="2" cols="64">{$role->description|escape}</textarea>
					{foreach item="error" from=$errorMap.description}<div class="error">{$error}</div>{/foreach}
				</div>
			</div>

			<div class="form-group">
				<div class="col-xs-12">
					<label for="role-is-system-role">
					<input type="checkbox" id="role-is-system-role" name="isSystemRole" {if !$role->inDataSource || $role->isSystemRole}checked="checked"{/if}>Include this role in the normal role list.</label>
			</div>
		</div>
	
		<div id="perms" class="dynamic-tab-panel" title="Permissions">
			<div class="form-group">
				<div class="col-xs-12">
					<h2>Current permissions</h2>
			
					<table class="data">
						<thead>
							<tr>
								<th scope="col">Permission</th>
								<th scope="col">Target</th>
								<th scope="col">Remove?</th>
							</tr>
						</thead>
						<tbody>
						{foreach item="entity" from=$entityList}
						{foreach item="taskName" from=$entity.permissionList}
						{assign var="shownPermission" value=true}
							<tr>
								<td><label for="task-{" "|str_replace:"_":$taskName|escape}-{$entity.id}">{$taskName|escape}</label></td>
								<td><label for="task-{" "|str_replace:"_":$taskName|escape}-{$entity.id}">{$entity.name|escape}</label></td>
								<td><label><input type="checkbox" name="task[{$taskName|escape}][{$entity.id}]" id="task-{" "|str_replace:"_":$taskName|escape}-{$entity.id}" title="Check and click save to revoke the task {$taskName|escape} {$entity.name|escape}."></label></td>
							</tr>
						{/foreach}
						{/foreach}
						{if !$shownPermission}
							<tr><td colspan="3" class="minor detail">No permissions are defined for this role, yet.</td></tr>
						{/if}
						</tbody>
					</table>
				</div>
			</div>
	
			<div class="form-group">
				<div class="col-xs-12">
				<label for="role-add-task">Add permission for {$role->name} members to </label>
				<select id="role-add-task" name="addTask">
					<option disabled="disabled" selected>choose a task</option>
				{foreach key="taskName" item="taskDescription" from=$taskDefinitionMap}
					<option value="{$taskName|escape}">{$taskName|escape}</option>
				{/foreach}
				</select>

				<label for="role-add-target">the target object</label>
				<select id="role-add-target" name="addTarget">
					<option disabled="disabled" selected>choose a target</option>
					<option value="system">System</option>
				{foreach item="accessLevel" from=$accessLevelList}
					<option value="{$accessLevel->id}">{$accessLevel->name|escape}</option>
				{/foreach}
				</select>.

				</div>
				<div class="commands col-xs-12">
					<input type="submit" class="btn btn-default" name="command[apply]" value="{if $role->inDataSource}Save{else}Add{/if} and continue editing">
				</div>
			</div>
		</div>
	
		<div class="form-group">
			
		{generate_form_post_key}

			<div class="col-xs-12">
				<div class="commands">
				{if $role->inDataSource}<a href="admin/roles/{$role->id}/delete" class="btn btn-danger">Delete</a>{/if}
				<input type="submit" class="btn btn-primary" name="command[save]" value="{if $role->inDataSource}Save changes{else}Add{/if}">
				<a href="admin/roles" class="btn btn-default">Cancel</a>
				</div>
			</div>
		</div>
	</form>
</div>
