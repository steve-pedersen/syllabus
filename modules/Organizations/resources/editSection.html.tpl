<div class="container-fluid">
	<h1 class="mb-4">{$sectionExtension->getDisplayName()}</h1>
	<form action="{$smarty.server.REQUEST_URI}" method="post" form="groupForm">
		{assign var=groupForm value=true}
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Section Name</label>
            <div class="col-lg-9">
                <input class="form-control" type="text" name="section[title]" value="{if $importable->title}{$importable->title}{else}{$sectionExtension->getDisplayName()}{/if}">
				<small id="{$extName}HelpBlock" class="form-text text-muted ml-1">
					Give this section a unique and clear title, which will allow instructors to easily identify what the content may be.
				</small>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label" for="importable">Available for import?</label>
            <div class="col-lg-9">
				<div class="form-check">
					<input class="form-check-input" type="radio" name="importable" id="importable1" value="1" {if $importable->importable || !$importable->id}checked{/if}>
					<label class="form-check-label" for="importable1">
						Yes
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="importable" id="importable2" value="0" {if !$importable->importable && $importable->id}checked{/if}>
					<label class="form-check-label" for="importable2">
						No
					</label>
				</div>
				<small id="importableHelpBlock" class="form-text text-muted">
					When set to <strong>Yes</strong>, instructors will be able to use this content.
				</small>
            </div>
        </div>

		<div class="real-section-editor">{include file="{$sectionExtension->getEditFormFragment()}"}</div>
	    <div class="form-group row pt-3 mt-5">
	        <label class="col-lg-3 col-form-label form-control-label"></label>
	        <div class="col-lg-9">
				<div class="d-flex">
	                <input class="btn btn-success" type="submit" name="command[savesection]" value="Save Section" />
	                <a href="groups/{$group->id}/sections" class="btn btn-outline-default mx-1">Cancel</a>
	                <input class="btn btn-danger ml-auto" type="submit" name="command[deletesection]" value="Delete Section" />				
				</div>
	        </div>
	    </div>
	    {generate_form_post_key}
	</form>
</div>