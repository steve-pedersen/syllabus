
<ul class="list-group">
{foreach $syllabi as $syllabus}
	<li class="list-group-item"><a href="syllabus/{$syllabus->id}">{$syllabus->title}</a>
	{if $syllabus->modifiedDate}
		<small class="d-block"><strong>Last Modified:</strong> {$syllabus->modifiedDate->format('F jS, Y - h:i a')}</small>
	{/if}
	<a class="btn btn-secondary" href="syllabus/startwith/{$syllabus->id}">Create a new one with this one, then do other things.</a>
	</li>
{/foreach}
</ul>