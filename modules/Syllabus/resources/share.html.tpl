<div class="container">
<form class="form-horizontal" action="{$smarty.server.REQUEST_URI|escape}" method="post">
    <div class="data-form">
    	<h1>Share Syllabus</h1>
        <div class="row pt-3">
            <div class="col-xl-7 col-lg-6 col-md-12">
                <h2 class=" pb-3">{$syllabusVersion->title}</h2>
                <p>
                {if $courseInfoSection}
                    <strong>{$courseInfoSection->title}:</strong>
                    {$courseInfoSection->resolveSection()->classDataCourseSection->getFullSummary()}<br>
                {/if}
                {if $syllabusVersion->description}<strong>Description: </strong>{$syllabusVersion->description}{/if}
                    <br><strong>Last modified: </strong> {$syllabusVersion->createdDate->format('F jS, Y - h:i a')}
                </p>
                <p class="my-5 lead alert alert-light d-block">
                    <strong class="mr-3 align-self-middle" style="font-weight:900;">Share Status: </strong> 
                    {if $shareLevel == 'all'}
                        <i class="fas fa-user-check fa-2x text-success mr-3"></i> All enrolled in course
                    {else}
                        <i class="fas fa-user-lock fa-2x text-warning mr-3"></i> Only course instructors (private)
                    {/if}
                </p>
                
                <div class="mt-4 p-4 border border-info rounded">
                    <label for="share"><strong>Share with:</strong></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="share" id="all" value="all" {if $shareLevel == 'all'}checked{/if} onchange="this.form.submit()">
                        <label class="form-check-label pl-2" for="all">
                            All enrolled in course
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="share" id="private" value="private" {if $shareLevel == 'private'}checked{/if} onchange="this.form.submit()">
                        <label class="form-check-label pl-2" for="private">
                            Only course instructors (private)
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-6 col-md-12 mb-5">
                <div class="" style="text-align:center;justify-content: center;">
                    <img src="{$syllabus->imageUrl}" alt="{$syllabus->title}" class="img-fluid border border-light text-cente" style="border-width:5px !important;width:350px;">
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
        <div class="commands my-5">
            {generate_form_post_key}
            <input class="btn btn-success" type="submit" name="command[share]" value="Save Share Settings" />
            <a class="btn btn-default" href="syllabi">Cancel</a>
        </div>
    </div>
</form>
</div>