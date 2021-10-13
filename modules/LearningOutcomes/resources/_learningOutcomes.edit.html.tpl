<!-- LearningOutcomes Section -->
<div class="card card-outline-secondary rounded-0" id="learningOutcomesForm">
    <input type="hidden" name="section[real][external_key]" value="{$realSection->externalKey}" id="courseExternalKey">
    

    <div class="accordion" id="columnAccordion">
<div class="card-body sort-container" id="learningOutcomesSection">


<div class="form-group row px-3 mt-2">
    <label class="col-lg-3 col-form-label form-control-label" for="section[real][columns]">Choose Display Format</label>
    <div class="col-lg-9 pt-2">
        <div class="form-check form-check-inline columns1 px-3">
            <input class="form-check-input" type="radio" name="section[real][columns]" id="columns1" value=1 {if $realSection->columns != 2 && $realSection->columns != 3}checked{/if}>
            <label class="form-check-label" for="columns1">
            Bullet List <small class="ml-1 text-muted">(autofill only)</small>
            </label>
        </div>
        <div class="form-check form-check-inline columns2 px-3" data-toggle="collapse" data-target="#collapseColumn" aria-expanded="false" aria-controls="collapseColumn">
            <input class="form-check-input" type="radio" name="section[real][columns]" id="columns2" value=2 {if $realSection->columns == 2}checked{/if}>
            <label class="form-check-label" for="columns2">
            Two Column Table
            </label>
        </div>
        <div class="form-check form-check-inline columns3 px-3" data-toggle="collapse" data-target="#collapseColumn" aria-expanded="false" aria-controls="collapseColumn">
            <input class="form-check-input" type="radio" name="section[real][columns]" id="columns3" value=3 {if $realSection->columns == 3}checked{/if}>
            <label class="form-check-label" for="columns3">
            Three Column Table
            </label>
        </div>
    </div>
</div>

<div class="row form-group px-3 mt-3" id="outcomesList">
    <label class="col-lg-3 col-form-label form-control-label">Learning Outcomes</label>
    <div class="col-lg-9">
    <ul>
        {foreach $realSection->learningOutcomes as $i => $learningOutcome}
            {assign var=learningOutcomeId value="{$learningOutcome->id}"}
            <li class="learning-outcome-li" id="li-{$learningOutcomeId}">
                <input type="hidden" name="section[real][{$learningOutcomeId}][column1]" value="{$learningOutcome->column1}">
                {$learningOutcome->column1|strip_tags}
            </li>
        {/foreach}        
    </ul>
    </div>
</div>

<div id="outcomesTable">

<div class="form-group headers row px-3">
    <label class="form-control-label col-12" for="section[real][header]">Define Table Headers</label>
    <div class="col-lg-4 header1">
        <input class="form-control" type="text" name="section[real][header1]" value="{if $realSection->header1}{$realSection->header1}{else}Learning Outcomes{/if}">
    </div>
    <div class="col-lg-4 header2">
        <input class="form-control" type="text" name="section[real][header2]" value="{$realSection->header2}" placeholder="Relevant course work...">
    </div>
    <div class="col-lg-4 header3 collapse" id="collapseColumn" data-parent="#columnAccordion">
        <input class="form-control" type="text" name="section[real][header3]" value="{$realSection->header3}">
    </div>
</div>

    {if $realSection->learningOutcomes}

        {foreach $realSection->learningOutcomes as $i => $learningOutcome}
            {assign var=learningOutcomeId value="{$learningOutcome->id}"}
            {assign var=sortOrder value="{str_pad($i+1, 3, '0', STR_PAD_LEFT)}"}
<div class="container-fluid mb-2 pr-0" id="learningOutcomeContainer{$learningOutcomeId}">
    <div class="row sort-item">
        <div class="tab-content col-11 px-0" id="toggleEditViewTab{$i}">
            <div class="tab-pane fade border" id="nav-edit-{$i}" role="tabpanel" aria-labelledby="nav-edit-{$i}-tab">
                <div class="mb-2 mx-0 d-flex flex-row bg-light p-3 dragdrop-handle">
                    <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                </div>
                
                <input type="hidden" name="section[real][{$learningOutcomeId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$i+1}-sort-order">
                
                <div class="d-flex justify-content-between">
                    <label class="form-control-label px-2 row-label">Row #{$i + 1}</label>
                    <button type="submit" aria-label="Delete" class="btn btn-link text-danger my-0 mx-2" 
                    {if $groupForm}
                        name="command[deleteitem][Syllabus_LearningOutcomes_LearningOutcome][{$learningOutcomeId}]" 
                    {else}
                        name="command[deletesectionitem][Syllabus_LearningOutcomes_LearningOutcome][{$learningOutcomeId}]" 
                    {/if}
                    id="{$learningOutcomeId}">
                        <i class="fas fa-trash-alt mr-1"></i>Delete
                    </button>
                </div>
                
                <div class="form-group learning-outcome-row row px-2">
                    <div class="col-lg-4 column1">
                        <textarea readonly="true" rows="3" id="ckeditor-{$i}-1" class="form-control" name="section[real][{$learningOutcomeId}][column1]">{$learningOutcome->column1}</textarea>
                    </div>
                    <div class="col-lg-4 column2">
                        <textarea rows="2" class="form-control wysiwyg wysiwyg-table-cell" name="section[real][{$learningOutcomeId}][column2]" placeholder="Column 2">{$learningOutcome->column2}</textarea>
                    </div>
                    <div class="col-lg-4 column3 collapse" id="collapseColumn" data-parent="#columnAccordion">
                        <textarea rows="2" class="form-control wysiwyg wysiwyg-table-cell" name="section[real][{$learningOutcomeId}][column3]" placeholder="Column 3">{$learningOutcome->column3}</textarea>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show active d-inline-block w-100 px-3" id="nav-view-{$i}" role="tabpanel" aria-labelledby="nav-view-{$i}-tab">
                <div class="row py-2 bg-light border dragdrop-handle rounded">
                    <div class="col-1 dragdrop-handle align-middle">
                        <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
                    </div>
                    <div class="col-4 text-truncate">{$learningOutcome->column1|strip_tags:true|truncate:50}</div>
                    <div class="col-4 text-truncate">{$learningOutcome->column2|strip_tags:true|truncate:50}</div>
                    <div class="col-3 text-truncate">{$learningOutcome->column3|strip_tags:true|truncate:50}</div>
                </div>   
            </div>
        </div>
        <div class="nav nav-tabs col-1 toggle-edit d-inline-block border-0" role="tablist">
            <a class="btn btn-sm btn-info py-2" id="nav-edit-{$i}-tab" data-toggle="tab" href="#nav-edit-{$i}" role="tab" aria-controls="nav-edit-{$i}" aria-selected="false">Edit #{$i+1}</a>
            <a class="btn btn-sm btn-secondary py-2 active" id="nav-view-{$i}-tab" data-toggle="tab" href="#nav-view-{$i}" role="tab" aria-controls="nav-view-{$i}" aria-selected="true">Minimize #{$i+1}</a>
        </div>

    </div>
</div>
        {/foreach}

    {/if}
        {if $realSection->learningOutcomes}
            {assign var=i value="{count($realSection->learningOutcomes)}"}
            {assign var=sortOrder value="{count($realSection->learningOutcomes)}"}
        {else}
            {assign var=i value="0"}
            {assign var=sortOrder value="1"}
        {/if}
        {assign var=learningOutcomeId value="new-{$i}"}

            
    <div class="sort-item mt-3 border p-2" id="newSortItem{$i}">
        <div class="mb-2 d-flex flex-row bg-white p-2 dragdrop-handle">
             <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
        </div>
        <input type="hidden" name="section[real][{$learningOutcomeId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$sortOrder}-sort-order">
        <label class="form-control-label px-2 row-label">Row #{$i + 1}</label>
        <div class="form-group learning-outcome-row row px-2">
            <div class="col-lg-4 column1">
                <textarea readonly="true" rows="3" id="ckeditor-{$i}-1" class="form-control" name="section[real][{$learningOutcomeId}][column1]"></textarea>
            </div>
            <div class="col-lg-4 column2">
                <textarea rows="2" class="form-control wysiwyg wysiwyg-table-cell" name="section[real][{$learningOutcomeId}][column2]" placeholder="Column 2"></textarea>
            </div>
            <div class="col-lg-4 column3 collapse" id="collapseColumn" data-parent="#columnAccordion">
                <textarea rows="2" class="form-control wysiwyg wysiwyg-table-cell" name="section[real][{$learningOutcomeId}][column3]" placeholder="Column 3"></textarea>
            </div>

            <hr class="fancy-line-2">
        </div>
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
            <textarea class="form-control wysiwyg wysiwyg-syllabus-full" type="text" name="section[real][additionalInformation]" rows="5">{$realSection->additionalInformation}</textarea>
        </div>
    </div>

</div>
</div> <!-- End Accordion div -->
</div>
<!-- End LearningOutcomes Section -->