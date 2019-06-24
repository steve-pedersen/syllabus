
<form method="post" action="{$smarty.server.REQUEST_URI|escape}">

{foreach $allCourses as $term => $myCourses}
<div class="bg-secondary pt-1 mb-3 rounded">
	<h3 class="text-white p-3">{$term}</h3>
	<table class="table table-bordered table-responsive-sm bg-white shadow-sm" >
		<thead class="thead-light">

			<tr class="">
				<th scope="col" class="border-left-0" style="width:35%;">Course Information</th>
				<th scope="col">Syllabus Details & Options</th>
			</tr>
		</thead>
		<tbody >

	{foreach $myCourses as $i => $courseSection}
			{assign var=syllabus value=$courseSection->courseSyllabus}

			{if $syllabus}<input type="hidden" name="course[{$courseSection->id}][syllabusId]" value="{$syllabus->id}">{/if}
			<tr class="">
				<td class="align-middle" style="width:35%;">
					<div class="p-3">
					{if $courseSection->classNumber}<h2 class="">{$courseSection->classNumber}</h2>{/if}
					{if $courseSection->sectionNumber}Section {$courseSection->sectionNumber}<br>{/if}
					<strong>{$courseSection->title}</strong><br>
					<span class="badge badge-secondary">{$term}</span>
					</div>
				</td>
				<td style="">
				{if $syllabus}
					<p class="border-bottom pb-2">
						<strong>{$syllabus->title}</strong>
					</p>
					<div class="col">
						<div class="media">
							<img src="{$courseSection->image}" class="img-fluid" alt="Syllabus thumbnail" style="max-height: 10rem;">
							<div class="media-body">
								<div class="col-md-5 col-sm-4 col-xs-3" >
									<table class="table table-borderless" style="height: 10rem;">
									<tbody>
										<tr>
											<td class="align-top">
											<a class="btn btn-dark d-block" target="_blank" href="syllabus/{$syllabus->id}/view">
												<span class="">
													<span class="float-left"><i class="fas fa-eye"></i></span>
													View
												</span>
											</a>
											</td>
										</tr>
										<tr>
											<td class="align-bottom">
											<a href="syllabus/{$syllabus->id}" class="btn btn-info d-block">
												<span class="">
													<span class="float-left"><i class="fas fa-edit"></i></span>
													Edit
												</span>
											</a>
											</td>
										</tr>
									</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				{else}
					<p class="border-bottom pb-2">
						<em>This class currently does not have a syllabus associated with it.</em>
					</p>
					<div class="col">
						<div class="row py-3">
							<div class="col-3">
								<a class="btn btn-success" href="syllabus/start?course={$courseSection->id}">
									<span class="mr-3"><i class="fas fa-plus"></i></span> 
									Create New
								</a>
							</div>

							{if $courseSection->pastCourseSyllabi}
							<div class="col-1">
								<div class="row row-divided">
									<div class="col-xs-6 column-one ">
									</div>
									<div class="vertical-divider">OR</div>
									<div class="col-xs-6 column-two ">
									</div>
								</div>
							</div>

							<div class="col-8 form-group mb-3 ">
								<div class="input-group">
								<select name="courseSyllabus" class="form-control " id="course{$i}SyllabusOption">
									<option value="" default>Choose past syllabus to start from...</option>
								{foreach $courseSection->pastCourseSyllabi as $pastCourse}
									<option value="{$pastCourse->syllabus->id}">[{$pastCourse->getShortName(true)}] {$pastCourse->syllabus->title}</option>
								{/foreach}
								</select>
								<div class="input-group-append">
									<input class="btn btn-primary btn-sm" type="submit" name="command[courseClone][{$courseSection->id}]" value="Submit" />
								</div>
								</div>
							</div>
							{/if}
						</div>
					</div>
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
	<div>{generate_form_post_key}</div>

</form>
