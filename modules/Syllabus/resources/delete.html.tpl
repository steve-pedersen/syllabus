<div class="container">
<form class="form-horizontal" action="{$smarty.server.REQUEST_URI|escape}" method="post">
    <div class="data-form">
    	<h1>Delete {if !$organization}Syllabus{else}{$organization->name} Template{/if}</h1>
    	<div class="form-group py-3 ">
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
                <p>
                {if $courseInfoSection}
                    <strong>{$courseInfoSection->title}:</strong>
                    {$courseInfoSection->resolveSection()->classDataCourseSection->getFullSummary()}<br>
                {/if}
                    <strong>Description: </strong>{$syllabusVersion->description}
                    <br><strong>Last modified: </strong> {$syllabusVersion->createdDate->format('F jS, Y - h:i a')}
                </p>
            </div>
            <div class="col-xl-7 col-lg-6 col-md-12 mb-5">
                <div class="" style="text-align:center;justify-content: center;">
                    <img src="{$syllabus->imageUrl}" alt="{$syllabus->title}" class="img-fluid border border-light text-cente" style="border-width:5px !important;width:350px;">
                    <div class="card-footer bg-white border-0">
                        <small class="d-block"><em class="text-muted">This preview is from when the syllabus was last edited.</em></small>
                        <div class="mt-3">
                            <a class="px-3" href="#"><i class="far fa-file-word"></i> Download as Word</a>
                            <a class="px-3" href="#"><i class="fas fa-print"></i></i> Print</a>
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