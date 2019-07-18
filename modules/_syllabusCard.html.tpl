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
			{if $btnView}
			<a class="btn btn-dark" href="syllabus/{$syllabus->id}/view">View</a>
			{/if}
			{if $btnEdit}
			<a class="btn btn-outline-primary" href="syllabus/{$syllabus->id}">Edit</a>
			{/if}
			{if $btnClone}
			<a class="btn btn-outline-primary" href="syllabus/startwith/{$syllabus->id}">Clone</a>
			{/if}
			{if $btnStartTemplateForCourse}
				<input class="btn btn-success" type="submit" name="command[start][department][{$syllabus->id}]" value="Start From Template">
			{/if}
		</div>
	</div>
</div>
