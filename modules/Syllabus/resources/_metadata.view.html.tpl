<!-- Metadata Section - View -->
<div class="card card-outline-secondary rounded-0">
    <div class="card-body">
        <div class="col">
            <h2 class="display-5">{$syllabusVersion->title}</h2>
        </div>
        <div class="col">
            <p class="lead">{$syllabusVersion->description}</p>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label"></label>
            <div class="col-lg-9 d-flex flex-row-reverse">
                <input class="btn btn-info" type="submit" name="command[editsyllabus]" value="Edit" />
            </div>
        </div>
    </div>
    {if $syllabusVersion->dateCreated}
    <div class="card-footer text-muted">
        <small class="text-muted">Date modified - {$syllabusVersion->dateCreated->format('Y m, d')}</small>
    </div>
    {/if}
</div>
<!-- End Metadata Section - View -->