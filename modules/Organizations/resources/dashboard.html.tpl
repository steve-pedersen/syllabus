{assign var=organizationType value=$organization->getOrganizationType()}

<div class="col-md-6 col-sm-8">
	<p class="stretched-link">{$organization->description}</p>
	<h4>Options:</h4>
	<ul class="list-group list-group-horizontal-sm">
		{assign var='fullAccess' value=false}
		{if $pAdmin || $organization->userHasRole($viewer, 'manager')}
			{assign var='fullAccess' value=true}
			<li class="list-group-item list-group-item-action">
				<i class="fas fa-users fa-2x col-1"></i>
				<a href="{$organization->routeName}/{$organization->id}/users" class="stretched-link col-11">Users</a>
			</li>
			<!-- <li class="list-group-item list-group-item-action"><i class="fa-2x mr-3"></i>
			<a href="{$organization->routeName}/{$organization->id}/settings" class="stretched-link"><i class="fa-2x mr-3"></i>
			Settings</a></li> -->
		{/if}
		{if $fullAccess || $organization->userHasRole($viewer, 'moderator')}
			<li class="list-group-item list-group-item-action">
				<i class="fas fa-cloud fa-2x col-1"></i>
				<a href="{$organization->routeName}/{$organization->id}/submissions" class="stretched-link col-11">Manage Submissions</a>
			</li>
		{/if}
		{if $fullAccess || $organization->userHasRole($viewer, 'repository_manager')}
<!-- 			<li class="list-group-item list-group-item-action">
				<i class="fa-2x mr-3"></i>
				<a href="{$organization->routeName}/{$organization->id}/repository" class="stretched-link">View Repository</a>
			</li> -->
		{/if}
		{if $fullAccess || $organization->userHasRole($viewer, 'creator')}
			<li class="list-group-item list-group-item-action">
				<i class="fas fa-plus fa-2x col-1"></i>
				<a href="{$organization->routeName}/{$organization->id}/syllabus/start" class="stretched-link col-11">New Template</a>
			</li>
			<li class="list-group-item list-group-item-action">
				<i class="far fa-folder-open fa-2x col-1"></i>
				<a href="{$organization->routeName}/{$organization->id}/templates" class="stretched-link col-11">View Templates</a>
			</li>
		{/if}
		{if $fullAccess || $organization->userHasRole($viewer, 'communicator')}
<!-- 			<li class="list-group-item list-group-item-action">
				<i class="fa-2x mr-3"></i>
				<a href="{$organization->routeName}/{$organization->id}/communications/new" class="stretched-link">Send Communication</a>
			</li>
			<li class="list-group-item list-group-item-action">
				<i class="fa-2x mr-3"></i>
				<a href="{$organization->routeName}/{$organization->id}/communications" class="stretched-link">View Communications</a>
			</li> -->
		{/if}
	</ul>
</div>