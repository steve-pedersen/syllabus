<div class="card" >
	<div class="card-body">
		<h4 class="card-title"><a href="{$organization->routeName}/{$organization->id}">{$organization->name}</a></h4>
		<h6 class="card-subtitle mb-2 text-muted">{$organization->abbreviation}</h6>
		<p class="card-text">{$organization->description}</p>
		<div class="d-flex justify-content-between align-items-center">
			<div class="">
				<a href="{$organization->routeName}/{$organization->id}" class="btn btn-outline-primary">View</a>
<!-- 				<button class="btn btn-outline-info btn-lg" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Options &nbsp;<i class="fas fa-ellipsis-v"></i>
				</button>
				<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				{if $pAdmin || $organization->userHasRole($viewer, 'manager')}
					{assign var='fullAccess' value=true}
					<div class="dropdown-divider"></div>
					<a href="{$organization->routeName}/{$organization->id}/users" class="dropdown-item">Users</a>
					<a href="{$organization->routeName}/{$organization->id}/settings" class="dropdown-item">Settings</a>
				{/if}
				{if $fullAccess || $organization->userHasRole($viewer, 'moderator')}
					<div class="dropdown-divider"></div>
					<a href="{$organization->routeName}/{$organization->id}/submissions" class="dropdown-item">Manage Submissions</a>
				{/if}
				{if $fullAccess || $organization->userHasRole($viewer, 'repository_manager')}
					<div class="dropdown-divider"></div>
					<a href="{$organization->routeName}/{$organization->id}/repository" class="dropdown-item">View Repository</a>
				{/if}
				{if $fullAccess || $organization->userHasRole($viewer, 'creator')}
					<div class="dropdown-divider"></div>
					<a href="{$organization->routeName}/{$organization->id}/syllabus/start" class="dropdown-item">New Template</a>
					<a href="{$organization->routeName}/{$organization->id}/templates" class="dropdown-item">View Templates</a>
				{/if}
				{if $fullAccess || $organization->userHasRole($viewer, 'communicator')}
					<div class="dropdown-divider"></div>
					<a href="{$organization->routeName}/{$organization->id}/communications/new" class="dropdown-item">Send Communication</a>
					<a href="{$organization->routeName}/{$organization->id}/communications" class="dropdown-item">View Communications</a>
				{/if}
				</div> -->
			</div>

		</div>
	</div>
</div>