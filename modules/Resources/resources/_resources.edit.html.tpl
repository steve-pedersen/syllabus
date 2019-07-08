<!-- Resources Section -->
<div class="card card-outline-secondary rounded-0">
<div class="card-body sort-container" id="resourcesSection">

<p><em>You can choose to add as many preset campus resources using the "See Campus Resources" button below. If you can't find the resource you are looking for then you can create your own custom one by filling out the following form fields.</em></p>

    {if $realSection->resources}

        {foreach $realSection->resources as $i => $resource}
            {assign var=linkedResource value="{$resource->getCampusResource()}"}
            {assign var=resourceId value="{$resource->id}"}

<div class="container-fluid mb-2" id="resourceContainer{$resourceId}">
    <div class="row sort-item">
        <div class="tab-content col-11 px-0" id="toggleEditViewTab{$i}">
            <div class="tab-pane fade border" id="nav-edit-{$i}" role="tabpanel" aria-labelledby="nav-edit-{$i}-tab">
                <div class="mb-2 mx-0 d-flex flex-row bg-light p-3 dragdrop-handle">
                    <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                </div>
               
                <input type="hidden" name="section[real][{$resourceId}][sortOrder]" value="{$i+1}" class="sort-order-value" id="form-field-{$i+1}-sort-order">
                {if $linkedResource}
                <input type="hidden" name="section[real][{$resourceId}][campusResourcesId]" value="{$resource->campusResourcesId}">
                <input type="hidden" name="section[real][{$resourceId}][imageId]" value="{$resource->imageId}">
                <input type="hidden" name="section[real][{$resourceId}][isCustom]" value="false">
                {/if}
                    
                <div class="d-flex justify-content-end">
                    <button type="submit" aria-label="Delete" class="btn btn-link text-danger my-0 mx-2" name="command[deletesectionitem][Syllabus_Resources_Resource][{$resourceId}]" id="{$resourceId}">
                        <i class="fas fa-trash-alt mr-1"></i>Delete
                    </button>
                </div>
                
                <div class="form-row px-3">
                    <div class="form-group col-md-6">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="section[real][{$resourceId}][title]" placeholder="Title" value="{$resource->title}" {if !$resource->isCustom}disabled{/if}>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="url">Website URL</label>
                        <input type="text" class="form-control" id="url" name="section[real][{$resourceId}][url]" placeholder="https://sfsu.edu" {if !$resource->isCustom}disabled{/if} value="{$resource->url}">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="abbreviation">Abbreviation</label>
                        <input type="text" class="form-control" id="abbreviation" name="section[real][{$resourceId}][abbreviation]" placeholder="Abbreviation" value="{$resource->abbreviation}" {if !$resource->isCustom}disabled{/if}>
                    </div>
                </div>
                <div class="form-row px-3">
                    <div class="form-group col-md-3 text-center d-inline-block">
                        <img class="img-thumbnail" src="{$resource->getImageSrc()}" alt="{$resource->title}">
                    </div>
                    <div class="form-group col-md-9">
                        <label for="description">Description</label>
                        <textarea class="form-control {if !$resource->isCustom}disabled{/if} wysiwyg wysiwyg-basic" name="section[real][{$resourceId}][description]" rows="3" {if !$resource->isCustom}disabled{/if}>{$resource->description}</textarea>
                    </div>
                </div>

                {if !$resource->isCustom}
                <p class="px-3 text-warning"><strong>This resource is not custom and can therefore not be edited.</strong></p>
                {/if}
            </div>
            <div class="tab-pane fade show active d-inline-block w-100 px-3" id="nav-view-{$i}" role="tabpanel" aria-labelledby="nav-view-{$i}-tab">
                <div class="row py-2 bg-light border dragdrop-handle rounded">
                    <div class="col-1 dragdrop-handle align-middle">
                        <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                    </div>
                    <div class="col-1 text-truncate"><strong>#{$i+1}</strong></div>
                    <div class="col-5 text-truncate">{$resource->title|truncate:60}</div>
                    <div class="col-5 text-truncate ">
                        <a href="{$resource->url}">{$resource->url|strip_tags:true|truncate:60}</a>
                    </div>
                </div>   
            </div>
        </div>
        <div class="nav nav-tabs col-1 toggle-edit d-inline-block border-0" role="tablist">
            <a class="btn {if !$resource->isCustom}btn-dark{else}btn-info{/if} py-2" id="nav-edit-{$i}-tab" data-toggle="tab" href="#nav-edit-{$i}" role="tab" aria-controls="nav-edit-{$i}" aria-selected="false">
            {if !$resource->isCustom}
                View #{$i+1}
            {else}
                Edit #{$i+1}
            {/if}
            </a>
            <a class="btn btn-secondary py-2 active" id="nav-view-{$i}-tab" data-toggle="tab" href="#nav-view-{$i}" role="tab" aria-controls="nav-view-{$i}" aria-selected="true">Minimize #{$i+1}</a>
        </div>
    </div>
</div>
        {/foreach}

    {/if}
        {if $realSection->resources}
            {assign var=i value="{count($realSection->resources)}"}
            {assign var=sortOrder value="{count($realSection->resources)}"}
        {else}
            {assign var=i value="0"}
            {assign var=sortOrder value="1"}
        {/if}
        {assign var=resourceId value="new-{$i}"}

            
    <div class="sort-item mt-3 border p-2" id="newSortItem{$i}">
        <div class="mb-2 d-flex flex-row bg-white p-2 dragdrop-handle">
             <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
        </div>
       
        <input type="hidden" name="section[real][{$resourceId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$sortOrder}-sort-order">
        <h4>Custom Resource</h4>
        <div class="form-row ">
            <div class="form-group title col-md-6">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="section[real][{$resourceId}][title]" placeholder="">
            </div>
            <div class="form-group url col-md-4">
                <label for="url">Website URL</label>
                <input type="text" class="form-control" id="url" name="section[real][{$resourceId}][url]" placeholder="https://sfsu.edu">
            </div>
            <div class="form-group abbreviation col-md-2">
                <label for="abbreviation">Abbreviation</label>
                <input type="text" class="form-control" id="abbreviation" name="section[real][{$resourceId}][abbreviation]" placeholder="e.g. DPRC">
            </div>
        </div>
        <div class="form-group description">
            <label for="description">Description</label>
            <textarea class="form-control wysiwyg wysiwyg-basic" id="description" name="section[real][{$resourceId}][description]" placeholder="This resource helps students by..." rows="3"></textarea>
        </div>
    </div>  

    <div class="mt-5">
        
        <div class="form-group d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#resourceAddModal">
            + Add Preset Campus Resources
            </button>
            <span class="display-4 w-100 mx-lg-5 mx-md-0 border-bottom border-top text-center">OR</span>
            <input class="btn btn-light" id="addResourcesSectionItemBtn" type="submit" name="command[addsectionitem][{$realSectionClass}]" value="+ Add Another Custom Resource" />
        </div>

        <hr class="fancy-line-2 mt-5">

        <div class="form-group row px-3 mt-5">
            <label class="col-lg-3 col-form-label form-control-label">Additional Information</label>
            <div class="col-lg-9">
                <textarea class="form-control wysiwyg wysiwyg-basic" type="text" name="section[real][additionalInformation]" rows="5">{$realSection->additionalInformation}</textarea>
            </div>
        </div>
    </div>

</div>
</div>


<!-- Add to Syllabus Modal -->
<div class="modal fade" id="resourceAddModal" tabindex="-1" role="dialog" aria-labelledby="addTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl " role="document">
    <div class="modal-content">
    <div class="modal-header">
        <div class="modal-title d-block-inline">
            <h5>
            <img class="mw-10 mr-3" id="addImage" src="" alt="">
            <span id="addTitle"></span>
            </h5>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <span id="addText"></span>
    </div>
    <div class="modal-body">

        <div class="container-fluid campus-resources-overview row">


        {foreach $campusResources as $i => $campusResource} 
            {if $i > 0 && ($i % 3) == 0}
            {/if}
    <div class="col-xl-4 col-lg-6 col-md-12 mb-4">
        <div class="card h-100">
            <div class="card-body" id="{$i}">
                <label class="form-check-label mt-0 pt-0" for="overlayCheck{$i}">
                <div class="mr-auto text-left mt-0">
                    <div class="form-check mt-0">
                        <input data-index="{$i}" type="checkbox" class="form-check-input overlay-checkbox" id="overlayCheck{$i}" value="{$campusResource->id}" name="section[real][campusResources][{$campusResource->id}]">
                    </div>
                </div>
                <div class="media campus-resource">
                    <div class="text-center vertical-align overlay-icon overlay-icon-resources" id="checkIcon{$i}">
                        <i class="fas fa-check fa-7x text-success"></i>
                    </div>
                    <img class="align-self-center mr-2 ml-0 img-thumbnail w-25" id="image{$i}" src="{$campusResource->imageSrc}" alt="{$campusResource->title}">
                    <div class="media-body pl-1">
                        <h5 class="card-title" id="title{$i}">{$campusResource->title|truncate:50}{if $campusResource->abbreviation} <small>({$campusResource->abbreviation})</small>{/if}</h5>
                        <div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
                        <div class="card-text text-muted" id="text{$i}">
                            {$campusResource->description|truncate:200}
                        </div>
                        <div class="w-100 d-block">
                            <span id="url{$i} text-truncate">{l text="{$campusResource->url|truncate:40}" href=$campusResource->url}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        {/foreach}


        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button class="btn btn-success" type="submit" name="command[addsectionitem][{$realSectionClass}]">Add Selected Resources to Syllabus</button>
    </div>
    </div>
  </div>
</div>




<!-- End Resources Section -->