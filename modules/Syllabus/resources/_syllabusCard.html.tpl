<div class="card h-100">
	<div class="card-body h-100">
		<img src="assets/images/testing0{$i}.jpg" class="card-img-top crop-top crop-top-{if $cropSize}{$cropSize}{else}10{/if}" alt="{$syllabus->title}">
		<h5 class="mt-3">{$syllabus->title}</h5>
		<p class="card-text">{$syllabus->description}</p>
		<small class="d-block"><strong>Last Modified:</strong> {$syllabus->modifiedDate->format('F jS, Y - h:i a')}</small>
	</div>
	<div class="card-footer">
		<div class="align-bottom mt-auto">
			{if $btnStart}
			<a class="btn btn-success" href="syllabus/startwith/{$syllabus->id}">Start From {if $isTemplate}Template{else}Syllabus{/if}</a>
			{/if}
			{if $btnView}
			<a class="btn btn-dark" target="_blank" href="syllabus/{$syllabus->id}/view">View</a>
			{/if}
			{if $btnEdit}
			<a class="btn btn-light" href="syllabus/{$syllabus->id}">Edit</a>
			{/if}
			{if $btnClone}
			<a class="btn btn-light" href="syllabus/startwith/{$syllabus->id}">Clone</a>
			{/if}
			{if $btnStartTemplateForCourse}
				<input class="btn btn-success" type="submit" name="command[start][{$syllabus->id}]" value="Start From Template">
			{/if}
		</div>
	</div>
</div>
