
{if !$isStudent}
<div class="p-3 my-syllabi-container">
	<div class="my-syllabi-nav-container border-bottom mb-4">
	<div class="my-syllabi-nav ">
		<nav class="nav">
			<a class="nav-link mr-md-5 mr-sm-3 {if $mode == 'overview'}active{/if}" id="overview-tab" href="syllabi?mode=overview" aria-controls="overview" aria-selected="true">
				Overview
			</a>
			<a class="nav-link mx-md-5 mx-sm-3 {if $mode == 'courses'}active{/if}" id="courses-tab"  href="syllabi?mode=courses" aria-controls="courses" aria-selected="false">
				Courses
			</a>
<!-- 			<a class="nav-link mx-md-5 mx-sm-3 {if $mode == 'submissions'}active{/if}" id="submissions-tab"  href="syllabi?mode=submissions" aria-controls="submissions" aria-selected="false">
				Submissions
			</a> -->
		{if $mode == 'overview' || ($mode != 'courses' && $mode != 'submissions')}
			<div class="ml-auto d-inline search-container">
			<form action="{$smarty.server.REQUEST_URI}" method="get" class="form-inline" role="form" id="searchSyllabi">
				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<button class="btn btn-outline-secondary" type="submit" id="search-addon">Go</button>
					</div>
					<input type="text" name="search" class="form-control" placeholder="Search syllabi..." aria-label="Search for syllabi" aria-describedby="search-addon" value="{$searchQuery}">
				</div>
			</form>
			</div>
		{/if}
		</nav>
	</div>
	</div>

	{if $searchQuery}
	<div class="ml-auto text-right remove-search-container" style="margin-top:-1.5em;position:relative;">					
		<a href="syllabi" class="ml-auto remove-search-link font-w700" style="">Remove Search</a>				
	</div>
	{/if}

	<div class="card border-0" id="mySyllabi">
		<input type="hidden" name="mode" value="{$mode}">
		<div class="card-body px-0">
			<div class="tab-content">
				<div class="tab-pane {if $mode == 'overview'}active{/if}" id="overview" role="tabpanel" aria-labelledby="overview-tab">
					{if $mode == 'overview' || ($mode != 'courses' && $mode != 'submissions')}
						{include file="partial:_overview.html.tpl"}
					{/if}
				</div>
				<div class="tab-pane {if $mode == 'courses'}active{/if}" id="courses" role="tabpanel" aria-labelledby="courses-tab">
					{if $mode == 'courses'}
						{include file="partial:_courses.html.tpl"}
					{/if}
				</div>
<!-- 				<div class="tab-pane {if $mode == 'submissions'}active{/if}" id="submissions" role="tabpanel" aria-labelledby="submissions-tab">
					{if $mode == 'submissions'}
						{include file="partial:_submissions.html.tpl"}
					{/if}
				</div> -->
			</div>
		</div>
	</div>
</div>
{else}
<div class="p-3 my-syllabi-container">
	<div class="my-syllabi-nav-container border-bottom mb-4">
	<div class="my-syllabi-nav ">
		<nav class="nav">
			<a class="nav-link mr-md-5 mr-sm-3 {if $mode == 'overview'}active{/if}" id="overview-tab" href="syllabi?mode=overview" aria-controls="overview" aria-selected="true">
				My Syllabi
			</a>
		</nav>
	</div>
	</div>

	<div class="card border-0" id="mySyllabi">
		<input type="hidden" name="mode" value="{$mode}">
		<div class="card-body px-0">
			<div class="tab-content">
				<div class="tab-pane {if $mode == 'overview'}active{/if}" id="overview" role="tabpanel" aria-labelledby="overview-tab">
					{if $mode == 'overview' || ($mode != 'courses' && $mode != 'submissions')}
						{include file="partial:_overview.student.html.tpl"}
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>

{/if}
