<!-- TeachingAssistants Section -->
<div class="card card-outline-secondary rounded-0">
<div class="card-body sort-container" id="teachingAssistantsSection">

    {if $realSection->teachingAssistants}

        {foreach $realSection->teachingAssistants as $i => $ta}
            {assign var=taId value="{$ta->id}"}
            {assign var=sortOrder value="{str_pad($i+1, 3, '0', STR_PAD_LEFT)}"}
<div class="container-fluid mb-2" id="teachingAssistantContainer{$taId}">
    <div class="row sort-item">
        <div class="tab-content col-11 px-0" id="toggleEditViewTab{$i}">
            <div class="tab-pane fade border" id="nav-edit-{$i}" role="tabpanel" aria-labelledby="nav-edit-{$i}-tab">
                <div class="mb-2 mx-0 d-flex flex-row bg-light p-3 dragdrop-handle">
                    <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                </div>
               
                <input type="hidden" name="section[real][{$taId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$i+1}-sort-order">
                <div class="d-flex justify-content-end">
                    <button type="submit" aria-label="Delete" class="btn btn-link text-danger my-0 mx-2" name="command[deletesectionitem][Syllabus_TeachingAssistants_TeachingAssistant][{$taId}]" id="{$taId}">
                        <i class="fas fa-trash-alt mr-1"></i>Delete
                    </button>
                </div>
                <div class="form-group name row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Name</label>
                    <div class="col-lg-9">
                        <input class="form-control" type="text" name="section[real][{$taId}][name]" value="{$ta->name}">
                    </div>
                </div>
                <div class="form-group email row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Email</label>
                    <div class="col-lg-9">
                        <input class="form-control" type="text" name="section[real][{$taId}][email]" value="{$ta->email}">
                    </div>
                </div>
                <div class="form-group row px-3">
                    <label class="col-lg-3 col-form-label form-control-label">Additional Information</label>
                    <div class="col-lg-9">
                        <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="section[real][{$taId}][additionalInformation]" rows="5">{$ta->additionalInformation}</textarea>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show active d-inline-block w-100 px-3" id="nav-view-{$i}" role="tabpanel" aria-labelledby="nav-view-{$i}-tab">
                <div class="row py-2 bg-light border dragdrop-handle rounded">
                    <div class="col-1 dragdrop-handle align-middle">
                        <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                    </div>
                    <div class="col-1 text-truncate"><strong>#{$i+1}</strong></div>
                    <div class="col-5 text-truncate"><strong>{$ta->name|truncate:40}</strong></div>
                    <div class="col-5 text-truncate">{l href="mailto:{$ta->email}" text=$ta->email}</div>
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
        {if $realSection->teachingAssistants}
            {assign var=i value="{count($realSection->teachingAssistants)}"}
            {assign var=sortOrder value="{count($realSection->teachingAssistants)}"}
        {else}
            {assign var=i value="0"}
            {assign var=sortOrder value="1"}
        {/if}
        {assign var=taId value="new-{$i}"}

            
<div class="sort-item mt-3 border p-2" id="newSortItem{$i}">
    <div class="mb-2 d-flex flex-row bg-white p-2 dragdrop-handle">
         <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
    </div>
   
    <input type="hidden" name="section[real][{$taId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$sortOrder}-sort-order">
    <div class="form-group name row">
        <label class="col-lg-3 col-form-label form-control-label">Name</label>
        <div class="col-lg-9">
            <input class="form-control" type="text" name="section[real][{$taId}][name]" value="">
        </div>
    </div>
    <div class="form-group email row">
        <label class="col-lg-3 col-form-label form-control-label">Email</label>
        <div class="col-lg-9">
            <input class="form-control" type="text" name="section[real][{$taId}][email]" value="">
        </div>
    </div>
    <div class="form-group additional-information row">
        <label class="col-lg-3 col-form-label form-control-label">Additional Information</label>
        <div class="col-lg-9">
            <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="section[real][{$taId}][additionalInformation]" rows="5"></textarea>
        </div>
    </div>
</div>  

            
    <div class="form-group d-flex flex-row-reverse mt-4">
        <input class="btn btn-light" id="addSectionItemBtn" type="submit" name="command[addsectionitem][{$realSectionClass}]" value="+ Add Another Teaching Assistant" />
    </div>
</div>
</div>
<!-- End TeachingAssistants Section -->