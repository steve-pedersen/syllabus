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
	{if !$courseSection->syllabus || $courseSection->syllabus->file}
	<strong>Please choose an option for your course syllabus:</strong>
	{/if}
</p>

<div class="container-fluid">

	{if $courseSection->syllabus && !$courseSection->syllabus->file}
	<div class="border rounded mb-3 bg-light">
		<label for="existing">
			<div class="media p-3 mt-2">
			  <!-- <input id="existing" type="radio" name="option" value="existing" class="align-self-center mr-4" checked selected> -->
			  <img src="assets/images/placeholder-4.jpg" class="align-self-center mr-3 d-md-inline d-sm-none img-thumbnail paper paper-top" alt="{$courseSection->syllabus->title}" data-src="syllabus/{$courseSection->syllabus->id}/thumbinfo" id="syllabus-{$courseSection->syllabus->id}" style="max-width: 60px">
			  <div class="media-body">
			    <h5 class="mt-0">
			    	Use existing syllabus: 
			    	<a href="syllabus/{$courseSection->syllabus->id}">
			    		{$courseSection->syllabus->title}
			    	</a>
			    </h5>
			    <p class="mb-0">
			    	You have already created an online syllabus for this course. Click publish and return to enable your students to view your syllabus.
			    </p>
			  </div>
			</div>
		</label>
		<div class="text-center pb-3" id="ilearnStartExisting">
			<input type="hidden" name="existingSyllabus" value="{$courseSection->syllabus->id}">
			<input class="btn btn-info" type="submit" name="command[existing]" value="Publish & Return to iLearn">
		</div>
	</div>
	
	{else}
	<div class="border rounded mb-3 bg-light">
		<!-- <label for="base"> -->
		<a href="syllabus/start?course={$courseSection->id}" class="">
			<div class="media p-3">
			  <input id="base" type="radio" name="option" value="base" class="align-self-center mr-4">
			  
			  <i class="fas fa-file-alt fa-5x align-self-center mr-3 d-md-inline d-sm-none text-primary"></i>
			  
			  <div class="media-body align-self-center">
			    <h5 class="mt-0">Start from another syllabus or create a new one</h5>
			    <!-- <p class="mb-0">Start fresh with a new syllabus draft, which includes all SF State requirements.</p> -->
			  </div>
				
			</div>
		</a>
		<!-- </label> -->
		<div class="text-center pb-3" id="ilearnStartBase" style="display:none">
			<input class="btn btn-success" type="submit" name="command[start][university]" value="Start From Base Template">
		</div>
	</div>

	<div class="border rounded mb-3 bg-light">
		<label for="upload">
			<div class="media p-3">
			  <input id="upload" type="radio" name="option" value="upload" class="align-self-center mr-4" {if $courseSection->syllabus->file}checked selected{/if}>
			  <i class="fas fa-file-upload align-self-center fa-5x mr-3 d-md-inline d-sm-none text-secondary"></i>
			  <div class="media-body align-self-center">
			    <h5 class="mt-0">Upload a syllabus file</h5>
			    <p class="mb-0">
			    	<!-- You may upload your existing syllabus document here.<br> -->
			    	<span class="font-w500">
			    		This will create a link to the exact file as uploaded but will not create an online syllabus.
			    	</span>
			    </p>
			  </div>
			</div>
		</label>

	</div>
	{/if}	

</div>
</form>

<div class="card border-0 col-xl-8 col-lg-10 col-md-12 offset-xl-2 offset-lg-2 mb-3" id="ilearnUploadSyllabus" style="display:none">
	<div class="row no-gutters ">
		<div class="col-md-12 p-3">
			<div class="py-3">
			{if $courseSection->syllabus->file}
				<span class="mr-2">Existing file: </span>
				<a href="files/{$courseSection->syllabus->file_id}/download" id="uploadedSyllabusFile">
					{$courseSection->syllabus->file->remoteName}
				</a>
				<span class="ml-3">
					<a class="text-danger" href="syllabus/{$courseSection->syllabus->id}/delete?return=syllabus/{$courseSection->id}/ilearn">
						<i class="fa fa-trash"></i> Delete
					</a>
				</span>
			{else}
				<a href="" id="uploadedSyllabusFile"></a>
				<span class="ml-3" id="deleteUpload" style="display:none;">
					<a class="text-danger" href="syllabus/{$courseSection->syllabus->id}/delete?return=syllabus/{$courseSection->id}/ilearn">
						<i class="fa fa-trash"></i> Delete
					</a>
				</span>
			{/if}				
			</div>

		{if true || !$courseSection->syllabus->file}
			<p>
				Drag and drop or click "Choose File" to begin uploading your syllabus document. <strong>Allowed file types are:
				docx, doc, or pdf.</strong>
			</p>
			{include file="{$ctrl->getDragDropUploadFragment()}" action="syllabus/{$courseSection->id}/upload?c={$courseSection->id}" singleFile=true uploadedBy={$viewer->id} publishAction="syllabus/{$courseSection->id}/publishreturn" fid={$courseSection->syllabus->file->id}}
		{/if}
		</div>
	</div>   
</div>

</div>
