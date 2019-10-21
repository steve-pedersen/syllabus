<div class="container-fluid" id="shareContainer">
<h1 class="pb-2">Review Submission</h1>
<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
<div class="data-form mb-4">	
    {assign var=syllabus value=$submission->syllabus}
    <div class="row pt-3">
        <div class="col-xl-6 col-lg-6 col-md-12">
            {if $submission->syllabus}
                {assign var="syllabusVersion" value=$submission->syllabus->latestVersion}
            {else}
                {assign var="syllabusVersion" value=$submission->syllabus->latestVersion}
            {/if}
            <h2 class=" pb-3">Syllabus for <small>{$courseSection->getFullSummary()}</small></h2>
            <dl class="mb-4">

                <dt>Syllabus Title</dt>
            {if $submission->syllabus_id}
                <dd>
                    <a href="syllabus/{$submission->syllabus->id}">{$submission->syllabus->latestVersion->title}</a>
                </dd>
            {else}
                <dd><a href="{$submission->fileSrc}">{$submission->file->remoteName}</a></dd>
                <dd><a href="syllabus/submissions/file?upload=true&c={$courseSection->id}" class="btn btn-info">Upload a Different Syllabus</a></dd>
            {/if}

                <dt>Submission Status</dt>
                <dd>{$submission->status|ucfirst} - {$submission->getStatusHelpText($submission->status)}</dd>

            {if $submission->feedback}
                <dt>Submission Feedback</dt>
                <dd class="border rounded p-2"><pre>{$submission->feedback}</pre></dd>
            {/if}

            {if $submission->approvedDate}
                <dt>Submission Approved Date</dt>
                <dd>{$submission->approvedDate->format('F jS, Y - h:i a')}</dd>
            {else}
                <dt>You Submitted On</dt>
                <dd>{$submission->submittedDate->format('F jS, Y - h:i a')}</dd>
            {/if}

            {if $submission->campaign->dueDate}
                <dt>Due Date</dt>
                <dd>{$submission->campaign->dueDate->format('F jS, Y - h:i a')}</dd>
            {/if}

            </dl>

            <div class="my-3 py-3">
            {if $courseSection->syllabus && !$submission->syllabus_id}

                <form action="syllabus/submissions" method="post">
                    <p>
                        You have created a syllabus for this course titled 
                        "<a href="syllabus/{$courseSection->syllabus_id}">
                            {$courseSection->syllabus->latestVersion->title}
                        </a>." 
                        {if $submission->file_id}
                            Click Submit This Syllabus to submit this syllabus instead of the file you uploaded.
                        {/if}
                    </p>
                    <input name="command[submit][{$syllabus->id}]" class="btn btn-dark mb-3" value="Submit This Syllabus">
                    {generate_form_post_key}
                </form>

            {/if}
            </div>

        </div>
        {if $submission->syllabus_id}
        
        <div class="col-xl-6 col-lg-6 col-md-12 mb-1">
            <div class="" style="text-align:center;justify-content: center;">
                <img src="{$syllabus->imageUrl}" alt="{$syllabus->title}" class="img-fluid border border-light text-cente" style="border-width:5px !important;width:350px;">
                <div class="card-footer bg-white border-0">
                    <small class="d-block"><em class="text-muted">This preview is from when the syllabus was last edited.</em></small>
                    <div class="mt-3">
                        <a class="px-3" href="{$routeBase}syllabus/{$syllabus->id}/word"><i class="far fa-file-word"></i> Download as Word</a>
                        <a class="px-3" href="{$routeBase}syllabus/{$syllabus->id}/print"><i class="fas fa-print"></i> Print</a>
                    </div>
                </div>
            </div>
        </div>
        {/if}
    </div>

{if $syllabus->latestVersion->courseInfoSection && $courseSection && $submission->status == 'approved'}
    <h2>Sharing</h2>
    <p>
        With this sharing option, you are able to choose whether or not your enrolled students can view this syllabus. Turning regular sharing off is recommended for when you are still creating your syllabus. With sharing turned on you will get the syllabus view link, which you can post in iLearn, your personal website, or wherever you please.
    </p>
    <div class="mb-4">
        <div class="col border rounded px-4 pt-4 pb-1 share-card" style="">
            <h3 class="pb-2">Share with:</h3>  
            <div class="wrap pb-2"><div class="left"></div><div class="right"></div></div> 
            <div class="form-row p-3">
                <div>
                    {assign var=dataOnText value="<div class='media'><i class='align-self-center mr-2 fas fa-user-check fa-2x'></i><div class='media-body'>All enrolled<br>in course</div></div>"}
                    {assign var=dataOffText value="<div class='media'><i class='align-self-center text-warning mr-3 fas fa-user-lock fa-2x'></i><div class='media-body pr-2'>Only course<br>instructors (private)</div></div>"}
                    {assign var=sharePage value=true}
                    {include file="partial:_shareWidget.html.tpl"}
                </div>
            </div>
            <div class="form-row pb-3">
                <div class="col">
                <span id="shareLinkEnabledHelpBlock" class="form-text text-muted">
                    Click the left slider to change the share status. Click the right dropdown button to get the view link to share with enrolled students & instructors.
                </span>
                </div>
            </div>
        </div>
    </div>
{/if}
</div>


</div>





























