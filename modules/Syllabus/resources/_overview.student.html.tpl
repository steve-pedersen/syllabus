<div class="container-fluid student-container px-0">

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
				<div class="col-4 border-right text-center d-block">
				{if $course->courseSyllabus}
					<img src="{$course->imageUrl}" class="img-thumbnail" alt="{$course->title} syllabus preview" style="min-height:140px;">
				{else}
					<span class="text-center d-block h-100 pt-5 text-muted">
						<em class="">No syllabus on file for this course</em>
					</span>
				{/if}
				</div>
				<div class="col-8">
					<h5 class="card-title {if !$course->courseSyllabus}text-muted{/if}">
						{$course->classNumber}: {$course->title}
					</h5>
					<p class="card-text mb-5 mt-2 {if !$course->courseSyllabus}text-muted{/if}">
						Section {$course->sectionNumber}
					</p>
				{if $course->courseSyllabus}
					<a href="syllabus/{$course->courseSyllabus->id}/view" class="mb-2 btn btn-dark">
						View
					</a>
					<a href="syllabus/{$course->courseSyllabus->id}/export" class="mb-2 btn btn-link">
						<i class="far fa-file-word"></i> Download as Word
					</a>
					<a href="syllabus/{$course->courseSyllabus->id}/print" class="mb-2 btn btn-link">
						<i class="fas fa-print"></i> Print
					</a>
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
