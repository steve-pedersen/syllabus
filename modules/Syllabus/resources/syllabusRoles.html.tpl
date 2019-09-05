<div class="container-fluid">
	<div class="top">
		<h1>{if $role->inDataSource}Edit role &ldquo;{$role->name|escape}&rdquo;{else}Add new role{/if}</h1>
	</div>

	<form method="post" action="{$smarty.server.REQUEST_URI|escape}" class="form-horizontal">
		<div class="dynamic-tab-panel" title="About">
			<div class="form-group">
				<div class="col-xs-3">
					<label for="role-name">Name</label>
					<input type="text" class="form-control" id="role-name" name="name" value="{$role->name|escape}">
					{foreach $errorMap.name as $error}<div class="error">{$error}</div>{/foreach}
				</div>
			</div>

			<div class="form-group">
				<div class="col-xs-6">
					<label for="role-description">Description</label>
					<textarea class="text-field form-control" id="role-description" name="description" rows="2" cols="64">{$role->description|escape}</textarea>
					{foreach $errorMap.description as $error}<div class="error">{$error}</div>{/foreach}
				</div>
			</div>
		</div>
	
		<div id="perms" class="dynamic-tab-panel" title="Permissions">
			<div class="form-group">
				<div class="col-xs-12">
					<h2>Current permissions</h2>
			
					<table class="table table-condensed table-secondary">
						<thead class="">
							<tr>
								<th scope="col">Permission</th>
								<th scope="col">Target</th>
								<th scope="col">Remove?</th>
							</tr>
						</thead>
						<tbody>
						{foreach $entityList as $entity}
						{foreach $entity.permissionList as $taskName}
						{assign var="shownPermission" value=true}
							<tr>
								<td><label for="task-{" "|str_replace:"_":$taskName|escape}-{$entity.id}">{$taskName|escape}</label></td>
								<td><label for="task-{" "|str_replace:"_":$taskName|escape}-{$entity.id}">{$entity.name|escape}</label></td>
								<td><label>
									<input 
									type="checkbox" 
									name="task[{$taskName|escape}][{$entity.id}]" 
									id="task-{" "|str_replace:"_":$taskName|escape}-{$entity.id}" 
									title="Check and click save to revoke the task {$taskName|escape} {$entity.name|escape}."></label></td>
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
					<select id="role-add-task" name="addTask" class="form-control">
						<option disabled="disabled" selected>choose a task</option>
					{foreach $taskDefinitionMap as $taskName}
						<option value="{$taskName|escape}">{$taskName|escape}</option>
					{/foreach}
					</select>
				</div>
				<div class="mt-3 mb-1 col-xs-12">
					<input type="submit" class="btn btn-light" name="command[apply]" value="{if $role->inDataSource}Save{else}Add{/if} and continue editing">
				</div>
			</div>
		</div>
	
		<div class="form-group">
			
		{generate_form_post_key}

			<div class="col-xs-12">
				<div class="commands">
				{if $role->inDataSource}<a href="admin/roles/{$role->id}/delete" class="btn btn-danger">Delete</a>{/if}
				<input type="submit" class="btn btn-primary" name="command[save]" value="{if $role->inDataSource}Save changes{else}Add{/if}">
				<a href="admin/roles" class="btn btn-link">Cancel</a>
				</div>
			</div>
		</div>
	</form>
</div>
