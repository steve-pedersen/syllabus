<div class="container-fluid my-syllabi-overview mb-5 p-5">
<h1>Manage University Templates</h1>
<p class="text-muted">Instructions: have the template creator start a syllabus and save it, then enter their id (primary key) to search for that syllabus here.</p>

<form action="{$smarty.server.REQUEST_URI}" method="get" class="form-inline" role="form" id="templateUserId">
<div class="col mb-5 border-bottom">
	<div class="form-group row mb-5">
		<label for="inputUserId" class=" col-form-label">Template Creator User ID</label>
		<div class="mx-5">
			<input name="userId" type="text" class="form-control" id="inputUserId" placeholder="Primary Key" value="{$userId}">
		</div>
		<input class="btn btn-primary" type="submit" id="account-search-button" name="btn" value="Search">
	</div>
</div>
</form>

{if $universityTemplates}
<form action="{$smarty.server.REQUEST_URI}" method="post" class="form-inline" role="form" id="templateId">
	<div class="row mb-3">
		<div class="col-lg-2 col-md-4 px-2">
			<div class="card h-100">
				<a href="syllabus/start" class="text-center align-text-middle text-success h-100">
				<div class="card-body">
					<i class="h-50 mt-5 mb-5 fas fa-plus-circle fa-7x"></i>
					<p class="h-50 text-center align-bottom text-success mt-3"><strong>Start a new template</strong></p>
					<p class="align-bottom card-text mt-3">After creating the template, come back to this page and select it as the University Template.</p>
				</div>
				<div class="card-footer">
					<div class="align-bottom mt-auto">
						<a class="btn btn-success" href="syllabus/start">Create New</a>
					</div>
				</div>
				</a>
			</div>
		</div>
	<!-- <div class="row mb-3">	 -->
	{foreach $universityTemplates as $i => $syllabus}
		{if $i == 9}{break}{/if}
		<div class="col-lg-2 col-md-4 px-2">
			<div class="card">
				<label class="form-check-label" for="overlayCheck{$i}">
				<div class="card-body h-100 p-0">
					<div class="ml-auto text-right">
					<div class="form-check d-flex justify-content-end">
						<input name="template" data-index="{$i}" type="radio" class="mr-2 form-check-input overlay-radio" 
						id="overlayCheck{$i}" value="{$syllabus->id}" {if $templateId && $templateId == $syllabus->id}checked{/if} style="margin-bottom:-2em;z-index:1;">
					</div>
					</div>
					<div class="card-img-top-overlay p-0">
						<div class="text-center vertical-align overlay-icon" id="checkIcon{$i}" >
							<i class="fas fa-check fa-7x text-success"></i>
						</div>
						{if $syllabus->imageUrl}
							<img src="{$syllabus->imageUrl}" class="card-img-top crop-top crop-top-13" alt="{$syllabus->title}" />
						{else}
							<img src="assets/images/testing0{$i}.jpg" class="card-img-top crop-top crop-top-13" alt="{$syllabus->title}" />
						{/if}
						
					</div>
					<div class="card-text p-3">
					<h6 class="mt-3 text-dark">
						<a href="syllabus/{$syllabus->id}" target="_blank">{$syllabus->title}</a>
					</h6>
					<small class="d-block">
						<p class="card-text">{$syllabus->description|truncate:175}
							<strong class="d-block">Last Modified:</strong> 
							{$syllabus->modifiedDate->format('F jS, Y - h:i a')}
						</p>
					</small>
				</div>
				</div>
				</label>
			</div>
		</div>
	{/foreach}
	</div>

	<div class="col my-5 py-5 border-top">
		<a href="{$smarty.server.REQUEST_URI}" class="btn btn-outline-default mx-1">Cancel</a>
		<input type="submit" name="command[select]" class="btn btn-primary">
		{if $templateId}
		<input type="submit" name="command[unset]" class="btn btn-danger" value="Unset">
		{/if}
	</div>
	{generate_form_post_key}
</form>
{else}
		<div class="col-lg-3 col-md-4 px-2">
			<div class="card h-100">
				<a href="syllabus/start" class="text-center align-text-middle text-success h-100">
				<div class="card-body">
					<i class="h-50 mt-5 mb-5 fas fa-plus-circle fa-7x"></i>
					<p class="h-50 text-center align-bottom text-success mt-3"><strong>Start a new template</strong></p>
					<p class="align-bottom card-text mt-3">After creating the template, come back to this page and select it as the University Template.</p>
				</div>
				<div class="card-footer">
					<div class="align-bottom mt-auto">
						<a class="btn btn-success" href="syllabus/start">Create New</a>
					</div>
				</div>
				</a>
			</div>
		</div>
{/if}

</div>