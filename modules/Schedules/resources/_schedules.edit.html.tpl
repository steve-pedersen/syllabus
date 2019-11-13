<!-- Schedules Section -->
<div class="card card-outline-secondary rounded-0">
    <div class="accordion" id="columnAccordion">
<div class="card-body sort-container" id="schedulesSection">


<div class="form-group row px-3 mt-5">
    <label class="col-lg-3 col-form-label form-control-label" for="section[real][columns]">Select 3 or 4 Column Table</label>
    <div class="col-lg-9">
        <div class="form-check form-check-inline columns3 px-3" data-toggle="collapse" data-target="#collapseColumn" aria-expanded="false" aria-controls="collapseColumn">
            <input class="form-check-input" type="radio" name="section[real][columns]" id="columns3" value=3 {if $realSection->columns != 4}checked{/if}>
            <label class="form-check-label" for="columns3">
            Three Columns
            </label>
        </div>
        <div class="form-check form-check-inline columns4 px-3" data-toggle="collapse" data-target="#collapseColumn" aria-expanded="false" aria-controls="collapseColumn">
            <input class="form-check-input" type="radio" name="section[real][columns]" id="columns4" value=4 {if $realSection->columns == 4}checked{/if}>
            <label class="form-check-label" for="columns4">
            Four Columns
            </label>
        </div>
    </div>
</div>


<div class="form-group headers row px-3">
    <label class="form-control-label col-12" for="section[real][header]">Define Table Headers</label>
    <div class="col-lg-3 header1">
        <input class="form-control" type="text" name="section[real][header1]" value="{$realSection->header1}">
    </div>
    <div class="col-lg-3 header2">
        <input class="form-control" type="text" name="section[real][header2]" value="{$realSection->header2}">
    </div>
    <div class="col-lg-3 header3">
        <input class="form-control" type="text" name="section[real][header3]" value="{$realSection->header3}">
    </div>
    <div class="col-lg-3 header4 collapse" id="collapseColumn" data-parent="#columnAccordion">
        <input class="form-control" type="text" name="section[real][header4]" value="{$realSection->header4}">
    </div>
</div>

    {if $realSection->schedules}

        {foreach $realSection->schedules as $i => $schedule}
            {assign var=scheduleId value="{$schedule->id}"}
            {assign var=sortOrder value="{str_pad($i+1, 3, '0', STR_PAD_LEFT)}"}
<div class="container-fluid mb-2 pr-0" id="scheduleContainer{$scheduleId}">
    <div class="row sort-item">
        <div class="tab-content col-11 px-0" id="toggleEditViewTab{$i}">
            <div class="tab-pane fade border" id="nav-edit-{$i}" role="tabpanel" aria-labelledby="nav-edit-{$i}-tab">
                <div class="mb-2 mx-0 d-flex flex-row bg-light p-3 dragdrop-handle">
                    <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                </div>
                
                <input type="hidden" name="section[real][{$scheduleId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$i+1}-sort-order">
                
                <div class="d-flex justify-content-between">
                    <label class="form-control-label px-2 row-label">Row #{$i + 1}</label>
                    <button type="submit" aria-label="Delete" class="btn btn-link text-danger my-0 mx-2" 
                    {if $groupForm}
                        name="command[deleteitem][Syllabus_Schedules_Schedule][{$scheduleId}]" 
                    {else}
                        name="command[deletesectionitem][Syllabus_Schedules_Schedule][{$scheduleId}]" 
                    {/if}
                    id="{$activityId}">
                        <i class="fas fa-trash-alt mr-1"></i>Delete
                    </button>
                </div>
                
                <div class="form-group schedule-row row px-2">
                    <div class="col-lg-3 column1">
                        <textarea rows="2" class="form-control wysiwyg wysiwyg-datepicker" name="section[real][{$scheduleId}][column1]" placeholder="Column 1">{$schedule->column1}</textarea>
                    </div>
                    <div class="col-lg-3 column2">
                        <textarea rows="2" class="form-control wysiwyg wysiwyg-syllabus-standard" name="section[real][{$scheduleId}][column2]" placeholder="Column 2">{$schedule->column2}</textarea>
                    </div>
                    <div class="col-lg-3 column3">
                        <textarea rows="2" class="form-control wysiwyg wysiwyg-syllabus-standard" name="section[real][{$scheduleId}][column3]" placeholder="Column 3">{$schedule->column3}</textarea>
                    </div>
                    <div class="col-lg-3 column4 collapse" id="collapseColumn" data-parent="#columnAccordion">
                        <textarea rows="2" class="form-control wysiwyg wysiwyg-syllabus-standard" name="section[real][{$scheduleId}][column4]" placeholder="Column 4">{$schedule->column4}</textarea>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show active d-inline-block w-100 px-3" id="nav-view-{$i}" role="tabpanel" aria-labelledby="nav-view-{$i}-tab">
                <div class="row py-2 bg-light border dragdrop-handle rounded">
                    <div class="col-1 dragdrop-handle align-middle">
                        <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                    </div>
                    <div class="col-2 text-truncate">{$schedule->column1|strip_tags:true|truncate:40}</div>
                    <div class="col-3 text-truncate">{$schedule->column2|strip_tags:true|truncate:45}</div>
                    <div class="col-3 text-truncate">{$schedule->column3|strip_tags:true|truncate:45}</div>
                    <div class="col-3 text-truncate">{$schedule->column4|strip_tags:true|truncate:45}</div>
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
        {if $realSection->schedules}
            {assign var=i value="{count($realSection->schedules)}"}
            {assign var=sortOrder value="{count($realSection->schedules)}"}
        {else}
            {assign var=i value="0"}
            {assign var=sortOrder value="1"}
        {/if}
        {assign var=scheduleId value="new-{$i}"}

            
    <div class="sort-item mt-3 border p-2" id="newSortItem{$i}">
        <div class="mb-2 d-flex flex-row bg-white p-2 dragdrop-handle">
             <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
        </div>
        <input type="hidden" name="section[real][{$scheduleId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$sortOrder}-sort-order">
        <label class="form-control-label px-2 row-label">Row #{$i + 1}</label>
        <div class="form-group schedule-row row px-2">
            <div class="col-lg-3 column1">
                <textarea rows="2" class="form-control wysiwyg wysiwyg-datepicker" name="section[real][{$scheduleId}][column1]" placeholder="Column 1"></textarea>
            </div>
            <div class="col-lg-3 column2">
                <textarea rows="2" class="form-control wysiwyg wysiwyg-datepicker" name="section[real][{$scheduleId}][column2]" placeholder="Column 2"></textarea>
            </div>
            <div class="col-lg-3 column3">
                <textarea rows="2" class="form-control wysiwyg wysiwyg-datepicker" name="section[real][{$scheduleId}][column3]" placeholder="Column 3"></textarea>
            </div>
            <div class="col-lg-3 column4 collapse" id="collapseColumn" data-parent="#columnAccordion">
                <textarea rows="2" class="form-control wysiwyg wysiwyg-datepicker" name="section[real][{$scheduleId}][column4]" placeholder="Column 4"></textarea>
            </div>
            <hr class="fancy-line-2">
        </div>
    </div>  


            
{if !$importableSections}
    <div class="form-group d-flex flex-row-reverse mt-4">
{else}
    <div class="form-group d-flex justify-content-between mt-4">
        {include file="{$sectionExtension->getImportFragment()}"}
{/if}
        <input class="btn btn-light" id="addSectionItemBtn" type="submit" name="command[addsectionitem][{$realSectionClass}]" value="+ Add Row" />
    </div>

<hr class="fancy-line-1">

    <div class="form-group row px-3 mt-5">
        <label class="col-lg-3 col-form-label form-control-label">Additional Information</label>
        <div class="col-lg-9">
            <textarea class="form-control wysiwyg wysiwyg-datepicker" type="text" name="section[real][additionalInformation]" rows="5">{$realSection->additionalInformation}</textarea>
        </div>
    </div>

</div>
</div> <!-- End Accordion div -->
</div>
<!-- End Schedules Section -->