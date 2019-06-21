{assign var=ext value=$sectionVersion->extension}
{assign var=extName value=$ext::getExtensionName()}
{assign var=sectionVersionId value=$sectionVersion->id}
{assign var=editable value=(!$sectionVersion->readOnly || ($sectionVersion->readOnly && $sectionVersion->canEditReadOnly))}

<div class="sort-item editor-{$extName} mt-3 {if !$editable}text-muted{/if}" id="section{$extName}{$i}">
	<div class="d-block-inline bg-light p-2 section-collapse-link dragdrop-handle" data-toggle="collapse" href="#{$extName}Collapse{$i}" aria-expanded="false" aria-controls="{$extName}Collapse{$i}">
	<i class="fas fa-bars fa-2x dragdrop-handle mr-2 text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
	<a class="d-block-inline p-3" data-toggle="collapse" href="#{$extName}Collapse{$i}"><div class="text-left d-inline-block" id="{$extName}Heading">
		<span class="mb-0 section-title">
			<strong>{$sectionVersion->title}</strong><small><i class="fas fa-chevron-down text-dark pl-2"></i></small>
		</span></div></a>
		{if $sectionVersion->description}<small class="text-dark">{$sectionVersion->description}</small>{/if}
		{if !$editable}<span class=""><i class="fas fa-lock ml-3 mr-2"></i> (Read Only)</span>{/if}
	</div>

	<input type="hidden" name="section[realClass][{$sectionVersionId}]" value="{$realSectionClass}">
	<input type="hidden" name="section[extKey][{$sectionVersionId}]" value="{$ext->getExtensionKey()}">
	<input type="hidden" name="section[properties][sortOrder][{$sectionVersionId}]" value="{$i+1}" class="sort-order-value" id="form-field-{$i+1}-sort-order">

	<div class="collapse multi-collapse show section-collapsible" id="{$extName}Collapse{$i}">
		<div class="card card-outline-secondary rounded-0">
			<div class="card-body">
				{include file="{$ext->getViewFragment()}"}
			    <div class="form-group row">
			        <label class="col-lg-3 col-form-label form-control-label"></label>
			        <div class="col-lg-9 d-flex flex-row-reverse">
			        	{if $editable}
			            <a class="btn btn-info" href="syllabus/{$syllabus->id}?edit={$sectionVersionId}">Edit</a>
			        	{else}
						<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="This section can't be edited, but you can still change it's order.">
							<button class="btn btn-dark" style="pointer-events: none;" type="button" disabled>Read Only</button>
						</span>
			        	{/if}
			        </div>
			    </div>				
			    {if $sectionVersion->dateCreated}
			    <div class="card-footer text-muted">
			        <small class="text-muted">Date created - {$sectionVersion->dateCreated->format('Y m, d')}</small>
			    </div>
			    {/if}
			</div>
		</div>
	</div>
</div>