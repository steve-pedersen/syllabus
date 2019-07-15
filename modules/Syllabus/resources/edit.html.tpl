<div class="row editor-main-container">
	<main role="main" class="col-md-10 ml-sm-auto col-lg-10 mt-0">
		{if $isUniversityTemplate}
		<div class="text-center alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Attention!</strong> 
			You are editing the <a href="admin/templates/university" class="alert-link">University Base Template</a>. 
			Proceed with caution.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		{elseif $isDetachedSyllabus}
		<div class="text-center alert alert-info alert-dismissible fade show" role="alert">
			<strong>Attention!</strong> 
			You are editing a syllabus that isn't based on the University Base Template.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		{/if}

		<div class="syllabus-editor " id="syllabusEditor">	
		
		<!-- ADD SECTION FORM -->
		<form action="{$smarty.server.REQUEST_URI}" method="get" class="form" role="form" id="addSection">
		</form>
		
		<!-- MAIN FORM -->
		<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" id="viewSections">		
			
			<!-- MAIN CONTROLS - TOP -->			
			{assign var=position value='Top'}
			{include file="partial:_controls.html.tpl"}

			{if $syllabusVersion->inDataSource}
			<input type="hidden" name="syllabusVersion[id]" value="{$syllabusVersion->id}">
			{/if}

			<div class="editor-metadata">
				<a class="d-block bg-white border p-2 section-collapse-link- {if !$editMetadata && $syllabus->inDataSource}collapsed{/if}" 
					data-toggle="collapse" href="#metadataCollapse" role="button" aria-expanded="false" aria-controls="metadataCollapse">			
					<div class="text-left d-inline-block" id="metadataHeading"> 
						<span class="mb-0 section-title text-dark">
							<strong>Syllabus Metadata</strong><small><i class="fas fa-chevron-right text-dark pl-2"></i></small>
						</span>
						 - <small class="text-dark">Information about this syllabus that is not displayed inside the syllabus itself.</small>
					</div>
				</a>
				<div class="collapse {if $editMetadata || !$syllabus->inDataSource}show{/if} section-collapsible" id="metadataCollapse">
					{if $editMetadata || !$syllabus->inDataSource}
						{include file="partial:_metadata.edit.html.tpl"}
					{else}
						{include file="partial:_metadata.view.html.tpl"}
					{/if}
				</div>
			</div>
	
			<div class="sort-container">				
			{if $sectionVersions}
				{assign var=counter value=0}
				{foreach $sectionVersions as $i => $sectionVersion}
					
					<!-- Render view template for existing section -->
					{if !$currentSectionVersion || ($currentSectionVersion->id != $sectionVersion->id)}

						{include file="partial:_section.view.html.tpl"}
						{assign var=counter value="{$counter + 1}"}

					<!-- Editing an existing section -->
					{elseif ($currentSectionVersion && ($currentSectionVersion->id == $sectionVersion->id)) || ($syllabus->inDataSource && $realSection)}
						
						{include file="partial:_section.edit.html.tpl"}
					{/if}
				{/foreach}
				
				<!-- At least 1 section exists already, plus 1 brand new is being created -->
				{if (!$currentSectionVersion || ($syllabus->inDataSource && $realSection)) && ($realSection && count($sectionVersions) == $counter)}	
					
					{include file="partial:_section.edit.html.tpl"}
				{/if}

			<!-- No sections exist yet, but 1 brand new is being edited -->
			{elseif $syllabus->inDataSource && $realSection}
				
				{include file="partial:_section.edit.html.tpl"}

			{elseif !$syllabus->inDataSource && $realSection}
				<div class="alert alert-danger mt-3" role="alert">
					You need to save the syllabus metadata before adding a section. See above.
				</div>
			{/if}				
			</div>

			{if $sectionVersions && (count($sectionVersions) > 1)}
				<!-- MAIN CONTROLS - TOP -->
				{assign var=position value='Bottom'}
				{include file="partial:_controls.html.tpl"}
			{/if}

			{generate_form_post_key}
		</form>
		</div>
	</main>

	{if $editUri}
	<input type="hidden" value="{$editUri}" id="editUri">
	{/if}
	
	<nav class="col-md-2 d-none d-md-block anchor-links-sidebar bg-light text-dark px-0">
		<div class="sidebar-sticky">
			<ul class="nav flex-column">
				<div class="mb-3 border-bottom text-right">
					<a class="collapse-all p-2 expanded d-inline-block mt-1 mr-1 text-primary" href="#">
						<i class="fas fa-compress collapsed"></i>
						<i class="fas fa-expand expanded"></i>
						<span class="ml-2">Hide/Show Sections</span>
					</a>
				</div>
				<li class="nav-item sidebar-anchor-item">
					<a class="nav-link my-3" href="{$smarty.server.REQUEST_URI}#goToTop">
					<strong><i class="fas fa-arrow-up pr-2"></i> Go To Top</strong>
					</a>
				</li>
			{foreach $sectionVersions as $i => $sectionVersion}
				{if ($sectionVersion->resolveSection()->id != $realSection->id) && $sectionVersion->isAnchored}
					{assign var=ext value=$sectionVersion->extension}
					{assign var=extName value=$ext::getExtensionName()}
				<li class="nav-item sidebar-anchor-item">
					<a class="nav-link" href="{$smarty.server.REQUEST_URI}#section{$extName}{$i}">
					{if $sectionVersion->title}{$sectionVersion->title}{else}{$ext->getDisplayName()}{/if}
					</a>
				</li>
				{/if}				
			{/foreach}
			{if $realSection}
				{assign var=extName value=$sectionExtension::getExtensionName()}
				{assign var=displayName value=$sectionExtension->getDisplayName()}
				<li class="nav-item sidebar-anchor-item">
					<a class="nav-link active" href="{$smarty.server.REQUEST_URI}#section{$extName}Edit">
					{if $currentSectionVersion}{$currentSectionVersion->title}{else}{$displayName}{/if}
					</a>
				</li>
			{/if}

			</ul>
		</div>
	</nav>

</div>