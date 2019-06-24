<!-- Objectives Section -->
<div class="card card-outline-secondary rounded-0">
<div class="card-body sort-container" id="objectivesSection">

    {if $realSection->objectives}

        {foreach $realSection->objectives as $i => $objective}
            {assign var=objectiveId value="{$objective->id}"}
<div class="container-fluid mb-2">
    <div class="row sort-item">
        <div class="tab-content col-11 px-0" id="toggleEditViewTab{$i}">
            <div class="tab-pane fade border" id="nav-edit-{$i}" role="tabpanel" aria-labelledby="nav-edit-{$i}-tab">
                <div class="mb-2 mx-0 d-flex flex-row bg-light p-3 dragdrop-handle">
                    <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                </div>
               
                <input type="hidden" name="section[real][{$objectiveId}][sortOrder]" value="{$i+1}" class="sort-order-value" id="form-field-{$i+1}-sort-order">
                <div class="form-group row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Objective #{$i+1} Title</label>
                    <div class="col-lg-9">
                        <input class="form-control" type="text" name="section[real][{$objectiveId}][title]" value="{$objective->title}">
                    </div>
                </div>
                <div class="form-group row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Objective #{$i+1} Description</label>
                    <div class="col-lg-9">
                        <textarea class="form-control wysiwyg wysiwyg-basic" type="text" name="section[real][{$objectiveId}][description]" rows="5">{$objective->description}</textarea>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show active d-inline-block w-100 px-3" id="nav-view-{$i}" role="tabpanel" aria-labelledby="nav-view-{$i}-tab">
                <div class="row py-2 bg-light border dragdrop-handle rounded">
                    <div class="col-1 dragdrop-handle align-middle">
                        <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                    </div>
                    <div class="col-1 text-truncate"><strong>#{$i+1}</strong></div>
                    <div class="col-3 text-truncate"><strong>Title: </strong>{$objective->title|truncate:30}</div>
                    <div class="col text-truncate "><strong>Description: </strong>{$objective->description|strip_tags:true|truncate:50}</div>
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
        {if $realSection->objectives}
            {assign var=i value="{count($realSection->objectives)}"}
            {assign var=sortOrder value="{count($realSection->objectives)}"}
        {else}
            {assign var=i value="0"}
            {assign var=sortOrder value="1"}
        {/if}
        {assign var=objectiveId value="new-{$i}"}

            
<div class="sort-item mt-3 border p-2" id="newSortItem{$i}">
    <div class="mb-2 d-flex flex-row bg-white p-2 dragdrop-handle">
         <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
    </div>
   
    <input type="hidden" name="section[real][{$objectiveId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$sortOrder}-sort-order">
    <div class="form-group row">
        <label class="col-lg-3 col-form-label form-control-label">Objective #{$i+1} Title</label>
        <div class="col-lg-9">
            <input class="form-control" type="text" name="section[real][{$objectiveId}][title]" value="">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-3 col-form-label form-control-label">Objective #{$i+1} Description</label>
        <div class="col-lg-9">
            <textarea class="form-control wysiwyg wysiwyg-basic" type="text" name="section[real][{$objectiveId}][description]" rows="5"></textarea>
        </div>
    </div>
</div>  

            
    <div class="form-group d-flex flex-row-reverse mt-4">
        <input class="btn btn-primary" id="addSectionItemBtn" type="submit" name="command[addsectionitem][{$realSectionClass}]" value="Add Another Objective" />
    </div>
</div>
</div>
<!-- End Objectives Section -->