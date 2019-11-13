<!-- Policies Section -->
<div class="card card-outline-secondary rounded-0">
<div class="card-body sort-container" id="policiesSection">

    {if $realSection->policies}

        {foreach $realSection->policies as $i => $policy}
            {assign var=policyId value="{$policy->id}"}
            {assign var=sortOrder value="{str_pad($i+1, 3, '0', STR_PAD_LEFT)}"}
<div class="container-fluid mb-2" id="policyContainer{$policyId}">
    <div class="row sort-item">
        <div class="tab-content col-11 px-0" id="toggleEditViewTab{$i}">
            <div class="tab-pane fade border" id="nav-edit-{$i}" role="tabpanel" aria-labelledby="nav-edit-{$i}-tab">
                <div class="mb-2 mx-0 d-flex flex-row bg-light p-3 dragdrop-handle">
                    <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                </div>
               
                <input type="hidden" name="section[real][{$policyId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$i+1}-sort-order">
                <div class="d-flex justify-content-end">
                    <button type="submit" aria-label="Delete" class="btn btn-link text-danger my-0 mx-2" 
                    {if $groupForm}
                        name="command[deleteitem][Syllabus_Policies_Policy][{$policyId}]" 
                    {else}
                        name="command[deletesectionitem][Syllabus_Policies_Policy][{$policyId}]" 
                    {/if}
                    id="{$activityId}">
                        <i class="fas fa-trash-alt mr-1"></i>Delete
                    </button>
                </div>

                <div class="form-group row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Policy #{$i+1} Title</label>
                    <div class="col-lg-9">
                        <input class="form-control" type="text" name="section[real][{$policyId}][title]" value="{$policy->title}">
                    </div>
                </div>
                <div class="form-group row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Policy #{$i+1} Description</label>
                    <div class="col-lg-9">
                        <textarea class="form-control wysiwyg wysiwyg-syllabus-full" type="text" name="section[real][{$policyId}][description]" rows="5">{$policy->description}</textarea>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show active d-inline-block w-100 px-3" id="nav-view-{$i}" role="tabpanel" aria-labelledby="nav-view-{$i}-tab">
                <div class="row py-2 bg-light border dragdrop-handle rounded">
                    <div class="col-1 dragdrop-handle align-middle">
                        <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                    </div>
                    <div class="col-1 text-truncate"><strong>#{$i+1}</strong></div>
                    <div class="col-3 text-truncate"><strong>{$policy->title|truncate:40}</strong></div>
                    <div class="col text-truncate ">{$policy->description|strip_tags:true|truncate:80}</div>
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
        {if $realSection->policies}
            {assign var=i value="{count($realSection->policies)}"}
            {assign var=sortOrder value="{count($realSection->policies)}"}
        {else}
            {assign var=i value="0"}
            {assign var=sortOrder value="1"}
        {/if}
        {assign var=policyId value="new-{$i}"}

            
<div class="sort-item mt-3 border p-2" id="newSortItem{$i}">
    <div class="mb-2 d-flex flex-row bg-white p-2 dragdrop-handle">
         <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
    </div>
   
    <input type="hidden" name="section[real][{$policyId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$sortOrder}-sort-order">
    <div class="form-group row">
        <label class="col-lg-3 col-form-label form-control-label">Policy #{$i+1} Title</label>
        <div class="col-lg-9">
            <input class="form-control" type="text" name="section[real][{$policyId}][title]" value="">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-3 col-form-label form-control-label">Policy #{$i+1} Description</label>
        <div class="col-lg-9">
            <textarea class="form-control wysiwyg wysiwyg-syllabus-full" type="text" name="section[real][{$policyId}][description]" rows="5"></textarea>
        </div>
    </div>
</div>  

            
{if !$importableSections}
    <div class="form-group d-flex flex-row-reverse mt-4">
{else}
    <div class="form-group d-flex justify-content-between mt-4">
        {include file="{$sectionExtension->getImportFragment()}"}
{/if}
        <input class="btn btn-light" id="addSectionItemBtn" type="submit" name="command[addsectionitem][{$realSectionClass}]" value="+ Add Another Policy" />
    </div>
</div>
</div>
<!-- End Policies Section -->