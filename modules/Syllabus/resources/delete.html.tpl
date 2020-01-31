<div class="container-fluid">
<form class="form-horizontal" action="{$smarty.server.REQUEST_URI|escape}" method="post">
    <div class="data-form">
    	<h1 class="pb-2">Delete {if !$organization}Syllabus{else}{$organization->name} Template{/if}</h1>
        <div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
    	<div class="form-group py-3 mt-4">
    		<div class="col-xs-9">
		    	<div class="alert alert-danger reset-warning">
			        <span class="fas fa-exclamation-sign" aria-hidden="true"></span>
			        <span class="sr-only">Alert:</span>
			        Are you sure you want to delete this {if !$organization}Syllabus{else}{$organization->name} Template{/if}?
		    	</div>
            {if $hasDownstreamSyllabiSection}
                <p>
                    Note, any other syllabi that have been cloned from this syllabus will <strong>not</strong> be affected by this delete action.
                </p>
            {/if}
    		</div>
    	</div>
        <div class="row pt-3">
            <div class="col-xl-5 col-lg-6 col-md-12">
                <h2 class=" pb-3">{$syllabusVersion->title}</h2>
            <dl class="mb-4">
            {if $courseInfoSection}
                <dt>{$courseInfoSection->title}</dt>
                <dd>{$courseInfoSection->resolveSection()->classDataCourseSection->getFullSummary()}</dd>
            {/if}
            {if $syllabusVersion->description}
                <dt>Syllabus Description</dt>
                <dd>{$syllabusVersion->description}</dd>
            {/if}
                <dt>Last Modified</dt>
                <dd>{$syllabusVersion->createdDate->format('F jS, Y - h:i a')}</dd>
            {if $syllabusVersion && $syllabusVersion->getCourseInfoSection() && $syllabusVersion->getCourseInfoSection()->resolveSection()}
                {assign var=courseInfoSection value=$syllabusVersion->getCourseInfoSection()->resolveSection()}
                {if $courseInfoSection && $courseInfoSection->classDataCourseSection && $activeStudents > 0}
                <dt>Syllabus Activity Estimation</dt>
                <dd>
                    Approximately {$activeStudents} out of {count($courseInfoSection->classDataCourseSection->enrollments) - 1} students have accessed the syllabus this semester.
                </dd>
                {/if}
            {/if}
            </dl>
            </div>
            <div class="col-xl-7 col-lg-6 col-md-12 mb-5">
                <div class="" style="text-align:center;justify-content: center;">
                    <img src="assets/images/placeholder-4.jpg" data-src="syllabus/{$syllabus->id}/thumbinfo" id="syllabus-{$syllabus->id}" alt="{$syllabus->title}" class="img-fluid border border-light text-center" style="border-width:5px !important;width:350px;">
                    <div class="card-footer bg-white border-0">
                        <small class="d-block"><em class="text-muted">This preview is from when the syllabus was last edited.</em></small>
                        <div class="mt-3">
                            <a class="px-3" href="{$routeBase}syllabus/{$syllabus->id}/word"><i class="far fa-file-word"></i> Download as Word</a>
                            <a class="px-3" href="{$routeBase}syllabus/{$syllabus->id}/print"><i class="fas fa-print"></i></i> Print</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="commands pt-5">
            {generate_form_post_key}
            <input class="btn btn-danger" type="submit" name="command[deletesyllabus][{$syllabus->id}]" value="Delete" />
            <a class="btn btn-default" href="{$routeBase}">Cancel</a>
        </div>
    </div>
</form>
</div>