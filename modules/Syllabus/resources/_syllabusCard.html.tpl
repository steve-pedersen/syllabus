<div class="card h-100 mb-sm-2 {if !$organization && $syllabus->hasCourseSection}ribbon-wrapper{/if}">
	{if !$organization && ($syllabus->hasCourseSection || $syllabus->file)}
	<div class="ribbon-wrapper-gray">
		{if $syllabus->getShareLevel() == 'all'}
		<div class="ribbon ribbon-successs">
			<i class="fas fa-user-check text-success"></i> 
			<small class="font-w700">Shared</small>
		</div>
		{else}
		<div class="ribbon ribbon-warnings">
			<i class="fas fa-user-lock text-warning"></i> 
			<small class="font-w700">Private</small>
		</div>
		{/if}
	</div>
	{/if}
	<div class="card-body h-100">
		{if $syllabus->file}
		<div class="text-center"><i class="fas fa-file fa-5x text-center"></i></div>
		{else}
		<img src="assets/images/placeholder-4.jpg" class="border-bottom card-img-top crop-top crop-top-{if $cropSize}{$cropSize}{else}10{/if}" alt="{$syllabus->title}" data-src="syllabus/{$syllabus->id}/thumbinfo" id="syllabus-{$syllabus->id}">
		{/if}
		<h5 class="mt-3">
			{if $syllabus->semester}
				{$syllabus->syllabus->title|truncate:75}
			{elseif $syllabus->file}
				<a href="files/{$syllabus->file->id}/download">{$syllabus->file->remoteName}</a>
			{else}
				{$syllabus->title|truncate:75}
			{/if}
		</h5>
		<p class="card-text">
			{if $syllabus->semester}
				{$syllabus->syllabus->description|truncate:120}
			{else}
				{$syllabus->description|truncate:120}
			{/if}
			</p>
		{if !$hideDate && !$syllabus->semester}
		<small class="d-block"><strong>Last Modified:</strong> {$syllabus->modifiedDate->format('F jS, Y - h:i a')}</small>
		{/if}
	</div>
	<div class="card-footer">
		<div class="align-bottom mt-auto px-2 row">
			{if $btnStart}
			<a class="btn btn-success" href="syllabus/startwith/{$syllabus->id}">Start From {if $isTemplate}Template{else}Syllabus{/if}</a>
			{/if}

			{if $btnEdit}
				{if $overview}
				<!-- <div class="d-inline"> -->
				<div class="btn-group my-1" role="group" aria-label="Edit & View buttons">
					{if !$syllabus->file}
					<a href="{$routeBase}syllabus/{$syllabus->id}" class="btn btn-info">
						Edit
					</a>
					<a href="{$routeBase}syllabus/{$syllabus->id}/view" class="btn btn-dark">
						View
					</a>
					{else}
					<a href="{$routeBase}syllabus/{$syllabus->courseSection->id}/ilearn" class="btn btn-info">
						Edit
					</a>
					<a href="{$routeBase}syllabus/{$syllabus->id}/delete" class="btn btn-danger">
						<i class="far fa-trash-alt"></i>
					</a>
					{/if}
				</div>
			
				{if !$organization && $syllabus->hasCourseSection || !$organization && $syllabus->file}
					{include file="partial:_shareWidget.html.tpl"}
				{elseif !$organization && !$syllabus->hasCourseSection}
					<button type="button" class="btn btn-light text-muted d-inline-block ml-auto my-1" data-toggle="tooltip" data-html="true" data-placement="bottom" title="You must add a <strong>Course Information</strong> section to this syllabus before it can be shared.">
						<i class="fas fa-share"></i>
					</button>
				{/if}

				<!-- </div> -->
				{else}
				<a class="btn btn-info" href="syllabus/{$syllabus->id}">Edit</a>
				{/if}
			{/if}

			{if $btnView && $btnStart}
			<a class="btn btn-dark" href="syllabus/{$syllabus->id}/view">View</a>
			{/if}

			{if $btnStartTemplateForCourse}
				<input class="btn btn-success" type="submit" name="command[start][department][{$syllabus->id}]" value="Start From Template">
			{/if}
			{if !$btnStart && !$btnStartTemplateForCourse && !$overview}
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
					<div class="dropdown-divider"></div>
					<a href="syllabus/{$syllabus->id}/word" class="dropdown-item">
						<i class="far fa-file-word  mr-3 text-dark"></i> Export
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
		{if !$btnStart && !$btnStartTemplateForCourse && !$organization && $syllabus->hasCourseSection}

		{/if}
	</div>
</div>
