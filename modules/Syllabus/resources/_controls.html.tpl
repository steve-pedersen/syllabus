<div class="editor-main-controls editor-main-controls-{$position|lcfirst} {if $position == 'Top'}mb-3{else}mt-3{/if} form-inline bg-light" id="editorMainControls{$position}">

{if $syllabus->inDataSource}
<div class="editor-controls-left mr-auto d-inline-block">
	<h3 class="mt-0 mb-4 pt-0" style="text-decoration:underline;">Add Section:</h3>
{foreach $sectionExtensions as $ext}
	{assign var=canHaveMultiple value=true}
	{foreach $sectionVersions as $sv}
		{if $sv->extension->getExtensionKey() == $ext->getExtensionKey() && !$ext->canHaveMultiple()}{assign var=canHaveMultiple value=false}{/if}
	{/foreach}
	{assign var=activeSection value=false}
	{if $sectionExtension && $sectionExtension->getExtensionName() == $ext->getExtensionName()}{assign var=activeSection value=true}{/if}


<!-- <div class=""> -->
	{strip}
	{if !$canHaveMultiple}
	<button class="px-2 py-0 btn {if !$activeSection}btn-outline-muted{else}btn-secondary text-white{/if} disabled" disabled type="submit" name="add" value="{$ext->getExtensionName()}" form="addSection" data-toggle="tooltip" data-placement="bottom" title="You can only have one instance of this section type.">
	<img src="{$ext->getLightIcon()}" class="img-fluid mr-1" style="max-height:1.5em;margin-bottom:1px;">
		<span class="text-muted pr-1">{$ext->getDisplayName()}</span>
	</button>
	{else}
	<button class="px-2 py-0 btn {if !$activeSection}btn-outline-secondary{else}btn-secondary text-white{/if}" type="submit" name="add" value="{$ext->getExtensionName()}" form="addSection">
	<img src="{$ext->getDarkIcon()}" class="img-fluid mr-1" style="max-height:1.5em;margin-bottom:1px;">
		<span class="available pr-1">{$ext->getDisplayName()}</span>
	</button>
	{/if}
	{/strip}
<!-- </div> -->

{/foreach}
</div>


	{if $position == 'Top'}
	<div class="editor-controls-header mx-auto text-center">
	{else}
	<div class="editor-controls-left">
	{/if}
		<span class="text-primary">
			{if $organization}
				<strong>[{$organization->name} Template]</strong>
			{/if}
		</span>
	</div>
{/if}

<div class="editor-controls-right ml-auto d-inline-block">
	{if $syllabus->inDataSource}
		
	{/if}
	<button class="btn btn-success " type="submit" name="command[savesyllabus]" id="globalSave">
		<!-- <i class="far fa-save mr-1"></i>  -->
		Save
	</button>
	<a class="btn btn-dark " href="syllabus/{$syllabus->id}/view" target="_blank">
		<!-- <i class="far fa-eye mr-1"></i>  -->
		View
	</a>
	<a href="{if $syllabus->inDataSource}syllabus/{$syllabus->id}{else}{$smarty.server.REQUEST_URI}{/if}" class="btn btn-default">Cancel</a>
</div>

</div>