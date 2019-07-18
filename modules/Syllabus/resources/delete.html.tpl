<div class="col p-5">
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
            <div class="col-4">
                <div class="card w-100">
                    <img src="{$syllabus->imageUrl}" alt="{$syllabus->title}" class="img-fluid border border-light" style="border-width:10px !important;">
                    <div class="card-footer bg-white">
                        <em class="text-muted">This preview is from when the syllabus was last edited.</em>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <h2>{$syllabusVersion->title}</h2>
                <p>{$syllabusVersion->description}</p>
                <p><strong>Date created: </strong> {$syllabus->createdDate->format('F jS, Y - h:i a')}</p>
                <p><strong>Last modified: </strong> {$syllabusVersion->createdDate->format('F jS, Y - h:i a')}</p>
            </div>
        </div>
        <div class="commands my-5 pt-5">
            {generate_form_post_key}
            <input class="btn btn-danger" type="submit" name="command[deletesyllabus][{$syllabus->id}]" value="Delete" />
            <a class="btn btn-default" href="{$routeBase}">Cancel</a>
        </div>
    </div>
</form>
</div>