<!-- Course Section -->
<div class="card card-outline-secondary rounded-0" id="courseForm">
    <input type="hidden" name="section[real][external_key]" value="{$realSection->externalKey}" id="courseExternalKey">
    <div class="card-body">
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Course Title</label>
            <div class="col-lg-9">
                <input disabled class="form-control" type="text" name="section[real][title]" value="{$realSection->title}" placeholder="Placeholder for Course Title">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Course Description</label>
            <div class="col-lg-9">
                <!-- <textarea class="form-control wysiwyg wysiwyg-full" type="text" name="section[real][description]" rows="3" id="description" placeholder="Placeholder for Course Description">{$realSection->description}</textarea> -->
                <textarea disabled class="form-control" type="text" name="section[real][description]" rows="4" id="description" placeholder="Placeholder for Course Description">{$realSection->description}</textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Course Section Number</label>
            <div class="col-lg-9">
                <input disabled class="form-control" type="text" name="section[real][sectionNumber]" value="{$realSection->sectionNumber}" placeholder="Placeholder for Section Number">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Class Number</label>
            <div class="col-lg-9">
                <input disabled class="form-control" type="text" name="section[real][classNumber]" value="{$realSection->classNumber}" placeholder="Placeholder for Class Number">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Semester</label>
            <div class="col-lg-9">
                <input disabled class="form-control" type="text" name="section[real][semester]" value="{$realSection->semester}" placeholder="Placeholder for Semester">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Year</label>
            <div class="col-lg-9">
                <input disabled class="form-control" type="text" name="section[real][year]" value="{$realSection->year}" placeholder="Placeholder for Year">
            </div>
        </div>

    {if $courseSchedule && $scheduleData}
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Schedule Information</label>
            <div class="col-lg-9">
            {foreach $scheduleData as $sched}
            <dl class="row mb-0">
                <dt class="col-xl-4 col-lg-4 col-md-5 col-sm-12">{$sched.facility.description}</dt>
                <dd class="col-xl-8 col-lg-8 col-md-7 col-sm-12">
                    <strong class="mr-2">{$sched.info.stnd_mtg_pat|replace:'R':'Th'}</strong> 
                    {$sched.info.start_time} to {$sched.info.end_time}
                </dd>
            </dl>
            {/foreach}                
            </div>
        </div>
    {/if}

        <div class="row mt-5 mb-2">
            <div class="col-lg-12 text-center alert alert-warning">
                <strong>The official course information fields above can't be edited.</strong> However, you can still change the syllabus section title & introduction.
            </div>
        </div>
    </div>
    {if $currentSectionVersion->dateCreated}
    <div class="card-footer text-muted">
        <small class="text-muted">Date modified - {$currentSectionVersion->dateCreated->format('Y m, d')}</small>
    </div>
    {/if}
</div>
<!-- End Course Section -->