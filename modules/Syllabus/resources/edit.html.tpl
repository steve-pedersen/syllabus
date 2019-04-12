
<div class="row editor-main-container">
	<nav class="col-md-2 d-none d-md-block anchor-links-sidebar bg-dark text-white" >
		<div class="sidebar-sticky mb-3">
			<br><br>
			<!-- <strong><u>Quick Links</u></strong><br> -->
			<br>
			<ul class="nav flex-column">
				<li class="nav-item sidebar-anchor-item">
					<a class="nav-link" href="{$smarty.server.REQUEST_URI}#goToTop">
					<strong><i class="fas fa-arrow-up pr-2"></i> Go To Top</strong>
					</a>
				</li>
			{foreach $sections as $section}
				{if ($section->version->resolveSection()->id != $realSection->id) && $section->isAnchored}
					{assign var=ext value=$section->extension}
					{assign var=extName value=$ext::getExtensionName()}
				<li class="nav-item sidebar-anchor-item">
					<a class="nav-link" href="{$smarty.server.REQUEST_URI}#section{$extName}">
					{if $section->version->title}{$section->version->title}{else}{$ext->getDisplayName()}{/if}
					</a>
				</li>
				{/if}				
			{/foreach}
			{if $realSection}
				{assign var=extName value=$sectionExtension::getExtensionName()}
				{assign var=displayName value=$sectionExtension->getDisplayName()}
				<li class="nav-item sidebar-anchor-item">
					<a class="nav-link active text-white" href="{$smarty.server.REQUEST_URI}#section{$extName}">
					{if $sectionVersion}{$sectionVersion->title}{else}{$displayName}{/if}
					</a>
				</li>
			{/if}
			</ul>
		</div>
	</nav>
		
	<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mt-0">
		<div class="syllabus-editor mt-3" id="syllabusEditor">
	

			<!-- Main Syllabus Editor Controls Top -->
			<div class="editor-main-controls-top mb-3">
				<form action="{$smarty.server.REQUEST_URI}" method="post" class="form-inline" role="form" id="editorMainControlsTop">
					{if $syllabus->inDataSource}
					<div class="editor-controls-left">
						<select name="addSection" class="form-control">
							<option value="false">Choose Section to Add...</option>
						{foreach $sectionExtensions as $ext}
							{if $sections}
								{assign var=extKey value=$ext->getExtensionKey()}
								{foreach $sections as $section}
									{if $section->extension->getExtensionKey() != $extKey}
										<option value="{$ext->getRecordClass()}">{$ext::getDisplayName()}</option>
									{/if}
								{/foreach}
							{else}
								<option value="{$ext->getRecordClass()}">{$ext::getDisplayName()}</option>
							{/if}
						{/foreach}
						</select>
						<input class="btn btn-primary" type="submit" name="command[addsection]" value="Add" />
					</div>
					{/if}
					<div class="editor-controls-right ml-auto">
						<input class="btn btn-success" type="submit" name="command[savesyllabus]" value="Save Syllabus" id="globalSave" />
						<input class="btn btn-secondary" type="submit" name="command[previewsyllabus]" value="Preview" />
						<button type="button" class="btn btn-link btn">Cancel</button>						
					</div>

    				{generate_form_post_key}
				</form>
			</div>
			<!-- End Main Syllabus Editor Controls Top -->


			<!-- Metadata Section -->
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
					{if $editMetadata}
						{include file="partial:_metadata.edit.html.tpl"}
					{elseif $syllabus->inDataSource}
						{include file="partial:_metadata.view.html.tpl"}
					{else}
						{include file="partial:_metadata.edit.html.tpl"}
					{/if}
				</div>
			</div>
			<!-- End Metadata Section -->


			<!-- Cycle through existing sections and render their view templates -->
			{foreach $sections as $section}
			{if ($genericSection->id != $section->id)}
				{assign var=ext value=$section->extension}
				{assign var=extName value=$ext::getExtensionName()}
			<div class="editor-{$extName} mt-3" id="section{$extName}">
				<a class="d-block bg-light p-2 section-collapse-link" data-toggle="collapse" href="#{$extName}Collapse" role="button" aria-expanded="false" aria-controls="{$extName}Collapse">			
					<div class="text-left d-inline-block" id="{$extName}Heading"> 
						<span class="mb-0 section-title">
							<small><i class="fas fa-chevron-down text-dark"></i></small> 
							<strong>{$section->version->title}</strong>
						</span>
						{if $section->version->description} - <small class="text-dark">{$section->version->description}</small>{/if}
					</div>
				</a>
				<div class="collapse multi-collapse show section-collapsible" id="{$extName}Collapse">
					<div class="card card-outline-secondary rounded-0">
						<div class="card-body">
							{include file="{$ext->getViewFragment()}"}
							<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form"id="{$section->version->title}View">
								<input type="hidden" name="syllabusVersion[id]" value="{$syllabusVersion->id}">
								<input type="hidden" name="editSection" value="{$ext->getRecordClass()}">
								<input type="hidden" name="section[version][id]" value="{$section->version->id}">
								<input type="hidden" name="section[properties][sortOrder]" value="{$section->sortOrder}">
								<input type="hidden" name="section[properties][isAnchored]" value="{$section->isAnchored}">
								<input type="hidden" name="section[properties][readOnly]" value="{$section->readOnly}">
								<input type="hidden" name="section[properties][log]" value="{$section->log}">
							    <div class="form-group row">
							        <label class="col-lg-3 col-form-label form-control-label"></label>
							        <div class="col-lg-9 d-flex flex-row-reverse">
							            <input class="btn btn-info" type="submit" name="command[editsection]" value="Edit" />
							        </div>
							    </div>
						    	{generate_form_post_key}
							</form>
						    {if $section->dateModified || $section->dateCreated}
						    <div class="card-footer text-muted">
						        {if $section->dateCreated}<small class="text-muted">Date created - {$section->dateCreated}</small>{/if}
						        {if $section->dateModified}<small class="text-muted">Last edited - {$section->dateModified}</small>{/if}
						    </div>
						    {/if}
						</div>
					</div>
				</div>
			</div>
			{/if}
			{/foreach}
			<!-- End cycle through existing sections and render their view templates -->			


			{if !$syllabus->inDataSource && $realSection}
				<div class="alert alert-danger mt-3" role="alert">
					You need to save the syllabus metadata before adding a section. See above.
				</div>
			{elseif $realSection}
				{assign var=extName value=$sectionExtension::getExtensionName()}
				{assign var=displayName value=$sectionExtension->getDisplayName()}
			<br>
			<div class="editor-{$extName}">
				<a class="d-block bg-light p-2 section-collapse-link" data-toggle="collapse" href="#{$extName}Collapse" role="button" aria-expanded="false" aria-controls="{$extName}Collapse">			
					<div class="text-left d-inline-block" id="{$extName}Heading"> 
						<span class="mb-0 section-title">
							<small><i class="fas fa-chevron-down text-dark"></i></small>
							<strong>{if $genericSection->title}{$genericSection->title}{else}{$displayName}{/if}</strong>
						</span>
						{if $sectionExtension->getHelpText()} - <small class="text-dark">{$sectionExtension->getHelpText()}</small>{/if}
					</div>
				</a>
				<form action="{$smarty.server.REQUEST_URI}" method="post" class="form sectionEditor" role="form" autocomplete="off" id="sectionForm">
					<input type="hidden" name="syllabusVersion[id]" value="{$syllabusVersion->id}">
					{if $sectionVersion}
						<input type="hidden" name="section[version][id]" value="{$sectionVersion->id}">
					{/if}
					<input type="hidden" name="section[real][class]" value="{$realSectionClass}">
					<input type="hidden" name="section[extKey]" value="{$sectionExtension->getExtensionKey()}">
					<input type="hidden" name="section[isNew]" value="{$newSection}">
					{if !$newSection}
						<input type="hidden" name="section[sortOrder]" value="{$genericSection->sortOrder}">
						<input type="hidden" name="section[readOnly]" value="{$genericSection->readOnly}">
					{/if}
					<div class="collapse multi-collapse show" id="{$extName}Collapse">
						<div class="section-metadata bg-light">
				            <div class="text-center mb-3">
				                <h4 class="">{if $genericSection->title}{$genericSection->title}{else}{$displayName}{/if} Title & Description Text</h4>
				            </div>
				            <div class="form-group row">
				                <label class="col-lg-3 col-form-label form-control-label">Section Title & Sidebar Link Name</label>
				                <div class="col-lg-9">
				                    <input class="form-control" type="text" name="section[generic][title]" value="{if $genericSection->title}{$genericSection->title}{else}{$displayName}{/if}">
									<small id="{$extName}HelpBlock" class="form-text text-muted ml-1">
										The title field here will be the main header for the section, as well as the link text displayed on the left sidebar. The description will be the section intro if set. {if !$sectionExtension->getHelpText()} {$sectionExtension->getHelpText()}{/if}
									</small>
									<div class="form-check ml-1">
										<input name="section[isAnchored]" class="form-check-input" type="checkbox" id="sectionIsAnchored" {if $newSection || $genericSection->isAnchored}checked{/if}>
										<label class="form-check-label" for="sectionIsAnchored">
											Include title in sidebar quick-links.
										</label>
									</div>
				                </div>
				            </div>
				            <div class="form-group row">
				                <label class="col-lg-3 col-form-label form-control-label">Section Description</label>
				                <div class="col-lg-9">
				                    <input class="form-control" type="text" name="section[generic][description]" value="{if $genericSection->description}{$genericSection->description}{/if}">
				                </div>
				            </div>
				        </div>
						{include file="{$sectionExtension->getEditFormFragment()}"}
					</div>
					{generate_form_post_key}
				</form>
			</div>
			{/if}


		{if $sections && (count($sections) > 1)}
			<!-- Main Syllabus Editor Controls Bottom -->
			<div class="editor-main-controls-bottom mt-3">
				<form class="form-inline" role="form" id="editorMainControlsBottom">
					<div class="editor-controls-left">
						<select name="section[add]" class="form-control">
							<option value="">Choose Section to Add...</option>
						{foreach $sectionExtensions as $ext}
							<option value="{$ext->getExtensionKey}">{$ext::getDisplayName()}</option>
						{/foreach}
						</select>
						<button type="button" class="btn btn-primary btn">Add Section</button>
					</div>
					<div class="editor-controls-right ml-auto">
						<button type="button" class="btn btn-success btn">Save Syllabus</button>
						<button type="button" class="btn btn-secondary btn">Preview</button>
						<button type="button" class="btn btn-link btn">Cancel</button>						
					</div>
				</form>
			</div>
			<!-- End Main Syllabus Editor Controls Bottom -->
		{/if}

		</div>
	</main>
</div>