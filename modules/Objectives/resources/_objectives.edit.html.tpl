<!-- Objectives Section -->
<div class="card card-outline-secondary rounded-0">
    <div class="card-body">
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Objectives Title</label>
            <div class="col-lg-9">
                <input class="form-control" type="text" name="section[real][title]" value="{$realSection->title}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Objectives Description</label>
            <div class="col-lg-9">
                <textarea class="form-control wysiwyg wysiwyg-full" type="text" name="section[real][description]" rows="5">{$realSection->description}</textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label"></label>
            <div class="col-lg-9">
                <a href="{$smarty.server.REQUEST_URI}" class="btn btn-secondary">Cancel</a>
                <input class="btn btn-primary" type="submit" name="command[savesection]" value="Save Section" />
                <!-- <input type="button" class="btn btn-primary" value="Save Section"> -->
            </div>
        </div>
    </div>
    {if $syllabus->dateModified || $syllabus->dateCreated}
    <div class="card-footer text-muted">
        {if $syllabus->dateCreated}<small class="text-muted">Date created - {$syllabus->dateCreated}</small>{/if}
        {if $syllabus->dateModified}<small class="text-muted">Last edited - {$syllabus->dateModified}</small>{/if}
    </div>
    {/if}
</div>
<!-- End Objectives Section -->