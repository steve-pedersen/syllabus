<!-- Course Section -->
<div class="card card-outline-secondary rounded-0" id="courseForm">
    <input type="hidden" name="section[real][external_key]" value="{$realSection->externalKey}" id="courseExternalKey">
    <div class="card-body">
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Course Title</label>
            <div class="col-lg-9">
                <input class="form-control" type="text" name="section[real][title]" value="{$realSection->title}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Course Description</label>
            <div class="col-lg-9">
                <textarea class="form-control wysiwyg wysiwyg-full" type="text" name="section[real][description]" rows="3" id="description">{$realSection->description}</textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Section Number</label>
            <div class="col-lg-9">
                <input class="form-control" type="text" name="section[real][sectionNumber]" value="{$realSection->sectionNumber}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Class Number</label>
            <div class="col-lg-9">
                <input class="form-control" type="text" name="section[real][classNumber]" value="{$realSection->classNumber}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Semester</label>
            <div class="col-lg-9">
                <input class="form-control" type="text" name="section[real][semester]" value="{$realSection->semester}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Year</label>
            <div class="col-lg-9">
                <input class="form-control" type="text" name="section[real][year]" value="{$realSection->year}">
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