<div class="container-fluid my-syllabi-overview mb-5">
<h2>My Syllabi: <small class="text-muted">Recently Modified</small></h2>
<p class="text-muted">Create a new syllabus or access your most recently modified syllabi. Click "see more" to show more results.</p>
	<div class="row mb-3">
		<div class="col-lg-3 col-md-4 px-2">
			<div class="card h-100">
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
		</div>
{assign var=hasSeeMore value=false}
{foreach $syllabi as $i => $syllabus}
	{if $i == 9}{break}{/if}
	{if $i == 3}
		{assign var=hasSeeMore value=true}
	</div> <!-- END row1 -->
<div class="collapse" id="seeMoreSyllabi">
	<div class="row mb-3">
	{elseif ($i > 4 && ($i % 4) == 0)}
	</div> <!-- END row2 -->
	<div class="row mb-3">
	{/if}
		<div class="col-lg-3 col-md-4 px-2">
			<div class="card h-100">
				<div class="card-body h-100">
					<img src="assets/images/testing0{$i}.jpg" class="card-img-top crop-top crop-top-13" alt="{$syllabus->title}">
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
			</div>
		</div>
{foreachelse}
	<p>You have no syllabi yet!</p>
{/foreach}
	</div> <!-- END row3 -->
{if $hasSeeMore}
</div> <!-- END collapse -->
<div class="float-right mb-5">
	<a class="collapsed ml-auto see-more-toggle" id="seeMoreToggle2" data-toggle="collapse" href="#seeMoreSyllabi" aria-expanded="false" aria-controls="seeMoreSyllabi"></a>
</div>
{/if}
</div>
