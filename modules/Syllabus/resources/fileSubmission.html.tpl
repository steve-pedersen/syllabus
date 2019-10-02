<div class="container-fluid">
<h1>
	Submit a syllabus file
</h1>
<div class="wrap pb-2 mb-3"><div class="left"></div><div class="right"></div></div>
<div class="col-xl-8 col-lg-10 col-md-12">
	{if $submission->file}
		<p class="lead">
			You have already uploaded your own syllabus file and submitted it to your department. If you'd like to upload a different file, <strong>it will overwrite the current one</strong>. <br /><br />
			If you'd like to use a syllabus that you created in this application instead of the current file upload, go back to the <a href="syllabi?mode=submissions"><u>submissions page</u></a> and choose "Submit Now" for this course's syllabus. Note, this will remove the current uploaded file.
		</p>
	{else}
		<p class="lead">
			You have opted to upload your own syllabus instead of creating one for this course in this application. If this is a mistake, go back to the <a href="syllabi?mode=submissions"><u>submissions page</u></a> and choose "Submit Now" for this course's syllabus.
		</p>
	{/if}
	<p class="alert alert-primary">
		You are uploading a syllabus for <strong>{$courseSection->getFullSummary()}</strong>.
	</p>
</div>
<div class="card my-5  border-0 col-xl-8 col-lg-10 col-md-12" style="">
	<div class="row no-gutters ">
		{if $submission}
		<div class="col-md-12">
			<strong>Current file: </strong> 
			{if !$submission->file_id}
				<a id="submissionFile" href=""></a>
			{else}
				<a id="submissionFile" href="{$submission->getFileSrc(true)}">{$submission->file->remoteName}</a>
			{/if}
		</div>
		{/if}
		<div class="col-md-12">
			<p>
				Drag and drop or click "Choose File" to begin uploading your syllabus document. <strong>Allowed file types are:
				docx, doc, or pdf.</strong>
			</p>
			{include file="{$ctrl->getDragDropUploadFragment()}" action="syllabus/submissions/file?c={$courseSection->id}" singleFile=true uploadedBy={$account->id}}
		</div>
	</div>   

</div>