<div class="container-fluid table-collapse-container px-0">

<div class="accordion" id="termAccordion">

{foreach $allCourses as $term => $myCourses}

	<div class="d-block w-100 p-3 {if $myCourses@index > 0}mt-3{/if} bg-secondary rounded-top">
		<a href="#" class="{if $term != $activeSemester->display}collapsed{/if} mt-1 d-block collapse-term" data-toggle="collapse" data-target="#collapse{$myCourses@index}" aria-expanded="true" aria-controls="collapse{$myCourses@index}">
			<h3 class="text-white">
				<i class="fas fa-plus mx-2 minimized text-dark"></i>
				<i class="fas fa-minus mx-2 expanded text-dark"></i>
				{$term}
			</h3>
		</a>
	</div>
    <div id="collapse{$myCourses@index}" class="collapse px-0 col-12 mt-0 {if $term == $activeSemester->display}show{/if}" aria-labelledby="headingOne" data-parent="#termAccordion">

	<table class="table table-bordered table-responsive-sm bg-white shadow-sm" >
		<thead class="thead-light">

			<tr class="">
				<th scope="col" class="text-dark " style="">Course Information</th>
				<th scope="col" class="text-dark ">Syllabus Details</th>
				<th scope="col" class="text-dark ">Submission Status</th>
				<th scope="col" class="text-dark ">Options</th>
			</tr>
		</thead>
		<tbody >

{foreach $myCourses as $i => $courseSection}

	{assign var=syllabus value=$courseSection->courseSyllabus}
	{assign var=submission value=$courseSection->submission}


	<tr class="
		{if ($submittedCourseId && $courseSection->id == $submittedCourseId) || $submission->status == 'approved'}
			bg-context success
		{elseif !$syllabus && !$submission->file}
			
		{/if}
	">
		<td class="align-middle">{$courseSection->getFullDisplayName()}</td>
		<td class="align-middle {if !$syllabus && !$submission->file}table-warning{else}{/if}" style="height: 6rem;">
			{if $syllabus && !$submission->file}
				<a href="syllabus/{$syllabus->id}">
					<img src="assets/images/placeholder-4.jpg" class="img-thumbnail mr-2" alt="{$syllabus->title}" data-src="syllabus/{$syllabus->id}/thumbinfo" id="syllabus-{$syllabus->id}" style="max-height: 6rem; min-height: 5rem;border:5px solid #efefef;">
					{$syllabus->title}
				</a>
				{if $submittedCourseId && $courseSection->id == $submittedCourseId}
					<strong class="text-success ml-3">Submitted!</strong>
				{/if}
			{elseif !$syllabus && $submission->file->id}
				Syllabus uploaded as file: <a href="{$submission->file->getFileSrc(true)}">{$submission->file->remoteName}</a>
			{elseif $syllabus && $submission->file->id}
				Syllabus uploaded as file: 
					<a href="{$submission->file->getFileSrc(true)}">{$submission->file->remoteName}</a>.<br />
				Syllabus with title 
					<a href="syllabus/{$syllabus->id}">{$syllabus->latestVersion->title}</a> available to be submitted as well.
			{else}
				<em>No syllabus associated with this course. Use the <a href="syllabi?mode=courses&f={$courseSection->id}">Courses</a> tab to create a new syllabus.</em>
			{/if}
		</td>
		<td class="align-middle text-center">
		{if $submission && $syllabus}
			<span data-toggle="tooltip" data-placement="top" title="{$submission->getStatusHelpText($submission->status)}">
				<i class="far fa-question-circle mr-1 text-muted"></i>
				<u class="
					{if $submission->status == 'pending'}
						text-info
					{elseif $submission->status == 'approved'}
						text-success
					{elseif $submission->status == 'denied'}
						text-danger
					{/if} 
					text-uppercase">{$submission->status|ucfirst}</u>
			</span>
		{elseif $submission}
			<span data-toggle="tooltip" data-placement="top" title="{$submission->getStatusHelpText($submission->status)}{if $submission->status == 'open'}. You may create a new syllabus for this course or upload your own.{/if}">
				<i class="far fa-question-circle mr-1 text-muted"></i>
				<u class="text-dark text-uppercase">{$submission->status|ucfirst}</u>
			</span>
		{else}
			No submission required
		{/if}
		</td>
	<form action="syllabus/submissions" method="post">
		<td class="text-center align-middle">
			{if $submission->status == 'open'}
				{if $syllabus}
					<input name="command[submit][{$syllabus->id}]" type="submit" class="btn btn-dark btn-sm btn-block mb-3" value="Submit">
				{/if}
				<a href="syllabus/submissions/file?upload=true&c={$courseSection->id}" class="d-block mt-3">
					<i class="fas fa-file-upload mr-2"></i>
					Upload a Syllabus...
				</a>
			{elseif $submission->status == 'pending'}
				<a href="syllabus/submissions/{$submission->id}" class="btn btn-info btn-sm">Review</a>
			{elseif $submission->status == 'approved'}
				<a href="syllabus/submissions/{$submission->id}" class="btn btn-success btn-sm">Review</a>
			{elseif $submission->status == 'denied'}
				<input name="command[submit][{$syllabus->id}]" type="submit" class="btn btn-dark btn-sm btn-block mb-3" value="Re-Submit">
			{else}
				&mdash;&mdash;
			{/if}
		</td>
		{generate_form_post_key}
	</form>
	</tr>

{/foreach}
		</tbody>
	</table>

	</div> 
{/foreach}
</div>

</div>
