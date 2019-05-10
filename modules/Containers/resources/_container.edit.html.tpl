<!-- Container Section -->
<div class="card card-outline-secondary rounded-0">
    <div class="card-body">
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label"></label>
            <div class="col-lg-9">
                <a href="{$smarty.server.REQUEST_URI}" class="btn btn-secondary">Cancel</a>
                <input class="btn btn-primary" type="submit" name="command[savesection]" value="Save Section" />
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
<!-- End Container Section -->