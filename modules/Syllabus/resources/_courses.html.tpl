
<!-- <form method="post" action="{$smarty.server.REQUEST_URI|escape}" id="coursesPageForm"> -->

{foreach $allCourses as $term => $myCourses}
<div class="bg-secondary pt-1 mb-3 rounded">
	<h3 class="text-white p-3">{$term}</h3>
	<table class="table table-bordered table-responsive-sm bg-white shadow-sm" >
		<thead class="thead-light">

			<tr class="">
				<th scope="col" class="text-dark border-left-0" style="width:35%;">Course Information</th>
				<th scope="col" class="text-dark ">Syllabus Details & Options</th>
			</tr>
		</thead>
		<tbody >

	{foreach $myCourses as $i => $courseSection}
			{assign var=syllabus value=$courseSection->courseSyllabus}
			{assign var=submission value=$courseSection->submission}

			<tr class="{if $focus == $courseSection->id}table-info{/if}">
				<td class="align-middle" style="width:35%;">
					<div class="p-3">
					{if $courseSection->classNumber}<h2 class="">{$courseSection->classNumber}</h2>{/if}
					{if $courseSection->sectionNumber}Section {$courseSection->sectionNumber}<br>{/if}
					<strong>{$courseSection->title}</strong><br>
					<span class="badge badge-secondary">{$term}</span>
					{if $syllabus}
	                <p class="my-2 pt-3">
	                    <strong class="mr-1" style="font-weight:900;">Share Status: <br></strong> 
	                    {if $syllabus->shareLevel == 'all'}
	                        <i class="fas fa-user-check text-success mr-1"></i> All enrolled in course
	                    {else}
	                        <i class="fas fa-user-lock text-warning mr-1"></i> Only course instructors (private)
	                    {/if}
	                </p>
					{/if}
					</div>
						
				</td>
				<td style="">
				{if $syllabus}
					<p class="border-bottom pb-2">
						<strong>{$syllabus->title}</strong>
					</p>
					<div class="col">
						<div class="media">
							<div class="media-body row">
								<div class="col-xl-3 col-lg-5 col-md-6 col-sm-7 col-xs-8" >
									<div class=" mb-3">
									{if $syllabus->file}
									<div class="text-center"><i class="fas fa-file fa-5x text-center"></i></div>
									{else}
									<img src="assets/images/placeholder-4.jpg" data-src="syllabus/{$syllabus->id}/thumbinfo" id="syllabus-{$syllabus->id}" class="img-thumbnail paper paper-bottom" alt="Syllabus thumbnail" style="max-height: 12rem; min-height: 10rem;border:2px solid #efefef;">
									{/if}
									</div>
								</div>
								<div class="col-xl-6 col-lg-6 col-md-5 col-sm-4 col-xs-10 d-block">
									{if $syllabus->file}
									<a style="max-width:200px;" href="syllabus/{$syllabus->courseSection->id}/start" class="my-3 btn btn-info d-block align-bottom">
										<span class="float-left"><i class="fas fa-edit"></i></span>
										Edit
									</a>
									{else}
									<a style="max-width:200px;" class="btn btn-dark d-block align-top mt-3" href="syllabus/{$syllabus->id}/view">
										<span class="float-left"><i class="fas fa-eye"></i></span>
										View
									</a>
									<a style="max-width:200px;" href="syllabus/{$syllabus->id}" class="my-3 btn btn-info d-block align-bottom">
										<span class="float-left"><i class="fas fa-edit"></i></span>
										Edit
									</a>
									{/if}
									{if $syllabus->hasCourseSection || $syllabus->file}
									<span class="d-block my-3">{include file="partial:_shareWidget.html.tpl"}</span>
									{elseif !$syllabus->file}
									<span class="d-block my-3">
										You must add a <strong>Course Information</strong> section to this syllabus before it can be shared.
									</span>
									{/if}
								</div>
							</div>
						</div>
					</div>
				{else}
				<form method="post" action="{$smarty.server.REQUEST_URI|escape}" id="coursesPageForm{$i}">
					{if $syllabus}<input type="hidden" name="course[{$courseSection->id}][syllabusId]" value="{$syllabus->id}">{/if}
					<p class="border-bottom pb-2">
						<em>This class currently does not have a syllabus associated with it.</em>
						{if $submission->file->id}
							You have <a href="{$submission->fileSrc}">uploaded a syllabus (download link)</a> for department submission.
						{/if}
					</p>
					
					{if $courseSection->pastCourseSyllabi}
					<!-- <p class="">Duplicate a previous syllabus or start from scratch.</p> -->
					{/if}

					<div class="row py-3">
						<div class="col-md-3 d-block-inline">
							<a class="btn btn-success" href="syllabus/start?course={$courseSection->id}">
								<span class="mr-3"><i class="fas fa-plus"></i></span> 
								Create New
							</a>
						</div>

						{if $courseSection->pastCourseSyllabi}
						<div class="col-md-1 divider-div">
							<div class="row row-divided">
								<div class="col-xs-6 column-one ">
								</div>
								<div class="vertical-divider text-center">OR</div>
								<div class="col-xs-6 column-two ">
								</div>
							</div>
						</div>

						<div class="col-md-8 form-group mb-3 d-block-inline">
							<div class="input-group">
							<select name="courseSyllabus" class="form-control " id="course{$i}SyllabusOption">
								<option value="off" default>Choose other syllabus to start from...</option>
							{foreach $courseSection->pastCourseSyllabi as $pastCourse}
								{if $pastCourse->semester && $pastCourse->syllabus->id}
								<option value="{$pastCourse->syllabus->id}">[{$pastCourse->getShortName(true)}] {$pastCourse->syllabus->title}</option>
								{elseif $pastCourse->latestVersion->id}
								<option value="{$pastCourse->id}">[Non-course syllabus] {$pastCourse->latestVersion->title}</option>
								{/if}
							{/foreach}
							</select>
							<div class="input-group-append">
								<input class="btn btn-primary btn-sm" type="submit" name="command[courseClone][{$courseSection->id}]" value="Clone" />
							</div>
							</div>
						</div>
						{/if}
					</div>
					{generate_form_post_key}
				</form>
				{/if}
				</td>
			</tr>
	{foreachelse}
			<tr>
				<td colspan="2" class="notice">
					No courses found for you.
				</td>
			</tr>
	{/foreach}
		</tbody>
	</table>
</div>
{foreachelse}
	<p>No courses found for you.</p>
{/foreach}

