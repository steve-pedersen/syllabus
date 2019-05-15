<!-- BEGIN Page Container -->
<div class="row editor-main-container">

<!-- BEGIN Anchor Link Sidebar -->
<nav class="col-md-2 d-none d-md-block anchor-links-sidebar bg-dark text-white" >
	<div class="sidebar-sticky mb-3">
		<br><br>
		<br>
		<ul class="nav flex-column">
			<li class="nav-item sidebar-anchor-item">
				<a class="nav-link" href="{$smarty.server.REQUEST_URI}#goToTop">
				<strong><i class="fas fa-arrow-up pr-2"></i> Go To Top</strong>
				</a>
			</li>
		{foreach $sections as $i => $section}
			{if ($section->version->resolveSection()->id != $realSection->id) && $section->isAnchored}
				{assign var=ext value=$section->extension}
				{assign var=extName value=$ext::getExtensionName()}
			<li class="nav-item sidebar-anchor-item">
				<a class="nav-link" href="{$smarty.server.REQUEST_URI}#section{$extName}{$i}">
				{if $section->version->title}{$section->version->title}{else}{$ext->getDisplayName()}{/if}
				</a>
			</li>
			{/if}				
		{/foreach}
		{if $realSection}
			{assign var=extName value=$sectionExtension::getExtensionName()}
			{assign var=displayName value=$sectionExtension->getDisplayName()}
			<li class="nav-item sidebar-anchor-item">
				<a class="nav-link active text-white" href="{$smarty.server.REQUEST_URI}#section{$extName}Edit">
				{if $currentSectionVersion}{$currentSectionVersion->title}{else}{$displayName}{/if}
				</a>
			</li>
		{/if}
		</ul>
	</div>
</nav>
<!-- END Anchor Link Sidebar -->


<!-- BEGIN Main Region -->
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mt-0"> 

<!-- BEGIN Editor Container -->
<div class="syllabus-editor mt-3" id="syllabusEditor">

	<!-- BEGIN Sections Edit/View Form -->
	<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" id="viewSections">
		{if $syllabusVersion->inDataSource}
		<input type="hidden" name="syllabusVersion[id]" value="{$syllabusVersion->id}">
		{/if}

		<!-- BEGIN Main Syllabus Editor Controls Top -->
		<div class="editor-main-controls-top mb-3 form-inline" id="editorMainControlsTop">
			{if $syllabus->inDataSource}
			<div class="editor-controls-left">
				<select name="addSection" class="form-control">
					<option value="">Choose Section to Add...</option>
				{foreach $sectionExtensions as $ext}
					<option value="{$ext->getRecordClass()}">{$ext->getDisplayName()}</option>
				{/foreach}
				</select>
				<input class="btn btn-primary" type="submit" name="command[addsection]" value="Add Section" />
			</div>
			{/if}
			<div class="editor-controls-right ml-auto">
				<input class="btn btn-success" type="submit" name="command[savesyllabus]" value="Save Syllabus" id="globalSave" />
				<input class="btn btn-secondary" type="submit" name="command[previewsyllabus]" value="Preview" />
				<button type="button" class="btn btn-link btn">Cancel</button>						
			</div>
		</div>
		<!-- END Main Syllabus Editor Controls Top -->


		<!-- BEGIN Metadata Section -->
		<div class="editor-metadata">
			<a class="d-block bg-light p-2 section-collapse-link" data-toggle="collapse" href="#metadataCollapse" role="button" aria-expanded="false" aria-controls="metadataCollapse">			
				<div class="text-left d-inline-block" id="metadataHeading"> 
					<span class="mb-0 section-title">
						<small><i class="fas fa-chevron-down text-dark"></i></small> 
						<strong>Syllabus Metadata</strong>
					</span>
					 - <small class="text-dark">Information about this syllabus that is not displayed inside the syllabus itself.</small>
				</div>
			</a>
			<div class="collapse multi-collapse show section-collapsible" id="metadataCollapse">
				{if $editMetadata || !$syllabus->inDataSource}
					{include file="partial:_metadata.edit.html.tpl"}
				{else}
					{include file="partial:_metadata.view.html.tpl"}
				{/if}
			</div>
		</div>
		<!-- END Metadata Section -->


	<!-- BEGIN Sort Container -->
	<div class="sort-container">

		<!-- Cycle through existing sections and render their view templates -->
		{foreach $syllabusVersion->getSectionVersionsWithExt(true) as $i => $sectionVersion}
			<!-- Check if not rendering a section that is currently being edited. -->
			{if !$currentSectionVersion || ($currentSectionVersion->id != $sectionVersion->id)}
				
				{assign var=ext value=$sectionVersion->extension}
				{assign var=extName value=$ext::getExtensionName()}
				{assign var=sectionVersionId value=$sectionVersion->id}

			<div class="sort-item editor-{$extName} mt-3" id="section{$extName}{$i}">
				<div class="d-block-inline bg-light p-2 section-collapse-link dragdrop-handle" data-toggle="collapse" href="#{$extName}Collapse{$i}" aria-expanded="false" aria-controls="{$extName}Collapse{$i}">
					<i class="fas fa-bars fa-2x dragdrop-handle mr-2"></i>
					<a class="d-block-inline p-3" data-toggle="collapse" href="#{$extName}Collapse{$i}"><div class="text-left d-inline-block" id="{$extName}Heading">
						<span class="mb-0 section-title">
							<strong>{$sectionVersion->title}</strong><small><i class="fas fa-chevron-down text-dark pl-2"></i></small>
						</span></div></a>
					{if $sectionVersion->description}<small class="text-dark">{$sectionVersion->description}</small>{/if}
				</div>

				<!-- <input type="hidden" name="section[versionId]" value="{$sectionVersionId}"> -->
				<input type="hidden" name="section[realClass][{$sectionVersionId}]" value="{$realSectionClass}">
				<input type="hidden" name="section[extKey][{$sectionVersionId}]" value="{$ext->getExtensionKey()}">
				<input type="hidden" name="section[properties][sortOrder][{$sectionVersionId}]" value="{$i+1}" class="sort-order-value" id="form-field-{$i+1}-sort-order">
				<input type="hidden" name="section[properties][readOnly][{$sectionVersionId}]" value="{if $genericSection->readOnly}true{else}false{/if}">
				<input type="hidden" name="section[properties][log][{$sectionVersionId}]" value="{$genericSection->log}">

				<div class="collapse multi-collapse show section-collapsible" id="{$extName}Collapse{$i}">
					<div class="card card-outline-secondary rounded-0">
						<div class="card-body">
							{include file="{$ext->getViewFragment()}"}
						    <div class="form-group row">
						        <label class="col-lg-3 col-form-label form-control-label"></label>
						        <div class="col-lg-9 d-flex flex-row-reverse">
						            <input class="btn btn-info" type="submit" name="command[editsection][{$sectionVersionId}]" value="Edit" />
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
			{/if}
			<!-- END check if not rendering a section that is currently being edited. -->
		{/foreach}
		<!-- END cycle through existing sections and render their view templates -->



		{if !$syllabus->inDataSource && $realSection}
			<div class="alert alert-danger mt-3" role="alert">
				You need to save the syllabus metadata before adding a section. See above.
			</div>
		{elseif $realSection}
			{assign var=extName value=$sectionExtension::getExtensionName()}
			{assign var=displayName value=$sectionExtension->getDisplayName()}
			{if $currentSectionVersion}
				{assign var=sectionVersionId value=$currentSectionVersion->id}
			{else}
				{assign var=sectionVersionId value='new'}
			{/if}
		<br>
		<div class="editor-{$extName} border border-warning rounded-0 p-1" id="section{$extName}Edit">
			<div class="d-block-inline bg-light p-2 section-collapse-link dragdrop-handle" data-toggle="collapse" href="#{$extName}CollapseEdit" aria-expanded="false" aria-controls="{$extName}CollapseEdit">
				<i class="fas fa-bars fa-2x dragdrop-handle mr-2"></i>
				<a class="d-block-inline p-3" data-toggle="collapse" href="#{$extName}CollapseEdit"><div class="text-left d-inline-block" id="{$extName}Heading">
					<span class="mb-0 section-title">
						<strong>{if $currentSectionVersion->title}{$currentSectionVersion->title}{else}{$displayName}{/if}</strong><small><i class="fas fa-chevron-down text-dark pl-2"></i></small>
					</span></div></a>
				{if $currentSectionVersion->description}<small class="text-dark">{$currentSectionVersion->description}</small>{/if}
				{if $currentSectionVersion}<span class="float-right"><small class="badge badge-default">Section Version #{$currentSectionVersion->normalizedVersion}</small></span>{/if}
			</div>


	        {if $sectionExtension->getAddonFormFragment()}
				{include file="{$sectionExtension->getAddonFormFragment()}"}
	        {/if}

			<input type="hidden" name="section[versionId]" value="{$sectionVersionId}">
			<input type="hidden" name="section[realClass][{$sectionVersionId}]" value="{$realSectionClass}">
			<input type="hidden" name="section[extKey][{$sectionVersionId}]" value="{$sectionExtension->getExtensionKey()}">
			<input type="hidden" name="section[properties][sortOrder][{$sectionVersionId}]" value="{if $genericSection->sortOrder}{$genericSection->sortOrder}{else}{$syllabusVersion->sectionCount + 1}{/if}">
			<input type="hidden" name="section[properties][readOnly][{$sectionVersionId}]" value="{if $genericSection->readOnly == 'true' || $genericSection->readOnly == true}true{else}false{/if}">
			<input type="hidden" name="section[properties][log][{$sectionVersionId}]" value="{$genericSection->log}">
			
			<div class="collapse multi-collapse show" id="{$extName}CollapseEdit">
				<div class="section-metadata bg-light">
		            <div class="text-center mb-3">
		                <h4 class="">{if $genericSection->title}{$genericSection->title}{else}{$displayName}{/if} Title & Description Text</h4>
		            </div>
		            <div class="form-group row">
		                <label class="col-lg-3 col-form-label form-control-label">Section Title & Sidebar Link Name</label>
		                <div class="col-lg-9">
		                    <input class="form-control" type="text" name="section[generic][{$sectionVersionId}][title]" value="{if $currentSectionVersion->title}{$currentSectionVersion->title}{else}{$displayName}{/if}">
							<small id="{$extName}HelpBlock" class="form-text text-muted ml-1">
								The title field here will be the main header for the section, as well as the link text displayed on the left sidebar. The description will be the section intro if set. {if !$sectionExtension->getHelpText()} {$sectionExtension->getHelpText()}{/if}
							</small>
							<div class="form-check ml-1">
								<input name="section[properties][isAnchored][{$sectionVersionId}]" class="form-check-input" type="checkbox" id="sectionIsAnchored" 
								{if !$currentSectionVersion || $genericSection->isAnchored}checked value="true"{/if}>
								<label class="form-check-label" for="sectionIsAnchored">
									Include title in sidebar quick-links.
								</label>
							</div>
		                </div>
		            </div>
		            <div class="form-group row">
		                <label class="col-lg-3 col-form-label form-control-label">Section Description</label>
		                <div class="col-lg-9">
		                    <input class="form-control" type="text" name="section[generic][{$sectionVersionId}][description]" value="{if $currentSectionVersion->description}{$currentSectionVersion->description}{/if}">
		                </div>
		            </div>
		        </div>
				{include file="{$sectionExtension->getEditFormFragment()}"}
			</div>
		</div>
		{/if}
		<!-- END if $realSection (section being edited) -->
		
	</div>
	<!-- END Sort Container -->


		<!-- Main Syllabus Editor Controls Bottom -->
		{if $sections && (count($sections) > 1)}
		<div class="editor-main-controls-bottom mt-3 form-inline" id="editorMainControlsBottom">
			{if $syllabus->inDataSource}
			<div class="editor-controls-left">
				<select name="addSection" class="form-control">
					<option value="">Choose Section to Add...</option>
				{foreach $sectionExtensions as $ext}
					<option value="{$ext->getRecordClass()}">{$ext->getDisplayName()}</option>
				{/foreach}
				</select>
				<input class="btn btn-primary" type="submit" name="command[addsection]" value="Add Section" />
			</div>
			{/if}
			<div class="editor-controls-right ml-auto">
				<input class="btn btn-success" type="submit" name="command[savesyllabus]" value="Save Syllabus" id="globalSave" />
				<input class="btn btn-secondary" type="submit" name="command[previewsyllabus]" value="Preview" />
				<button type="button" class="btn btn-link btn">Cancel</button>						
			</div>
		</div>
		{/if}
		<!-- END Main Syllabus Editor Controls Bottom -->


		{generate_form_post_key}
	</form>
	<!-- END Sections Edit/View Form -->

</div>
<!-- END Editor Container -->

</main>
<!-- END Main Region -->

</div>
<!-- END Page Container -->