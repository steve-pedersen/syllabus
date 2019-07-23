<div class="container">
<h1 class="mb-3">Migrate old Syllabus content</h1>

{if $newSyllabus}
<div class="my-5 pb-3 border-bottom">
    <h2>Your new Syllabus has been created!</h2>
    <p class="alert alert-success">
        You can find your new syllabus by clicking <a class="alert-link" href="syllabi">My Syllabi</a> in the main menu.
    </p>
</div>
{/if}

<h2>Begin new migration here</h2>
<p>
	This migration allows you to automatically create new syllabi in this new system from your old content. Not everything is the same in this new application, so you will probably still need to add some finishing touches to your migrated syllabi.
</p>
<p>
	Note, the migration will be executed as soon as you upload your backup file. This process may take a few moments.
</p>

<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" enctype="multipart/form-data">
    <div class="form-group upload-form my-5">
        <label for="backup" class="field-label field-linked"><strong>Upload Syllabus backup file</strong></label>       
        <input class="form-control-file" type="file" name="file" id="backup" />
        {foreach item='error' from=$errors.backup}<div class="error">{$error}</div>{/foreach}
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