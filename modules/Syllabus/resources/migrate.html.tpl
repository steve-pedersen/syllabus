<div class="container-fluid p-5">
<h1 class="mb-3">Migrate old Syllabus content</h1>

{if $newSyllabus}
<div class="my-5 pb-3 border-bottom">
    <p class="lead">Your new Syllabus has been created!</p>
    <p class="alert alert-success">
        You can find your new syllabus by clicking <a class="alert-link" href="syllabi">My Syllabi</a> in the main menu.
    </p>
    {if $errors && !empty($errors)}
    <p>There were {count($errors)} migration items that require your review:</p>
    <ul>
        {foreach $errors as $error}<li>{$error}</li>{/foreach}
    </ul>
    {/if}
</div>
{/if}

<!-- <h2>Begin new migration here</h2> -->
<div class="migrate-text-container w-75">
<p class="">
	This migration tool allows you to automatically create new syllabi from your old content.  Simply select a backup file exported from the old Syllabus application, and upload. Any syllabus content migrated and uploaded into the new system should be reviewed for accuracy and formatting.
</p>
<p class="">
	Note: The migration will be executed as soon as you select “Upload.” This process make take a few moments.
</p>
</div>

<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" enctype="multipart/form-data">
    <div class="form-group upload-form my-5">
        <label for="backup" class="field-label field-linked"><strong>Upload Syllabus backup file</strong></label>       
        <input class="form-control-file w-75" type="file" name="file" id="backup" />
        
        <div class="col-xs-12 help-block text-center">
            <p id="type-error" class="bg-danger" style="display:none"><strong>There was an error with the type of file you are attempting to upload.</strong></p>
        </div>          
    </div>
    <div class="form-group mt-5">
        <div class="col-xs-12">
            <input type="submit" name="command[upload]" id="importFile" value="Upload" class="btn btn-primary" />
        </div>
    </div> 
{generate_form_post_key}
</form>
</div>