
<div class="container mt-2">

<h1>Upload a file syllabus</h1>
<div class="wrap pb-2 mb-3"><div class="left"></div><div class="right"></div></div>
<!-- <form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" id="ilearnChooseStartingPoint">
{generate_form_post_key}

<input type="hidden" name="course" value="{$courseSection->id}">
{if $courseSection->syllabus->file}
<input type="hidden" name="existingFileSyllabus" value="{$courseSection->syllabus->id}">
{/if} -->

<p class="text-dark">
	You are uploading a file syllabus for <strong>{$courseSection->fullDisplayName}</strong>, 
	for the <strong>{$courseSection->term}</strong> semester.<br><br>

</p>

	<div class="row no-gutters ">
		<div class="col-md-12 p-3">
			<div class="py-3">
			{if $courseSection->syllabus->file}
				<span class="mr-2">Existing file: </span>
				<a href="files/{$courseSection->syllabus->file_id}/download" id="uploadedSyllabusFile">
					{$courseSection->syllabus->file->remoteName}
				</a>
				<span class="ml-3">
					<a class="text-danger" href="syllabus/{$courseSection->syllabus->id}/delete?return=syllabus/{$courseSection->id}/{if $userCameFromIlearn}ilearn{else}upload{/if}">
						<i class="fa fa-trash"></i> Delete
					</a>
				</span>
			{else}
				<a href="" id="uploadedSyllabusFile"></a>
				<span class="ml-3" id="deleteUpload" style="display:none;">
					<a class="text-danger" href="syllabus/{$courseSection->syllabus->id}/delete?return=syllabus/{$courseSection->id}/{if $userCameFromIlearn}ilearn{else}upload{/if}">
						<i class="fa fa-trash"></i> Delete
					</a>
				</span>
			{/if}				
			</div>


			<p>
				Drag and drop or click "Choose File" to begin uploading your syllabus document. <strong>Allowed file types are:
				docx, doc, or pdf.</strong>
			</p>
			{include 
				file="{$ctrl->getDragDropUploadFragment()}" action="syllabus/{$courseSection->id}/upload?c={$courseSection->id}" 
				singleFile=true 
				uploadedBy={$viewer->id} 
				fid={$courseSection->syllabus->file->id}
				sid={$courseSection->syllabus->id}
			}

		</div>
	</div>  

</div>