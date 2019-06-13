<!-- Metadata Section - Edit -->
<div class="card card-outline-secondary rounded-0">
    <div class="card-body">
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Syllabus Title</label>
            <div class="col-lg-9">
                <input class="form-control" type="text" name="syllabus[title]" value="{$syllabusVersion->title}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Description</label>
            <div class="col-lg-9">
                <input class="form-control" type="text" name="syllabus[description]" value="{$syllabusVersion->description}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label"></label>
            <div class="col-lg-9">
                {if $syllabusVersion->id}
                    <input type="hidden" name="syllabusVersion[id]" value="{$syllabusVersion->id}">
                {/if}
                <input class="btn btn-primary" type="submit" name="command[savesyllabus]" value="Save Metadata" />
                <a href="{$smarty.server.REQUEST_URI}" class="btn btn-outline-warning">Cancel</a>
            </div>
        </div>
    </div>
    {if $syllabusVersion->dateCreated}
    <div class="card-footer text-muted">
        <small class="text-muted">Date modified - {$syllabusVersion->dateCreated->format('Y m, d')}</small>
    </div>
    {/if}
</div>
<!-- End Metadata Section - Edit -->