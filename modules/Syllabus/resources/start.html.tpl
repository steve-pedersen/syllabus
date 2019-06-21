<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" id="chooseStartingPoint">
<div class="col p-md-5 p-sm-3">
	<div class="">
		{if $courseSection}
			<input type="hidden" name="course" value="{$courseSection->id}">
			<p class="lead">
				You are creating a new syllabus for <strong>{$courseSection->fullDisplayName}</strong>, 
				for the <strong>{$courseSection->term}</strong> semester.
				<!-- <br><a href="syllabus/start" class="float-right text-danger">Unselect this course</a> -->
			</p>
		{else}
			<p class="lead">
				You are creating a new syllabus and will be able to choose which course(s) it is for at a later point.
			</p>
		{/if}
	</div>
	<h1 class="text-dark">
		Choose your starting point:
	</h1>

<div class="accordion" id="startAccordion">

	<ul class="list-unstyled px-xl-5 mx-xl-5 mx-lg-2 mx-lg-2 form-inline">
		<li class="media my-lg-4 p-3" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
			<div class="form-check d-block-inline">
				<input class="form-check-input mr-5" type="radio" name="startingTemplate" id="startingTemplate1" value="university">
				<label class="form-check-label" for="startingTemplate1">
					<i class="start-icon fas fa-file fa-7x align-self-start mr-3 d-md-inline d-sm-none"></i>
					<div class="media-body">
						<h2 class="mt-0 mb-1 display-4">
						{if $pStartFromNothing}
							Create New Syllabus or University Template
						{else}
							University Base Template
						{/if}
						</h2>
						<p class="lead w-75 ml-1">
							Start fresh with a new syllabus draft, 
							which includes all SF State requirements.
						</p>
					</div>
				</label>
			</div>
		</li>
        <div id="collapseOne" class="collapse col-12 mt-0" aria-labelledby="headingOne" data-parent="#startAccordion">
		  	<div class="col mb-3 w-75 text-center">
		  		{if $pStartFromNothing}
				<a href="syllabus/new" class="btn btn-success btn-large">Begin</a>
		  		{else}
				<input class="btn btn-success btn-lg" type="submit" name="command[start]" value="Begin">
				{generate_form_post_key}
				{/if}
				<hr class="fancy-line-1">
			</div>
			
        </div> 

		<li class="media my-lg-4 p-3" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
			<div class="form-check d-block-inline">
				<input class="form-check-input mr-5" type="radio" name="startingTemplate" id="startingTemplate2" value="department">
				<label class="form-check-label" for="startingTemplate2">
					<i class="start-icon fas fa-file-alt fa-7x align-self-start mr-3 d-md-inline d-sm-none"></i>
					<div class="media-body">
						<h2 class="mt-0 mb-1 display-4">Department Template
						{if $organizations}
							<small class="text-muted font-size-18">(
							{foreach $organizations as $id => $org}
								{$org->abbreviation}{if !$org@last}/{/if}
							{/foreach})
							</small>
						{/if}
						</h2>
						<p class="lead w-75 ml-1">
						{if !$isTemplate}
							Your department has created a template for you to use, which 
							includes all SF State requirements, their own requirements, 
							and other resources that you may find useful.
						{else}
							Start from one of your existing department templates.
						{/if}
						</p>
					</div>
				</label>
			</div>
		</li>
		<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#startAccordion">
			{if $organizations}
				{assign var=btnStart value=true}
				{assign var=isTemplate value=true}
			{foreach $organizations as $organization}
			<div class="col px-5">
				<div class="row px-4">
					{foreach $templates[$organization->id] as $template}
						{assign var=syllabus value=$template}
					<div class="col-3 p-3 mh-50">
						{include file="partial:_syllabusCard.html.tpl"}
					</div>
					{foreachelse}
						<p>Your department hasn't created any templates!</p>
					{/foreach}
				</div>
			</div>
			{if $organization@iteration == 4}{break}{/if}
				
			{/foreach}
			{else}
				<p>You're not part of a department!</p>
			{/if}
		</div>


		{if !$isTemplate}
		<li class="media my-lg-4 p-3 collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
			<div class="form-check d-block-inline">
				<input class="form-check-input mr-5" type="radio" name="startingTemplate" id="startingTemplate3" value="clone">
				<label class="form-check-label" for="startingTemplate3">
					<i class="start-icon fas fa-file-invoice fa-7x align-self-start mr-3 d-md-inline d-sm-none"></i>
					<div class="media-body">
						<h2 class="mt-0 mb-1 display-4">Start From Another Syllabus</h2>
						<p class="lead w-75 ml-1">
							Create a duplicate from any one of your other syllabi, 
							such as one from a previous semester or a personal template.
						</p>
					</div>
				</label>
			</div>
		</li>
		{else}
			<input type="hidden" name="template" value="{$templateAuthorizationId}">
		{/if}

		<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#startAccordion">
		<div class="row mb-3">
{if $pastCourseSyllabi}
	{assign var=relevantSyllabi value=$pastCourseSyllabi}
{else}
	{assign var=relevantSyllabi value=$syllabi}
{/if}

	{assign var=btnStart value=true}
	{foreach $relevantSyllabi as $i => $syllabus}
		{if $i == 4}{break}{/if}
			<div class="col-lg-3 col-md-4 p-3">
				{include file="partial:_syllabusCard.html.tpl"}
			</div>
	{foreachelse}
		<p>You have no syllabi yet!</p>
	{/foreach}
		</div> <!-- END row -->
		</div>

	</ul>
</div>


</div>
</form>