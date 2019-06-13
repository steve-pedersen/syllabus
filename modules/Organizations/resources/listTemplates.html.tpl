<div class="row">
	{foreach $templates as $template}
	<div class="col-3 p-3">

		<div class="card" style="">
			<img src="assets/images/testing0{($template@index % 5)+1}.jpg" class="card-img-top img-fluid crop-top crop-top-10" alt="...">
			<!-- <i class="card-img-top fas fa-file-invoice fa-5x"></i> -->
			<div class="card-body">
				<h5 class="card-title">{$template->title}</h5>
				<p class="card-text">{$template->description}</p>
			</div>
			<div class="card-body">
				<a href="{$organization->templateAuthorizationId}/syllabus/{$template->id}" class="btn btn-info">Edit</a>
				<a href="{$organization->templateAuthorizationId}/syllabus/startwith/{$template->id}" class="btn btn-secondary">Clone</a>
				<!-- <a href="{$organization->templateAuthorizationId}/syllabus/{$template->id}" class="card-link text-secondary">View</a> -->
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