<div class="container" id="shareContainer">
<form class="form-horizontal" action="{$smarty.server.REQUEST_URI|escape}" method="post">
    <div class="data-form">
    	<h1>Share Syllabus</h1>
        <div>
            <p>To share your syllabus with students, follow these steps:</p>
            <ol>
                <li>Change the share status below to 'All enrolled in course'</li>
                <li>Copy the View syllabus link and paste it wherever you prefer, such as in your iLearn course as a URL resource</li>
            </ol> 
            <hr>
        </div>
        {if $shareLevel == 'all'}
        <div class="my-3 pt-4 pb-2 jumbotron">
            <label class="" for="clickToCopy">
                Use the following link to share your syllabus with <em>all enrolled in the course</em>:
            </label>
            <div class="input-group mb-1">
              <input form="" class="form-control" type="text" value="{$viewUrl}" id="clickToCopy" />
              <div class="input-group-append">
                <button class="btn btn-outline-dark" onClick="copyToClipboard(this)" form="" type="button" id="copyBtn"><i class="far fa-copy mr-1"></i> Copy to clipboard</button>
              </div>
            </div>  
            <div class="text-right w-100 font-w700 text-success" id="copiedAlert" style="opacity:0;">
                <span class="ml-auto">Link copied!</span>
            </div>   
            <input form="" type="hidden" value="{$viewUrl}" name="viewUrl" id="viewUrl">
        </div>
        {/if}
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
    <hr>
<p class="alert alert-secondary text-right">
    <a href="https://athelp.sfsu.edu/hc/en-us/articles/360033902033-Making-a-syllabus-available-to-students#linking-ilearn" target="_blank" class="alert-link">
        Learn how to link your syllabus in iLearn <i class="fas fa-external-link-alt ml-2"></i>
    </a>
</p>
</form>
</div>