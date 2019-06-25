<!-- Materials Section -->
<div class="card card-outline-secondary rounded-0">
<div class="card-body sort-container" id="materialsSection">

    {if $realSection->materials}

        {foreach $realSection->materials as $i => $material}
            {assign var=materialId value="{$material->id}"}
<div class="container-fluid mb-2">
    <div class="row sort-item">
        <div class="tab-content col-11 px-0" id="toggleEditViewTab{$i}">
            <div class="tab-pane fade border" id="nav-edit-{$i}" role="tabpanel" aria-labelledby="nav-edit-{$i}-tab">
                <div class="mb-2 mx-0 d-flex flex-row bg-light p-3 dragdrop-handle">
                    <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                </div>
               
                <input type="hidden" name="section[real][{$materialId}][sortOrder]" value="{$i+1}" class="sort-order-value" id="form-field-{$i+1}-sort-order">
                <div class="form-group title row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Material #{$i+1} Title</label>
                    <div class="col-lg-9">
                        <input class="form-control" type="text" name="section[real][{$materialId}][title]" value="{$material->title}">
                    </div>
                </div>
                <div class="form-group url row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Material #{$i+1} URL</label>
                    <div class="col-lg-9">
                        <input class="form-control" type="text" name="section[real][{$materialId}][url]" value="{$material->url}">
                    </div>
                </div>
                <div class="form-group required row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Required?</label>
                    <div class="col-lg-9">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="section[real][{$materialId}][required]" id="material{$i}Yes" value="true" {if $material->required}checked{/if}>
                            <label class="form-check-label" for="material{$i}Yes">Yes</label>
                        </div>
                        <div class="form-check form-check-inline ml-3">
                            <input class="form-check-input" type="radio" name="section[real][{$materialId}][required]" id="material{$i}No" value="false" {if !$material->required}checked{/if}>
                            <label class="form-check-label" for="material{$i}No">No</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show active d-inline-block w-100 px-3" id="nav-view-{$i}" role="tabpanel" aria-labelledby="nav-view-{$i}-tab">
                <div class="row py-2 bg-light border dragdrop-handle rounded">
                    <div class="col-1 dragdrop-handle align-middle">
                        <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                    </div>
                    <div class="col-1 text-truncate"><strong>#{$i+1}</strong></div>
                    <div class="col-5 text-truncate"><strong>Title: </strong>{$material->title|truncate:50}</div>
                    <div class="col-3 text-truncate ">
                        <strong>URL: </strong><a href="{$material->url}">{$material->url|strip_tags:true|truncate:30}</a>
                    </div>
                    <div class="col-2 text-truncate">
                        {if $material->required}<span class="text-danger">Required</span>{/if}
                    </div>
                </div>   
            </div>
        </div>
        <div class="nav nav-tabs col-1 toggle-edit d-inline-block border-0" role="tablist">
            <a class="btn btn-info py-2" id="nav-edit-{$i}-tab" data-toggle="tab" href="#nav-edit-{$i}" role="tab" aria-controls="nav-edit-{$i}" aria-selected="false">Edit #{$i+1}</a>
            <a class="btn btn-secondary py-2 active" id="nav-view-{$i}-tab" data-toggle="tab" href="#nav-view-{$i}" role="tab" aria-controls="nav-view-{$i}" aria-selected="true">Minimize #{$i+1}</a>
        </div>
    </div>
</div>
        {/foreach}

    {/if}
        {if $realSection->materials}
            {assign var=i value="{count($realSection->materials)}"}
            {assign var=sortOrder value="{count($realSection->materials)}"}
        {else}
            {assign var=i value="0"}
            {assign var=sortOrder value="1"}
        {/if}
        {assign var=materialId value="new-{$i}"}

            
<div class="sort-item mt-3 border p-2" id="newSortItem{$i}">
    <div class="mb-2 d-flex flex-row bg-white p-2 dragdrop-handle">
         <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
    </div>
   
    <input type="hidden" name="section[real][{$materialId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$sortOrder}-sort-order">
    <div class="form-group title row">
        <label class="col-lg-3 title col-form-label form-control-label">Material #{$i+1} Title</label>
        <div class="col-lg-9">
            <input class="form-control" type="text" name="section[real][{$materialId}][title]" value="">
        </div>
    </div>
    <div class="form-group url row">
        <label class="col-lg-3 url col-form-label form-control-label">Material #{$i+1} URL</label>
        <div class="col-lg-9">
            <input class="form-control" type="text" name="section[real][{$materialId}][url]" value="">
        </div>
    </div>
    <div class="form-group required row">
        <label class="col-lg-3 required col-form-label form-control-label">Required?</label>
        <div class="col-lg-9">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="section[real][{$materialId}][required]" id="material{$i}Yes" value="true" {if $material->required}checked{/if}>
                <label class="form-check-label" for="material{$i}Yes">Yes</label>
            </div>
            <div class="form-check form-check-inline ml-3">
                <input class="form-check-input" type="radio" name="section[real][{$materialId}][required]" id="material{$i}No" value="false" {if !$material->required}checked{/if}>
                <label class="form-check-label" for="material{$i}No">No</label>
            </div>
        </div>
    </div>
</div>  
            
    <div class="form-group d-flex flex-row-reverse mt-4">
        <input class="btn btn-light" id="addMaterialsSectionItemBtn" type="submit" name="command[addsectionitem][{$realSectionClass}]" value="+ Add Another Material" />
    </div>

    <div class="form-group row px-3 mt-5">
        <label class="col-lg-3 col-form-label form-control-label">Additional Information</label>
        <div class="col-lg-9">
            <textarea class="form-control wysiwyg wysiwyg-basic" type="text" name="section[real][additionalInformation]" rows="5">{$realSection->additionalInformation}</textarea>
        </div>
    </div>
</div>
</div>
<!-- End Materials Section -->