<!-- Metadata Section - Edit -->
<div class="card card-outline-secondary rounded-0">
    <div class="card-body">
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Syllabus Title</label>
            <div class="col-lg-9">
                <input form="viewSections" class="form-control" type="text" name="syllabus[title]" value="{$syllabusVersion->title}" required>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Description</label>
            <div class="col-lg-9">
                <input form="viewSections" class="form-control" type="text" name="syllabus[description]" value="{$syllabusVersion->description}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label"></label>
            <div class="col-lg-9">
                {if $syllabusVersion->id}
                    <input type="hidden" name="syllabusVersion[id]" value="{$syllabusVersion->id}" form="viewSections">
                {/if}
                <input class="btn btn-primary" type="submit" name="command[savesyllabus]" value="Save Metadata" form="viewSections" />
                <a href="{$smarty.server.REQUEST_URI}" class="btn btn-outline-dark">Cancel</a>
            </div>
        </div>
    {if $syllabusVersion && $syllabusVersion->getCourseInfoSection()}
        {assign var=courseInfoSection value=$syllabusVersion->getCourseInfoSection()->resolveSection()}
        {if $courseInfoSection && $courseInfoSection->classDataCourseSection}
        <div class="col">
            <hr>
            <p class="">
                This syllabus is being used for <strong>{$courseInfoSection->classDataCourseSection->getFullSummary()}</strong>.
            </p>
        </div>
        {/if}
    {/if}
    </div>
    {if $syllabusVersion->createdDate}
    <div class="card-footer text-muted">
        <span class="text-dark">Last modified on {$syllabusVersion->createdDate->format('M j, Y - h:ia')}</span>
    </div>
    {/if}
</div>
<!-- End Metadata Section - Edit -->