<!-- Metadata Section - View -->
<div class="card card-outline-secondary rounded-0">
    <div class="card-body">
        <div class="col">
            <h2 class="display-5">{$syllabusVersion->title}</h2>
        </div>
        <div class="col">
            <p class="lead">{$syllabusVersion->description}</p>
        {if $syllabusVersion && $syllabusVersion->getCourseInfoSection() && $syllabusVersion->getCourseInfoSection()->resolveSection()}
            {assign var=courseInfoSection value=$syllabusVersion->getCourseInfoSection()->resolveSection()}
            {if $courseInfoSection && $courseInfoSection->classDataCourseSection}
                <p class="">
                    This syllabus is being used for <strong>{$courseInfoSection->classDataCourseSection->getFullSummary()}</strong>.{if $activeStudents > 0} <br>Approximately {$activeStudents} out of {count($courseInfoSection->classDataCourseSection->enrollments) - 1} students have accessed the syllabus this semester.{/if}
                </p>
                <p>
                    <a href="{$courseInfoSection->classDataCourseSection->id}/logs">View access logs for this syllabus</a>
                </p>
            {/if}
        {/if}
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label"></label>
            <div class="col-lg-9 d-flex flex-row-reverse">
                <a class="btn btn-info" href="{$routeBase}syllabus/{$syllabus->id}?edit=metadata">Edit</a>
            </div>
        </div>
    </div>
    {if $syllabusVersion->createdDate}
    <div class="card-footer text-muted">
        <span class="text-dark">Last modified on {$syllabusVersion->createdDate->format('M j, Y - h:ia')}</span>
    </div>
    {/if}
</div>
<!-- End Metadata Section - View -->