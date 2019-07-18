<div class="row">
	{foreach $templates as $template}
	<div class="col-3 p-3">

		<div class="card" style="">
			<img src="{$template->imageUrl}" class="card-img-top img-fluid crop-top crop-top-10" alt="{$organization->title} template">
			<!-- <i class="card-img-top fas fa-file-invoice fa-5x"></i> -->
			<div class="card-body border-top">
				<h5 class="card-title">{$template->title}</h5>
				<p class="card-text">{$template->description}</p>
			</div>
			<div class="card-body">
				<a href="{$organization->templateAuthorizationId}/syllabus/{$template->id}/view" class="btn btn-dark">View</a>
				<a href="{$organization->templateAuthorizationId}/syllabus/{$template->id}" class="btn btn-outline-primary">Edit</a>
				<a href="{$organization->templateAuthorizationId}/syllabus/startwith/{$template->id}" class="btn btn-outline-primary">Clone</a>
				
				<a sr-only="Delete" class="btn btn-danger ml-auto float-right" id="viewFromEditor" href="{$organization->templateAuthorizationId}/syllabus/{$syllabus->id}/delete">
					<i class="fas fa-trash"></i>
				</a>
			</div>
			<div class="card-footer text-muted">
				{if $template->createdDate}
					<small class="d-block"><strong>Created:</strong> {$template->createdDate->format('F jS, Y - h:i a')}</small>
				{/if}
				{if $template->modifiedDate}
					<small class="d-block"><strong>Last Modified:</strong> {$template->modifiedDate->format('F jS, Y - h:i a')}</small>
				{/if}
			</div>
		</div>

	</div>
	{/foreach}

</div>
<div class="row mt-3">
	<div class="col p-4">
		<a href="{$organization->routeName}/{$organization->id}/syllabus/start" class="btn btn-success">
			<i class="fas fa-plus mr-2"></i>Create New Template
		</a>
	</div>
</div>