<h2>My Syllabi: <small class="text-muted">Recently Modified</small></h2>
<p class="text-muted">Create a new syllabus or access your most recently modified syllabi. Click "see more" to show more results.</p>

<div class="">
<div class="row card-group">
	<div class="card mx-3">
		<a href="syllabus/start" class="text-center align-text-middle text-success h-100">
		<div class="card-body">
			<i class="h-50 mt-5 mb-5 fas fa-plus-circle fa-7x"></i>
			<p class="h-50 text-center align-bottom text-success mt-3"><strong>Start a new syllabus</strong></p>
		</div>
		<div class="card-footer">
			<div class="align-bottom mt-auto">
				<a class="btn btn-success" href="syllabus/start">Create New</a>
			</div>
		</div>
		</a>
	</div>
{foreach $syllabi as $i => $syllabus}
	{if $i == 3}{break}{/if}
	<div class="card mx-3">
		<div class="card-body h-100">
			<img src="assets/images/testing01.jpg" class="card-img-top crop-top crop-top-13" alt="{$syllabus->title}">
			<h5 class="mt-3">{$syllabus->title}</h5>
			<p class="card-text">{$syllabus->description}</p>
			<small class="d-block"><strong>Last Modified:</strong> {$syllabus->modifiedDate->format('F jS, Y - h:i a')}</small>
		</div>
		<div class="card-footer">
			<div class="align-bottom mt-auto">
				<a class="btn btn-info" href="syllabus/{$syllabus->id}">Edit</a>
				<a class="btn btn-secondary" href="syllabus/startwith/{$syllabus->id}">Clone</a>
			</div>
		</div>
		</a>
	</div>
{/foreach}
</div>
</div>