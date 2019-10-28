<div class="container-fluid">
<h1 class="">{if $group->id}Edit{else}New{/if} Group</h1>
<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>

<form action="{$smarty.server.REQUEST_URI|escape}" method="post" class="mt-3">
	
    <div class="form-row py-3 row-2">
        <div class="col-md-4 mb-3 website">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" value="{if $group->name}{$group->name}{/if}" >
        </div>
        <div class="col-md-4 mb-3 website">
            <label for="abbreviation">Abbreviation</label>
            <input type="text" class="form-control" name="abbreviation" value="{if $group->abbreviation}{$group->abbreviation}{/if}">
        </div>
		<div class="col-md-4 pl-md-4">
			<label for="isSystemGroup">System Group?</label>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="isSystemGroup" id="isSystemGroup1" value="1" {if $group->isSystemGroup || !$group->id}checked{/if}>
				<label class="form-check-label" for="isSystemGroup1">
					Yes
				</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="isSystemGroup" id="isSystemGroup2" value="0" {if !$group->isSystemGroup && $group->id}checked{/if}>
				<label class="form-check-label" for="isSystemGroup2">
					No
				</label>
			</div>
			<small id="isSystemGroupHelpBlock" class="form-text text-muted">
				System groups are able to create seed content for all faculty syllabi.
			</small>
		</div>
    </div>
    <div class="form-row py-3 row-3">
        <div class="col-md-12 mb-3 about">
            <label for="about">About this group</label>
            <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="description" rows="4">{$group->description}</textarea>
        </div>
    </div>		

	<div class="form-group my-5 d-flex">
		{generate_form_post_key}
		<button type="submit" class="command-button btn btn-primary " name="command[save]">Save Group</button>
		<a class="btn btn-default  mr-auto" href="{$routeBase}">Cancel</a>
		{if $pManager}
			<button type="submit" class="command-button btn btn-danger ml-auto" name="command[delete]">Delete This Group</button>
		{/if}
	</div>
</form>

</div>
