<div class="container p-3">


<h1>Connect Syllabus to iLearn Course</h1>
<div class="wrap pb-2 mb-3"><div class="left"></div><div class="right"></div></div>
<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" id="ilearnChooseStartingPoint">
{generate_form_post_key}

<input type="hidden" name="course" value="{$courseSection->id}">
{if $courseSection->syllabus->file}
<input type="hidden" name="existingFileSyllabus" value="{$courseSection->syllabus->id}">
{/if}

<p class="text-dark">
	You are connecting a syllabus to the iLearn course <strong>{$courseSection->fullDisplayName}</strong>, 
	for the <strong>{$courseSection->term}</strong> semester.<br><br>
	<strong>Please choose an option for your course syllabus:</strong>
</p>

<div class="container-fluid">

	<div class="border rounded mb-3 bg-light">
		<label for="base">
			<div class="media p-3">
			  <input id="base" type="radio" name="option" value="base" class="align-self-center mr-4">
			  <i class="fas fa-file-alt fa-5x align-self-center mr-3 d-md-inline d-sm-none text-primary"></i>
			  <div class="media-body">
			    <h5 class="mt-0">University base template</h5>
			    <p class="mb-0">Start fresh with a new syllabus draft, which includes all SF State requirements.</p>
			  </div>
			</div>
		</label>
		<div class="text-center pb-3" id="ilearnStartBase" style="display:none">
			<input class="btn btn-success" type="submit" name="command[start][university]" value="Start From Base Template">
		</div>
	</div>

	{if $courseSection->syllabus && !$courseSection->syllabus->file}
	<div class="border rounded mb-3 bg-light">
		<label for="existing">
			<div class="media p-3 mt-2">
			  <input id="existing" type="radio" name="option" value="existing" class="align-self-center mr-4" checked selected>
			  <img src="assets/images/placeholder-4.jpg" class="align-self-center mr-3 d-md-inline d-sm-none img-thumbnail paper paper-top" alt="{$courseSection->syllabus->title}" data-src="syllabus/{$courseSection->syllabus->id}/thumbinfo" id="syllabus-{$courseSection->syllabus->id}" style="max-width: 60px">
			  <div class="media-body">
			    <h5 class="mt-0">Use existing syllabus: {$courseSection->syllabus->title}</h5>
			    <p class="mb-0">
			    	You have already created an online syllabus for this course. Choose this option to connect it to iLearn.
			    </p>
			  </div>
			</div>
		</label>
		<div class="text-center pb-3" id="ilearnStartExisting" style="display:none">
			<input type="hidden" name="existingSyllabus" value="{$courseSection->syllabus->id}">
			<input class="btn btn-success" type="submit" name="command[existing]" value="Choose">
		</div>
	</div>
	{/if}

	<div class="border rounded mb-3 bg-light">
		<label for="upload">
			<div class="media p-3">
			  <input id="upload" type="radio" name="option" value="upload" class="align-self-center mr-4" {if $courseSection->syllabus->file}checked selected{/if}>
			  <i class="fas fa-file-upload align-self-center fa-5x mr-3 d-md-inline d-sm-none text-secondary"></i>
			  <div class="media-body">
			    <h5 class="mt-0">Upload a syllabus file</h5>
			    <p class="mb-0">
			    	You may upload your existing syllabus document here.<br>
			    	<span class="text-dark font-w600">
			    		This will not convert a Word doc to an online syllabus, but will be linked to your iLearn course.
			    	</span>
			    </p>
			  </div>
			</div>
		</label>

	</div>

	

</div>
</form>

<div class="card border-0 col-xl-8 col-lg-10 col-md-12 offset-xl-2 offset-lg-2 mb-3" id="ilearnUploadSyllabus" style="display:none">
	<div class="row no-gutters ">
		<div class="col-md-12 p-3">
			{if $courseSection->syllabus->file}
				<a href="files/{$courseSection->syllabus->file_id}/download" id="uploadedSyllabusFile">
					{$courseSection->syllabus->file->remoteName}
				</a>
			{else}
				<a href="" id="uploadedSyllabusFile"></a>
			{/if}
			<p>
				Drag and drop or click "Choose File" to begin uploading your syllabus document. <strong>Allowed file types are:
				docx, doc, or pdf.</strong>
			</p>
			{include file="{$ctrl->getDragDropUploadFragment()}" action="syllabus/{$courseSection->id}/upload?c={$courseSection->id}" singleFile=true uploadedBy={$viewer->id}}
		</div>
	</div>   
</div>

</div>
