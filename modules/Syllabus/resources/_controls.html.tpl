<div class="editor-main-controls-{$position|lcfirst} {if $position == 'Top'}mb-3{else}mt-3{/if} form-inline bg-light" id="editorMainControls{$position}">
	{if $syllabus->inDataSource}
	<div class="editor-controls-left mr-auto">
		<select name="addSection{$position}" class="form-control">
			<option value="">Choose Section to Add...</option>
		{foreach $sectionExtensions as $ext}
			<option value="{$ext->getRecordClass()}">{$ext->getDisplayName()}</option>
		{/foreach}
		</select>
		<input class="btn btn-primary" type="submit" name="command[addsection]" value="Add Section" />
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
	<div class="editor-controls-right ml-auto">
		<input class="btn btn-success" type="submit" name="command[savesyllabus]" value="Save Syllabus" id="globalSave" />
		<a class="btn btn-dark" href="syllabus/{$syllabus->id}/view" target="_blank">View</a>
		<a href="{$smarty.server.REQUEST_URI}" class="btn btn-outline-danger">Cancel</a>
	</div>
</div>