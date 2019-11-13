<!-- Activities Section -->
<div class="card card-outline-secondary rounded-0">
<div class="card-body sort-container" id="activitiesSection">

    {if $realSection->activities}

        {foreach $realSection->activities as $i => $activity}
            {assign var=activityId value="{$activity->id}"}
            {assign var=sortOrder value="{str_pad($i+1, 3, '0', STR_PAD_LEFT)}"}
<div class="container-fluid mb-2" id="activityContainer{$activityId}">
    <div class="row sort-item">
        <div class="tab-content col-11 px-0" id="toggleEditViewTab{$i}">
            <div class="tab-pane fade border" id="nav-edit-{$i}" role="tabpanel" aria-labelledby="nav-edit-{$i}-tab">
                <div class="mb-2 mx-0 d-flex flex-row bg-light p-3 dragdrop-handle">
                    <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                </div>
               
                <input type="hidden" name="section[real][{$activityId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$i+1}-sort-order">
                <div class="d-flex justify-content-end">
                    <!-- <label class="form-control-label px-2 row-label">Row #{$i + 1}</label> -->
                    <button type="submit" aria-label="Delete" class="btn btn-link text-danger my-0 mx-2" 
                    {if $groupForm}
                        name="command[deleteitem][Syllabus_Activities_Activity][{activityId}]" 
                    {else}
                        name="command[deletesectionitem][Syllabus_Activities_Activity][{activityId}]" 
                    {/if}
                    id="{$activityId}">
                        <i class="fas fa-trash-alt mr-1"></i>Delete
                    </button>
                </div>

                <div class="form-group name row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Name</label>
                    <div class="col-lg-9">
                        <input class="form-control" type="text" name="section[real][{$activityId}][name]" value="{$activity->name}">
                    </div>
                </div>
                <div class="form-group value row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Contribution to Grade</label>
                    <div class="col-lg-9">
                        <input class="form-control" type="text" name="section[real][{$activityId}][value]" value="{$activity->value}">
                    </div>
                </div>
                <div class="form-group description row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Description</label>
                    <div class="col-lg-9">
                        <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="section[real][{$activityId}][description]" rows="4">{$activity->description}</textarea>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show active d-inline-block w-100 px-3" id="nav-view-{$i}" role="tabpanel" aria-labelledby="nav-view-{$i}-tab">
                <div class="row py-2 bg-light border dragdrop-handle rounded">
                    <div class="col-1 dragdrop-handle align-middle">
                        <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                    </div>
                    <div class="col-1 text-truncate"><strong>#{$i+1}</strong></div>
                    <div class="col-3 text-truncate"><strong>{$activity->name|truncate:40}</strong></div>
                    <div class="col-1 text-truncate">{$activity->value|truncate:10}</div>
                    <div class="col text-truncate"><strong>Description: </strong>{$objective->description|strip_tags:true|truncate:50}</div>
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
        {if $realSection->activities}
            {assign var=i value="{count($realSection->activities)}"}
            {assign var=sortOrder value="{count($realSection->activities)}"}
        {else}
            {assign var=i value="0"}
            {assign var=sortOrder value="1"}
        {/if}
        {assign var=activityId value="new-{$i}"}

            
<div class="sort-item mt-3 border p-2" id="newSortItem{$i}">
    <div class="mb-2 d-flex flex-row bg-white p-2 dragdrop-handle">
         <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
    </div>
   
    <input type="hidden" name="section[real][{$activityId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$sortOrder}-sort-order">
    <div class="form-group name row">
        <label class="col-lg-3 col-form-label form-control-label">Name</label>
        <div class="col-lg-9">
            <input class="form-control" type="text" name="section[real][{$activityId}][name]" value="">
        </div>
    </div>
    <div class="form-group value row">
        <label class="col-lg-3 col-form-label form-control-label">Contribution to Grade</label>
        <div class="col-lg-9">
            <input class="form-control" type="text" name="section[real][{$activityId}][value]" value="">
        </div>
    </div>
    <div class="form-group description row">
        <label class="col-lg-3 col-form-label form-control-label">Description</label>
        <div class="col-lg-9">
            <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="section[real][{$activityId}][description]" rows="4"></textarea>
        </div>
    </div>
</div>  

            
{if !$importableSections}
    <div class="form-group d-flex flex-row-reverse mt-4">
{else}
    <div class="form-group d-flex justify-content-between mt-4">
        {include file="{$sectionExtension->getImportFragment()}"}
{/if}
        <input class="btn btn-light" id="addSectionItemBtn" type="submit" name="command[addsectionitem][{$realSectionClass}]" value="+ Add Another Activity" />
    </div>
</div>
</div>
<!-- End Activities Section -->