{if $syllabusRoles}
<div class="container-fluid my-adhoc-syllabi-overview mb-5">
	<h2>
		{if $isStudent}
			Faculty Syllabi:
		{else}
			Other Syllabi: 
		{/if}
		<small class="text-muted">Edit Access</small>
	</h2>
	<p class="text-muted">
		These are other people's syllabi for which you've been granted access to edit.
	</p>
	<div class="row">
	{foreach $syllabusRoles as $syllabusRole}
		{assign var=syllabus value=$syllabusRole->syllabus}
	<div class="col mx-1 mb-3">
		<div class="card adhoc-syllabus-card" >
			<div class="card-body row">
				<div class="col-8 pr-1">
					<h5 class="card-title">
						{$syllabus->latestVersion->title}
					</h5>
					<h6 class="card-subtitle mb-1 text-muted">
						Created by {$syllabus->createdBy->fullName}
					</h6>
				</div>
				<div class="col-4 pl-0">
					<img class="img-thumbnail" src="assets/images/placeholder-4.jpg" data-src="syllabus/{$syllabus->id}/thumbinfo" id="syllabus-{$syllabus->id}">
				</div>
				<div class="col-12 mt-2">
					<p class="card-text text-center">
						{$syllabus->latestVersion->description|truncate:125}
						{if $syllabusRole->expiryDate}<br>
							My edit access expires in:<br><span class="text-primary">{$syllabusRole->expiration}</span>
						{/if}
					</p>
					<div class="btn-groups text-center my-1" role="group" aria-label="Edit & View buttons">
						<a href="{$routeBase}syllabus/{$syllabus->id}" class="btn btn-outline-info " style="">
							Edit
						</a>
						<a href="{$routeBase}syllabus/{$syllabus->id}/view" class="btn btn-outline-dark">
							View
						</a>
					</div>		
				</div>
			</div>
			<div class="card-footer">
				<small>Last modified: {$syllabus->modifiedDate->format('F jS, Y - h:i a')}</small>
			</div>
		</div>
	</div>
	{/foreach}
	</div>
</div>
{/if}
