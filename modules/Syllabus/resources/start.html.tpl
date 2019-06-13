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

<div class="accordion" id="accordionExample">

	<ul class="list-unstyled px-xl-5 mx-xl-5 mx-lg-2 mx-lg-2 form-inline">
		<li class="media my-lg-4 p-3" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
			<div class="form-check d-block-inline">
				<input class="form-check-input mr-5" type="radio" name="startingTemplate" id="startingTemplate1" value="university" checked>
				<label class="form-check-label" for="startingTemplate1">
					<i class="start-icon fas fa-file fa-7x align-self-start mr-3 d-md-inline d-sm-none"></i>
					<div class="media-body">
						<h2 class="mt-0 mb-1 display-4">University Base Template</h2>
						<p class="lead w-75 ml-1">
							Start fresh with a new syllabus draft, 
							which includes all SF State requirements.
						</p>
					</div>
				</label>
			</div>
		</li>
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample"></div> 

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
		<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
			{if $organizations}
			{foreach $organizations as $organization}
			<div class="col px-5">
				<div class="row px-4">
					{foreach $templates[$organization->id] as $template}
					<div class="col-3 p-3 mh-50">
						<div class="card" style="">
							<img src="assets/images/testing0{($template@index % 5)+1}.jpg" class="card-img-top img-fluid crop-top crop-top-8" alt="...">
							<div class="card-body">
								<h5 class="card-title">{$template->title}</h5>
								<p class="card-text">{$template->description}</p>
							</div>
							<div class="card-body">
								<a href="syllabus/startwith/{$template->id}" class="btn btn-success btn-sm">Start From Template</a>
							</div>
						</div>
					</div>
					{/foreach}
				</div>
			</div>
			{if $organization@iteration == 4}{break}{/if}
			{/foreach}
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

		<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
			<div class="card-body">
		Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
			</div>
		</div>

	</ul>

</div>

	<div class="col px-lg-5">
		<hr>
		<input class="btn btn-primary btn-lg" type="submit" name="command[start]" value="Submit">
		{generate_form_post_key}
	</div>
</div>
</form>