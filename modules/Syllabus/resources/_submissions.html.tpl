<div class="container-fluid submissions-container px-0">

<div class="accordion" id="termAccordion">

{foreach $allCourses as $term => $myCourses}

	<div class="d-block w-100 p-3 mt-3 bg-secondary ">
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
				<th scope="col" class="text-dark border-left-0" style="width:35%;">Course Information</th>
				<th scope="col" class="text-dark ">Syllabus Details</th>
			</tr>
		</thead>
		<tbody >

{foreach $myCourses as $i => $course}

	{assign var=syllabus value=$courseSection->courseSyllabus}

	<tr class="">
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
							<img src="{if $courseSection->imageUrl}{$courseSection->imageUrl}{else}assets/images/placeholder-4.jpg{/if}" class="img-thumbnail paper paper-bottom" alt="Syllabus thumbnail" style="max-height: 12rem; min-height: 10rem;border:2px solid #efefef;">
							</div>
						</div>
						<div class="col-xl-6 col-lg-6 col-md-5 col-sm-4 col-xs-10 d-block">
							<a style="max-width:200px;" class="btn btn-dark d-block align-top mt-3" href="syllabus/{$syllabus->id}/view">
								<span class="float-left"><i class="fas fa-eye"></i></span>
								View
							</a>
							<a style="max-width:200px;" href="syllabus/{$syllabus->id}" class="my-3 btn btn-info d-block align-bottom">
								<span class="float-left"><i class="fas fa-edit"></i></span>
								Edit
							</a>
							<span class="d-block my-3">{include file="partial:_shareWidget.html.tpl"}</span>
						</div>
					</div>
				</div>
			</div>
		{else}
		<form method="post" action="{$smarty.server.REQUEST_URI|escape}" id="coursesPageForm{$i}">
			{if $syllabus}<input type="hidden" name="course[{$courseSection->id}][syllabusId]" value="{$syllabus->id}">{/if}
			<p class="border-bottom pb-2">
				<em>This class currently does not have a syllabus associated with it.</em>
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
						{if $pastCourse->semester}
						<option value="{$pastCourse->syllabus->id}">[{$pastCourse->getShortName(true)}] {$pastCourse->syllabus->title}</option>
						{else}
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

{/foreach}
		</tbody>
	</table>

	</div> 
{/foreach}
</div>

</div>
