
<div class="card" id="mySyllabi">
	<input type="hidden" name="mode" value="{$mode}">
	<div class="card-body">

		<ul class="nav nav-pills flex-column flex-sm-row mb-5 p-3 bg-light" id="myTab" role="tablist">
			<li class="nav-item flex-sm-fill">
				<a class="lead text-sm-center nav-link {if $mode == 'overview'}active{/if}" id="overview-tab"  href="syllabi?mode=overview" role="tab" aria-controls="overview" aria-selected="true">Overview</a>
			</li>
			<li class="nav-item flex-sm-fill">
				<a class="lead text-sm-center nav-link {if $mode == 'courses'}active{/if}" id="courses-tab"  href="syllabi?mode=courses" role="tab" aria-controls="courses" aria-selected="false">Courses</a>
			</li>
			<li class="nav-item flex-sm-fill">
				<a class="lead text-sm-center nav-link {if $mode == 'submissions'}active{/if}" id="submissions-tab"  href="syllabi?mode=submissions" role="tab" aria-controls="submissions" aria-selected="false">Submissions</a>
			</li>
		</ul>

		<div class="tab-content">
		  <div class="tab-pane {if $mode == 'overview'}active{/if}" id="overview" role="tabpanel" aria-labelledby="overview-tab">{include file="partial:_overview.html.tpl"}</div>
		  <div class="tab-pane {if $mode == 'courses'}active{/if}" id="courses" role="tabpanel" aria-labelledby="courses-tab">{include file="partial:_courses.html.tpl"}</div>
		  <div class="tab-pane {if $mode == 'submissions'}active{/if}" id="submissions" role="tabpanel" aria-labelledby="submissions-tab">{include file="partial:_submissions.html.tpl"}</div>
		</div>
	</div>
</div>
