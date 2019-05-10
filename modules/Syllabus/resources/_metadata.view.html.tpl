<!-- Metadata Section - View -->
<div class="card card-outline-secondary rounded-0">
    <div class="card-body">
        <div class="col">
            <h2 class="display-5">{if $syllabusVersion}{$syllabusVersion->title}{else}{$syllabus->title}{/if}</h2>
        </div>
        <div class="col">
            <p class="lead">{if $syllabusVersion}{$syllabusVersion->description}{else}{$syllabus->description}{/if}</p>
        </div>
        <form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" autocomplete="off" id="metadataView">
            {if $syllabusVersion}
                <input type="hidden" name="syllabus[version][id]" value="{$syllabusVersion->id}">
            {/if}
            <div class="form-group row">
                <label class="col-lg-3 col-form-label form-control-label"></label>
                <div class="col-lg-9 d-flex flex-row-reverse">
                    <input class="btn btn-info" type="submit" name="command[editsyllabus]" value="Edit" />
                </div>
            </div>
            {generate_form_post_key}
        </form>
    </div>
    {if $syllabus->dateModified || $syllabus->dateCreated}
    <div class="card-footer text-muted">
        {if $syllabus->dateCreated}<small class="text-muted">Date created - {$syllabus->dateCreated}</small>{/if}
        {if $syllabus->dateModified}<small class="text-muted">Last edited - {$syllabus->dateModified}</small>{/if}
    </div>
    {/if}
</div>
<!-- End Metadata Section - View -->