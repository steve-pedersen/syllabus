<div class="editor-main-controls editor-main-controls-{$position|lcfirst} {if $position == 'Top'}mb-3{else}mt-3{/if} form-inline bg-light" id="editorMainControls{$position}">

<div class="row">
	{if $syllabus->inDataSource}
	<div class="editor-controls-left col-xl-9">
		<div class="row">
			<div class="col-md-3 d-block">
				<h3 class="mt-0 mb-4 pt-0 mr-3" style="text-decoration:underline;">Add Section:</h3>
				{if $position == 'Top'}
				<div class="editor-controls-header">
				{else}
				<div class="editor-controls-left">
				{/if}
				{if $organization}
					<span class="text-dark">
						<strong>[{$organization->name} Template]</strong>
					</span>
				{/if}
				</div>
			</div>
			<div class="col-md-9">
			{foreach $sectionExtensions as $ext}
				{assign var=canHaveMultiple value=true}
				{foreach $sectionVersions as $sv}
					{if $sv->extension->getExtensionKey() == $ext->getExtensionKey() && !$ext->canHaveMultiple()}{assign var=canHaveMultiple value=false}{/if}
				{/foreach}
				{assign var=activeSection value=false}
				{if $sectionExtension && $sectionExtension->getExtensionName() == $ext->getExtensionName()}{assign var=activeSection value=true}{/if}


				{strip}
				{if !$canHaveMultiple && !$activeSection}
					<button class="px-2 mb-2 py-0 btn btn-outline-muted disabled" disabled type="submit" name="add" value="{$ext->getExtensionName()}" form="addSection" data-toggle="tooltip" data-placement="bottom" title="You can only have one instance of this section type.">
					<img src="{$ext->getLightIcon()}" class="img-fluid mr-1" style="max-height:1.5em;margin-bottom:1px;">
						<span class="text-muted pr-1">{$ext->getDisplayName()}</span>
					</button>
				{else}
					<button class="px-2 mb-2 py-0 btn {if !$activeSection}btn-outline-secondary{else}btn-secondary text-white{/if}" type="submit" name="add" value="{$ext->getExtensionName()}" form="addSection">
					<img src="{$ext->getDarkIcon()}" class="img-fluid mr-1" style="max-height:1.5em;margin-bottom:1px;">
						<span class="available pr-1">{$ext->getDisplayName()}</span>
					</button>
				{/if}
				{/strip}

			{/foreach}
			</div>


		</div>
	</div>
	{/if}
	
	<div class="{if $syllabus->inDataSource}col-xl-3{else}col-xl-12{/if} text-right d-block">
		<div class="editor-controls-right d-inline-block py-3 text-center">
			<button class="btn btn-success  my-1" type="submit" name="command[savesyllabus]" id="globalSave">
				Save
			</button>
			<a class="btn btn-dark my-1" id="viewFromEditor" href="syllabus/{$syllabus->id}/view">
				View
			</a>
			{if $syllabus->inDataSource}
			<a sr-only="Delete" class="btn btn-danger my-1" id="viewFromEditor" href="{$routeBase}syllabus/{$syllabus->id}/delete">
				<i class="fas fa-trash"></i>
			</a>
			{/if}
			<a href="{if $syllabus->inDataSource}syllabus/{$syllabus->id}{else}{$smarty.server.REQUEST_URI}{/if}" class="btn btn-default my-1">Cancel</a>
		</div>
<!-- 		<div class="">
			<button class="btn btn-link accordion-collapse-all align-bottom">Collapse all sections</button>
		</div> -->
	</div>

</div> <!-- End row -->

</div>