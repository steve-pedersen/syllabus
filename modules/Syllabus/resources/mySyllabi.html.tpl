<!-- <h1>My Syllabi</h1> -->
<ul class="list-group">
{foreach $syllabi as $syllabus}
	<li class="list-group-item">
		<strong><a href="syllabus/{$syllabus->id}">{$syllabus->title}</a> - </strong> {$syllabus->description}
		<small class="float-right">
			{if $syllabus->modifiedDate}
				<strong>Last modified: {$syllabus->modifiedDate->format('Y-m-d')}</strong> {$syllabus->modifiedDate->format('h:iA')}
			{else}
				<strong>Date created: {$syllabus->createdDate->format('Y-m-d')}</strong> {{$syllabus->createdDate->format('h:iA')}}
			{/if}
		</small>
	</li>
{/foreach}
</ul>