<div class="container" id="shareContainer">
<h1 class="border-bottom pb-3">Share Syllabus</h1>

<div class="data-form">	
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
        </div>
        <div class="col-xl-5 col-lg-6 col-md-12 mb-1">
            <div class="" style="text-align:center;justify-content: center;">
                <img src="{$syllabus->imageUrl}" alt="{$syllabus->title}" class="img-fluid border border-light text-cente" style="border-width:5px !important;width:350px;">
                <div class="card-footer bg-white">
                    <small class="d-block"><em class="text-muted">This preview is from when the syllabus was last edited.</em></small>
                    <div class="mt-3">
                        <a class="px-3" href="{$routeBase}syllabus/{$syllabus->id}/word"><i class="far fa-file-word"></i> Download as Word</a>
                        <a class="px-3" href="{$routeBase}syllabus/{$syllabus->id}/print"><i class="fas fa-print"></i></i> Print</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2>Sharing</h2>
    <p>
        With this sharing option, you are able to choose whether or not your enrolled students can view your syllabus. Turning regular sharing off is recommended for when you are still creating your syllabus. With sharing turned on you will get the syllabus view link, which you can post in iLearn, your personal website, or wherever you please.
    </p>
    <div class="mb-4">
        <div class="col border rounded px-4 pt-4 pb-1" style="">
            <h3>Share with:</h3>   
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

    <p class="alert alert-light text-right mt-5">
        <a href="https://athelp.sfsu.edu/hc/en-us/articles/360033902033-Making-a-syllabus-available-to-students#linking-ilearn" target="_blank" class="alert-link text-dark">
            Learn how to link your syllabus in iLearn <i class="fas fa-external-link-alt ml-2"></i>
        </a>
    </p>
</div>


<div class="border-top pt-5 mt-5">
    <h2>Advanced Sharing</h2>
    <p>Advanced sharing is the place to create unique public links, grant read and/or edit access to other users, and manage your share settings.</p>
    <div class="mb-4">
        <form action="{$smarty.server.REQUEST_URI|escape}" method="post" class="form">
        <div class="col border rounded px-4 pt-4 pb-1" style="">
            <h3>Share link - <small>anyone with link can <u>view</u></small></h3>   
            <div class="form-row p-3">
                <input 
                    id="shareLinkEnabled" 
                    type="checkbox" {if $shareLinkEnabled}checked{/if} 
                    data-toggle="toggle" 
                    data-onstyle="primary" 
                    data-on="Enabled" 
                    data-off="Disabled" 
                    data-style="slow" 
                    data-width="115"
                    data-height="38"
                    name="shareLinkEnabled" 
                    onChange="this.form.submit()"
                    aria-describedby="shareLinkEnabledHelpBlock">
                <input type="hidden" name="command[toggleshare]">
                <label for="shareLinkEnableds" class="pt-2 ml-3">Enable share link?</label>
            </div>
            <div class="form-row">
                <div class="col">
                <span id="shareLinkEnabledHelpBlock" class="form-text text-muted">
                    When the share link is enabled, your syllabus is <strong>viewable by anyone with the link.</strong> Changing this option to Disabled will prevent all users from accessing your syllabus via the share link.
                </span>
                </div>
            </div>
            {if $shareLinkEnabled}
            <!-- <div class="jumbotron mt-4 pt-5 pb-2" style="background:#EAF2FF;"> -->
            <div class="jumbotron mt-4 pt-5 pb-2 bg-light" >
                <div class="input-group mb-2">
                    <input style="font-family:monospace;" form="" class="form-control" type="text" value="{$shareLink}" id="clickToCopy2" />
                    <div class="input-group-append">
                        <button class="btn btn-outline-dark" form="" type="button" id="copyBtn">
                            <i class="far fa-copy mr-1"></i> Copy to clipboard
                        </button>
                    </div>
                </div>  
                <div class="text-right w-100 font-w700 text-success" id="copiedAlert" style="opacity:0;">
                    <span class="ml-auto">Link copied!</span>
                </div>   
                <input form="" type="hidden" value="{$shareLink}" name="shareLink" id="shareLink">    
            </div>
            {/if}
        </div>
        {generate_form_post_key}
        </form>
    </div>
</div>

</div>





























