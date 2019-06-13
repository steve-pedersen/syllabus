
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
			<a class="nav-link mx-md-5 mx-sm-3 disabled" id="submissions-tab" tabindex="-1"  aria-disabled="true" disabled>
	<!-- 		<a class="nav-link mx-md-5 mx-sm-3 disabled {if $mode == 'submissions'}active{/if}" id="submissions-tab"  href="syllabi?mode=submissions" aria-controls="submissions" aria-selected="false" disabled> -->
				Submissions
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
						{include file="partial:_overview.html.tpl"}
					{/if}
				</div>
				<div class="tab-pane {if $mode == 'courses'}active{/if}" id="courses" role="tabpanel" aria-labelledby="courses-tab">
					{if $mode == 'courses'}
						{include file="partial:_courses.html.tpl"}
					{/if}
				</div>
				<div class="tab-pane {if $mode == 'submissions'}active{/if}" id="submissions" role="tabpanel" aria-labelledby="submissions-tab">
					{if $mode == 'submissions'}
						{include file="partial:_submissions.html.tpl"}
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>

