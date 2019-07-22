<div class="card h-100">
	<div class="card-body h-100">
	{if $syllabus->imageUrl}
		<img src="{$syllabus->imageUrl}" class="border-bottom card-img-top crop-top crop-top-{if $cropSize}{$cropSize}{else}10{/if}" alt="{$syllabus->title}">
	{else}
		<img src="assets/images/testing0{$i}.jpg" class="border-bottom card-img-top crop-top crop-top-{if $cropSize}{$cropSize}{else}10{/if}" alt="{$syllabus->title}">
	{/if}
		<h5 class="mt-3">{$syllabus->title}</h5>
		<p class="card-text">{$syllabus->description}</p>
		{if !$hideDate}
		<small class="d-block"><strong>Last Modified:</strong> {$syllabus->modifiedDate->format('F jS, Y - h:i a')}</small>
		{/if}
	</div>
	<div class="card-footer">
		<div class="align-bottom mt-auto">
			{if $btnStart}
			<a class="btn btn-success" href="syllabus/startwith/{$syllabus->id}">Start From {if $isTemplate}Template{else}Syllabus{/if}</a>
			{/if}

			{if $btnEdit}
			<a class="btn btn-info" href="syllabus/{$syllabus->id}">Edit</a>
			{/if}

			{if $btnView && $btnStart}
			<a class="btn btn-dark" href="syllabus/{$syllabus->id}/view">View</a>
			{/if}

			{if $btnStartTemplateForCourse}
				<input class="btn btn-success" type="submit" name="command[start][department][{$syllabus->id}]" value="Start From Template">
			{/if}
			{if !$btnStart && !$btnStartTemplateForCourse}
			<div class="dropdown d-inline">
				<a class="btn btn-dark dropdown-toggle pull-right" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Options
				</a>
				<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
					<a class="dropdown-item" id="viewFromEditor" href="{$routeBase}syllabus/{$syllabus->id}/view">
						<i class="far fa-eye  mr-3 text-dark"></i> View
					</a>
				{if !$organization && $syllabus->hasCourseSection}
					<div class="dropdown-divider"></div>
					<a href="syllabus/{$syllabus->id}/share" class="dropdown-item">
						<i class="fas fa-share-square  mr-3 text-primary"></i> Share
					</a>
				{/if}
				{if $btnClone}
					<div class="dropdown-divider"></div>
					<a href="{$routeBase}syllabus/startwith/{$syllabus->id}" class="dropdown-item">
						<i class="far fa-copy  mr-3 text-secondary"></i> Clone
					</a>
				{/if}
				{if $syllabus->inDataSource}
					<div class="dropdown-divider"></div>
					<a sr-only="Delete" class="dropdown-item" id="viewFromEditor" href="{$routeBase}syllabus/{$syllabus->id}/delete">
						<i class="fas fa-trash  mr-3 text-danger"></i> Delete
					</a>
				{/if}
				</div>
			</div>
			{/if}

		</div>
	</div>
</div>
