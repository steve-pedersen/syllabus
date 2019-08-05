<div class="col p-3">
<h1 class="pb-3 border-bottom">Manage Shared Resources, Guidelines, and Documents</h1>
<h2>Upload any files you want to use before saving the resource below</h2>
<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" id="uploadResourceFile" enctype="multipart/form-data">
    <div class="form-group upload-form mb-5">
        <label for="file" class="field-label field-linked">Upload file</label>       
        <input class="form-control-file" type="file" name="file" id="file" />
        {foreach item='error' from=$errors.file}<div class="error">{$error}</div>{/foreach}
        <div class="col-xs-12 help-block text-center">
            <p id="type-error" class="bg-danger" style="display:none"><strong>There was an error with the type of file you are attempting to upload.</strong></p>
        </div>          
    </div>
    <div class="form-group mt-5">
        <div class="col-xs-12">
            <input type="submit" name="command[upload]" id="saveResource" value="Upload File" class="btn btn-secondary" />
        </div>
    </div> 
{generate_form_post_key}
</form>
<hr>
<h2 class="my-5">Add/Edit Shared Resources, Guidelines, and Documents</h2>
<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" id="editSharedResources">
	<div class="col">
		<div class="form-row">
			<div class="form-group col-md-7">
				<label for="title">Title</label>
				<input type="text" class="form-control" id="title" name="resource[title]" placeholder="Title" value="{$resource->title}">
			</div>
			<div class="form-group col-md-3">
				<label for="iconClass">Icon Class</label>
				<input type="text" class="form-control" id="iconClass" name="resource[iconClass]" placeholder="fas fa-file" value="{$resource->iconClass}">
			</div>
			<div class="form-group col-md-2">
				<label for="sortOrder">Sort Order</label>
				<input type="text" class="form-control" id="sortOrder" name="resource[sortOrder]" value="{if $resource->sortOrder}{$resource->sortOrder}{else}{$bottommostPosition + 1}{/if}">
			</div>
		</div>
		<div class="form-group">
			<label for="description">Description</label>
			<textarea type="text" class="wysiwyg wysiwyg-basic" id="description" name="resource[description]" placeholder="This resource helps students by..." rows="3" value="{$resource->description}">{$resource->description}</textarea>
		</div>
		<div class="form-group form-check">
			<input type="checkbox" class="form-check-input" id="active" name="resource[active]" {if $resource->active != 'false' || $resource->active != false}checked{/if}>
			<label class="form-check-label" for="active">Display on Guidelines & Documents section?</label>
		</div>
		<div class="form-group">
			<label for="url">Website URL</label>
			<input type="text" class="form-control" id="url" name="resource[url]" placeholder="https://sfsu.edu">
		</div>
		<div class="col-xs-12 form-group">
			<label for="file">File</label>
			<select class="form-control" name="resource[fileId]" id="file">		
				<option value="">Choose a filename...</option>
			{foreach $files as $file}
				<option data-thumbnail="{$file->downloadUrl}" value="{$file->id}" {if $resource->fileId == $file->id}selected{/if}>
					{if $file->title}{$file->title}{else}{$file->remoteName}{/if}
				</option>
			{/foreach}
			</select>
		</div>
		<input type="hidden" name="resourceId" value="{$resource->id}">
	    <div class="form-group mt-5">
	        <div class="col-xs-12">
	            <input type="submit" name="command[save]" id="saveResource" value="Save Resource" class="btn btn-primary" />
	        </div>
	    </div> 
	</div>
{generate_form_post_key}
</form>


{if $guideDocs}
<hr>
<h2 class="my-5">Shared Resources, Guidelines, and Documents</h2>
<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" id="configureSharedResources">
	<div class="container-fluid sort-container mb-5 campus-resources row">
		{foreach $guideDocs as $i => $guideDoc}
	<div class="col-lg-4 col-md-6 px-lg-5 mb-3 sort-item">
		<div class="card card-bordered card-bordered-left card-bordered-secondary h-100">
			<div class="ml-auto w-100 bg-light text-right border-bottom dragdrop-handle">
				<i class="fas fa-bars p-2" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
				<input type="hidden" class="sort-order-value" name="sortOrder[{$guideDoc->id}]" value="{$i+1}">
			</div>
			<div class="card-body">
				<h5 class="card-title">
					<i class="{if $guideDoc->iconClass}{$guideDoc->iconClass}{else}far fa-file-alt{/if} mr-1"></i> 
					{$guideDoc->title}
				</h5>
				{if $guideDoc->active}
					<p class="p-2 bg-success"><strong>Displayed</strong></p>
				{else}
					<p class="p-2 bg-warning"><strong>Not displayed</strong></p>
				{/if}
				<p class="card-text text-muted align-middle">
					{$guideDoc->description}
				</p>
				<div class=" bg-white border-0">
				{if $guideDoc->file && !$guideDoc->url}
					<a href="{$guideDoc->fileSrc}" target="_blank" class="btn btn-link text-dark font-weight-bold"><span class=""><i class="fas fa-download"></i></span> Download</a>
				{elseif $guideDoc->url}
					<a href="{$guideDoc->url}" target="_blank" class="btn btn-link text-info font-weight-bold"><span class=""><i class="fas fa-external-link-alt"></i></span> View</a>
				{/if}
				</div>				
			</div>
			<div class="card-footer bg-white border-0">
				<a class="btn btn-info btn-sm" href="admin/syllabus/guidedocs?edit={$guideDoc->id}" id="editResource">Edit</a>
				<input class="btn btn-danger btn-sm" type="submit" name="command[delete][{$guideDoc->id}]" id="saveResource" value="Delete" />
			</div>
		</div>
	</div>
		{/foreach}
	</div>
	<div class="col my-5">
		<input class="btn btn-success" type="submit" name="command[sort]" id="saveResource" value="Save Sort Order" />
	</div>
</div>
{generate_form_post_key}
</form>
{/if}
</div>