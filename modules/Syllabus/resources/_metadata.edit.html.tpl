<!-- Metadata Section - Edit -->
<div class="card card-outline-secondary rounded-0">
    <div class="card-body">
        <form action="{$smarty.server.REQUEST_URI}" method="post" class="form section-editor" role="form" autocomplete="off" id="sectionForm">
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
                    <input type="reset" class="btn btn-secondary" value="Cancel">
                    <input class="btn btn-primary" type="submit" name="command[savesyllabus]" value="Save Metadata" />
                </div>
            </div>
            {generate_form_post_key}
        </form>
    </div>
    {if $syllabus->dateModified || $syllabus->dateCreated}
    <div class="card-footer text-muted">
        {if $syllabus->dateCreated}<small class="text-muted">Date created - {$syllabus->dateCreated}</small>{/if}
        {if $syllabus->dateModified}<small class="text-muted">Last edited - {$syllabus->dateModified}</small>{/if}
    </div>
    {/if}
</div>
<!-- End Metadata Section - Edit -->