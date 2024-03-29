<div class="container-fluid" id="shareContainer">
<h1 class="pb-2">Share Syllabus</h1>
<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
<div class="data-form mb-4">	
    <div class="row pt-3">
        <div class="col-xl-6 col-lg-6 col-md-12">
            <h2 class=" pb-3">
            {if $syllabus->file}
                {$syllabus->courseSection->shortName}: 
                <a href="files/{$syllabus->file->id}/download">{$syllabus->file->remoteName}</a>        
            {else}
                {$syllabusVersion->title}
            {/if}        
            </h2>
            <dl class="mb-4">
        {if $syllabusVersion && $syllabusVersion->getCourseInfoSection() && $syllabusVersion->getCourseInfoSection()->resolveSection()}
                {assign var=courseInfoSection value=$syllabusVersion->getCourseInfoSection()->resolveSection()}
            
            {if $courseInfoSection && $courseInfoSection->classDataCourseSection}
                <dt>{$courseInfoSection->title}</dt>
                <dd>{$courseInfoSection->classDataCourseSection->getFullSummary()}</dd>
            {/if}
        {/if}
            {if $syllabusVersion->description}
                <dt>Syllabus Description</dt>
                <dd>{$syllabusVersion->description}</dd>
            {/if}
            {if $syllabusVersion->createdDate}
                <dt>Last Modified</dt>
                <dd>{$syllabusVersion->createdDate->format('F jS, Y - h:i a')}</dd>
            {/if}

                {if $courseInfoSection && $courseInfoSection->classDataCourseSection && $activeStudents > 0}
                <dt>Syllabus Activity Estimation</dt>
                <dd>
                    Approximately {$activeStudents} out of {count($courseInfoSection->classDataCourseSection->enrollments) - 1} students have accessed the syllabus this semester.
                </dd>
                {/if}

            </dl>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12 mb-1">
            <div class="" style="text-align:center;justify-content: center;">
                {if $syllabus->file}
                    <div class="text-center"><i class="fas fa-file fa-5x text-center"></i></div>
                {else}
                <img src="assets/images/placeholder-4.jpg" data-src="syllabus/{$syllabus->id}/thumbinfo" id="syllabus-{$syllabus->id}" alt="{$syllabus->title}" class="img-fluid border border-light text-cente" style="border-width:5px !important;width:350px;">
                <div class="card-footer bg-white border-0">
                    <small class="d-block"><em class="text-muted">This preview is from when the syllabus was last edited.</em></small>
                    <div class="mt-3">
                        <a class="px-3" href="{$routeBase}syllabus/{$syllabus->id}/word"><i class="far fa-file-word"></i> Download as Word</a>
                        <a class="px-3" href="{$routeBase}syllabus/{$syllabus->id}/print"><i class="fas fa-print"></i> Print</a>
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </div>

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
</div>


<div class="border-tops pt-5 mt-5">
    <h2>More Sharing Options</h2>
    <p>Advanced sharing is the place to create unique public links, grant read or edit access to other users, and manage your share settings.</p>
    <div class="mb-4">
        <form action="{$smarty.server.REQUEST_URI|escape}" method="post" class="form">
        <div class="col border rounded px-4 pt-4 pb-1 share-card" style="">
            <h3 class="pb-2">Share link - <small>anyone with link can <u>view</u></small></h3> 
            <div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
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
            <div class="form-row mb-2">
                <div class="col">
                <span id="shareLinkEnabledHelpBlock" class="form-text text-muted">
                    When the share link is enabled, your syllabus is <strong>viewable by anyone with the link.</strong> Changing this option to Disabled will prevent users from accessing your syllabus via the share link.
                </span>
                </div>
            </div>
            {if $shareLinkEnabled}
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

    {if !$syllabus->file}
    <div class="mb-4">
        
        <div class="col border rounded px-4 py-4 share-card" style="" id="grantEditAccess">
            <h3 class="pb-2">Grant Edit Access</h3> 
            <div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
            <div class="form-row p-3">
                <p>
                    Add users as editors on an ad hoc basis for this particular syllabus. Search for a user who has an account in Syllabus. If they do not yet have an account or you are unsure if they do, then they must first sign into Syllabus–this will generate an account for them.
                </p>
            {if $adHocRoles}
                <div class="col">
                    <h4>Current ad hoc users of this syllabus:</h4>
                    <form method="post" action="{$smarty.server.REQUEST_URI}">
                    
                    <div class="px-1 pt-3">
                    {assign var=currentRole value=""}
                    <ul class="list-group">
                    {foreach $adHocRoles as $i => $adHocRole}
                        {if $adHocRole['users'] && $adHocRole['role']->name != $currentRole}
                            <p class="ml-0">Users with <strong>{$adHocRole['role']->name}</strong> access:</p>
                            {assign var=currentRole value=$adHocRole['role']->name}
                        {/if}
                        
                        {if $adHocRole['users']}
                        <li class="ml-3 list-group-item">
                        {foreach $adHocRole['users'] as $user}
                            {if $user}
                        <div class="row ">
                            <div class="col-9 pt-1">
                            	{$user->fullName} <span class="ml-3">({$user->emailAddress})</span>
							{if $adHocRole['expiration']}
                            	<span class="text-bold mx-3">
                            		Expires in {$adHocRole['expiration']}
                            	</span>
                            {/if}
                            </div>
                            <div class="col-2 ">
                            	<input type="submit" name="command[remove][{$user->id}]" value="Revoke" class="ml-3 btn btn-danger btn-sm">
                            	<input type="hidden" name="role[{$user->id}][{$adHocRole['role']->id}]" value="{$adHocRole['role']->id}">
                            </div>
                        </div>
                            {/if}
                        {/foreach}
                        </li>
                        {/if}
                    {/foreach}
                    </ul>
                    </div>
                    {generate_form_post_key}
                    </form>
                </div>
            {/if}
            </div>
            <div class="form-row p-3">
                <div class="col">
                    <!-- <form class="form" action="{$smarty.server.REQUEST_URI}" method="get" id="user-lookup"> -->
                    <form class="form" action="{$smarty.server.REQUEST_URI}" method="post" id="addEditor">
                        <div class="form-group ">
                            <label class="" for="query">Search for user by name, SF State ID, or email, then select them to "Add as Editor"</label>
                            <input class="form-control syllabus-account-autocomplete" id="user-lookup" type="text" name="query[]" id="query" />
                            <input type="hidden" name="record" value="0">
                        </div>        
                        <div class="search-container"></div>
                    <!-- </form> -->
                    
                        <div class="form-group">
                            <label for="expiry">This users access will expire:</label>
                            <select name="expiry" id="" class="form-control">
                                <option value="never">Never, I will revoke access manually if necessary</option>
                                <option value="1 day">In one day</option>
                                <option value="1 week">In one week</option>
                                <option value="1 month">In one month</option>
                                <option value="1 year">In one year</option>
                            </select>
                        </div>
                        <div id="addEditorContainer">
                            <button type="submit" name="command[addusers]" class="btn btn-info">Add as Editor</button>
                        </div>
                        {generate_form_post_key}
                    </form>
                </div>
            </div>

        </div>

    </div>
    {/if}

</div>

</div>





























