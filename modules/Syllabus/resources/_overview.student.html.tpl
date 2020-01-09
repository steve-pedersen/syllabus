<div class="container-fluid student-container px-0">

{if $syllabusRoles}
	{assign var=isStudent value=true}
	<div class="overview-section my-adhoc-syllabi">
		{include file="partial:_overview.adhocSyllabi.html.tpl"}
	</div>
{/if}


<div class="accordion" id="termAccordion">

{foreach $allCourses as $term => $myCourses}

	<div class="d-block w-100 p-3 mt-3 bg-secondary rounded">
		<a href="#" class="{if $term != $activeSemester->display}collapsed{/if} mt-1 d-block collapse-term" data-toggle="collapse" data-target="#collapse{$myCourses@index}" aria-expanded="true" aria-controls="collapse{$myCourses@index}">
			<h3 class="text-white">
				<i class="fas fa-plus mx-2 minimized text-dark"></i>
				<i class="fas fa-minus mx-2 expanded text-dark"></i>
				{$term}
			</h3>
		</a>
	</div>
    <div id="collapse{$myCourses@index}" class="collapse col-12 mt-0 {if $term == $activeSemester->display}show{/if}" aria-labelledby="headingOne" data-parent="#termAccordion">

	<div class="row ">
{foreach $myCourses as $i => $course}
	{if $i != 0 && $i%2 == 0}
	</div><div class="row ">
	{/if}	
	
	<div class="col-xl-6 col-lg-6 col-md-12 mt-4">
		<div class="card card-bordered card-bordered-left card-bordered-{if $course->courseSyllabus}success{else}light{/if} h-100 rounded">
			<div class="card-body row">
				<!-- <div class="col-4 border-right text-center d-block"> -->
				<div class="col-xl-4 col-lg-5 col-md-5 col-sm-12 col-xs-12 image-block border-right text-center d-block">
				{if $course->courseSyllabus}
				<a href="syllabus/{$course->courseSyllabus->id}/view" class="">
					<div class="paper paper-bottom mb-1">
					<img src="assets/images/placeholder-4.jpg" data-src="syllabus/{$syllabus->id}/thumbinfo" id="syllabus-{$syllabus->id}" class="img-thumbnail" alt="{$course->title} syllabus preview" style="min-height:120px;border:1px solid #dee2e6;border-radius:0;">
				</div>
				</a>
				{else}
					<span class="text-center d-block h-100  text-muted no-syllabus">
						<span class="vertical-spacer d-block"></span>
						<em class="">No syllabus on file for this course</em>
					</span>
				{/if}
				</div>
				<!-- <div class="col-8"> -->
				<div class="col-xl-8 col-lg-7 col-md-7 col-sm-12 col-xs-12">
					<h5 class="card-title mb-2 {if !$course->courseSyllabus}text-muted{/if}">
						{$course->classNumber}: <span class="{if $course->courseSyllabus}text-dark{/if}">{$course->title}</span>
					</h5>
<!-- 					<div class="wrap pb-1">
						<div class="left w-100 {if !$course->courseSyllabus}border-light{/if}"></div>
						<div class="right"></div>
					</div> -->
					<p class="card-text mb-3 mt-0 {if !$course->courseSyllabus}text-muted{/if}">
						<span class="text-dark">Section {$course->sectionNumber}</span>
					</p>
				{if $course->courseSyllabus}
			<div class="row text-center">
				<div class="col-xl-8 offset-xl-2 col-lg-12 offset-lg-0 col-sm-10 offset-sm-1 col-xs-8 offset-xs-2 d-block">
					<a href="syllabus/{$course->courseSyllabus->id}/view" class="mb-4 btn btn-dark btn-lg btn-block">
						<i class="far fa-eye  mr-3"></i> View
					</a>
				</div>
				<div class="col-xl-8 col-lg-9 col-sm-8 col-xs-12 d-block">
					<a href="syllabus/{$course->courseSyllabus->id}/word" class="mb-2 btn btn-link btn-blocks">
						<i class="far fa-file-word"></i> Download as Word
					</a>
				</div>
				<div class="col-xl-4 col-lg-4 col-sm-4 col-xs-12 d-block">
					<a href="syllabus/{$course->courseSyllabus->id}/print" class="mb-2 btn btn-link btn-blocks">
						<i class="fas fa-print"></i> Print
					</a>
				</div>
			</div>
				{/if}
				</div>

			</div>
		</div>
	</div>

{/foreach}
	</div>

	</div> 
{/foreach}
</div>

</div>
