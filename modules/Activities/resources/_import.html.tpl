<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#policiesImportModal">
    <i class="fas fa-file-import mr-2"></i> Import Activities Content
</button>

<!-- Add to Syllabus Modal -->
<div class="modal fade" id="policiesImportModal" tabindex="-1" role="dialog" aria-labelledby="importTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl " role="document">
    <div class="modal-content">
    <div class="modal-header">
        <div class="modal-title d-block-inline">
            <h4>
                Content Previews
            </h4>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <span id="importText"></span>
    </div>
    <div class="modal-body">

        <div class="container-fluid campus-policiess-overview row">
        <p class="lead">
            Select the content preview to add to your syllabus. You are able to view, edit, or remove the full content once it has been added to your syllabus.
        </p>

        {foreach $importableSections as $i => $importable}
        {if $importable->importable}
    <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
        <div class="card h-100">
            <div class="card-body pl-2 pt-2" id="{$i}">
                <label class="form-check-label mt-0 pt-0" for="overlayCheck{$i}">
                <div class="mr-auto text-left mt-0">
                    <div class="form-check mt-0">
                        <input data-index="{$i}" type="checkbox" class="form-check-input overlay-checkbox" id="overlayCheck{$i}" value="{$importable->id}" name="section[real][importable][{$importable->id}]">
                    </div>
                </div>
                <div class="media campus-policies">
                    <div class="text-center vertical-align overlay-icon overlay-icon-policiess" id="checkIcon{$i}">
                        <i class="fas fa-check fa-7x text-success"></i>
                    </div>
                    <div class="media-body">
                        <div class="pl-4">
                            <h5 class="card-title" id="title{$i}">{$importable->title|truncate:50}</h5>
                            <div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
                            <div class="card-text text-muted" id="text{$i}">
                                {include file="{$importable->getSectionExtension()->getPreviewFragment()}"}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        {/if}
        {/foreach}


        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button class="btn btn-success" type="submit" name="command[importsection][{$realSectionClass}]">Add Selected Content to Syllabus</button>
    </div>
    </div>
  </div>
</div>