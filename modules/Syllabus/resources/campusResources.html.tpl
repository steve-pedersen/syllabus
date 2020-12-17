<div class="col p-3">
<h1 class="pb-3 border-bottom">Manage Campus Resources</h1>
<h2>Upload any images you want to use before saving the resource below</h2>
<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" id="uploadResourceImage" enctype="multipart/form-data">
    <div class="form-group upload-form mb-5">
        <label for="image" class="field-label field-linked">Upload thumbnail image</label>       
        <input class="form-control-file" type="file" name="file" id="image" />
        {foreach item='error' from=$errors.image}<div class="error">{$error}</div>{/foreach}
        <div class="col-xs-12 help-block text-center">
            <p id="type-error" class="bg-danger" style="display:none"><strong>There was an error with the type of file you are attempting to upload.</strong></p>
        </div>          
    </div>
    <div class="form-group mt-5">
        <div class="col-xs-12">
            <input type="submit" name="command[upload]" id="saveResource" value="Upload Image" class="btn btn-secondary" />
        </div>
    </div> 
{generate_form_post_key}
</form>
<hr>
<h2 class="my-5">Add/Edit Campus Resources</h2>
<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" id="editCampusResources">
	<div class="col">
		<div class="form-row">
			<div class="form-group col-md-7">
				<label for="title">Title</label>
				<input type="text" class="form-control" id="title" name="resource[title]" placeholder="Title" value="{$resource->title}">
			</div>
			<div class="form-group col-md-3">
				<label for="abbreviation">Abbreviation</label>
				<input type="text" class="form-control" id="abbreviation" name="resource[abbreviation]" placeholder="Abbreviation" value="{$resource->abbreviation}">
			</div>
			<div class="form-group col-md-2">
				<label for="sortOrder">Sort Order</label>
				<input type="text" class="form-control" id="sortOrder" name="resource[sortOrder]" value="{if $resource->sortOrder}{$resource->sortOrder}{else}{$bottommostPosition + 1}{/if}">
			</div>
		</div>
		<div class="form-group">
			<label for="description">Description</label>
			<textarea type="text" class="wysiwyg wysiwyg-basic" id="description" name="resource[description]" placeholder="This resource helps students by..." rows="3">{$resource->description}</textarea>
		</div>
		<div class="form-group">
			<label for="url">Website URL</label>
			<input type="text" class="form-control" id="url" name="resource[url]" placeholder="https://sfsu.edu" value="{$resource->url}">
		</div>
		<div class="col-xs-12 form-group">
			<label for="image">Image Thumbnail</label>
			<select class="form-control" name="resource[imageId]" id="image">		
				<option value="">Choose a filename...</option>
			{foreach $files as $image}
				<option data-thumbnail="{$image->downloadUrl}" value="{$image->id}" {if $resource->imageId == $image->id}selected{/if}>
					{if $image->title}{$image->title}{else}{$image->remoteName}{/if}
				</option>
			{/foreach}
			</select>
		</div>
		{if $tags}
		<div class="col-xs-12 form-group">
			<label for="tags">Tags</label>
			{foreach $tags as $tag}

			<div class="form-check d-flex border-bottom pt-2">
				<input class="form-check-input" type="checkbox" value="{$tag->id}" name="tags[{$tag->id}]" id="tags[{$tag->id}]}" {if $resource && $resource->tags->has($tag)}checked{/if}>
				<label class="form-check-label d-inline" for="tags[{$tag->id}]}">
					{$tag->name}
				</label>
				<button type="submit" class="ml-auto btn btn-link text-danger p-1" name="command[deletetag]" value="{$tag->id}">X</button>
			</div>
			{/foreach}
		</div>
		{/if}
		<div class="col-xs-6 form-group">
			<label for="tags[new]">Add new tag</label>
			<input type="text" class="form-control" id="tag" name="tags[new]">
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


{if $campusResources}
<hr>
<h2 class="my-5">Campus Resources</h2>
<form action="{$smarty.server.REQUEST_URI}" method="post" class="form" role="form" id="configureCampusResources">
	<div class="container-fluid sort-container mb-5 campus-resources row">
		{foreach $campusResources as $i => $campusResource}
		<div class="col-lg-4 col-md-6 mb-5 sort-item">
			<div class="card h-100">
				<div class="ml-auto w-100 bg-light text-right border-bottom dragdrop-handle">
					<i class="fas fa-bars p-2" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
					<input type="hidden" class="sort-order-value" name="sortOrder[{$campusResource->id}]" value="{$i+1}">
				</div>
				<div class="card-body">
					<div class="media campus-resource">
						<img class="align-self-center mr-2 ml-0 img-fluid w-25" src="{$campusResource->imageSrc}" alt="{$campusResource->title}">
						<div class="media-body pl-1">
							<h5 class="card-title">{$campusResource->title}{if $campusResource->abbreviation} <small>({$campusResource->abbreviation})</small>{/if}</h5>
							<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
							<p class="card-text text-muted">
								{$campusResource->description}
								<small class="align-bottom d-block">
									<strong class="mr-2">Website: </strong><a target="_blank" href="{$campusResource->url}">{$campusResource->title}</a>
								</small>
							</p>

							{if $campusResource->tags}
							<!-- <span>Tags</span> -->
							<p class="text-muted">
								{foreach $campusResource->tags as $tag}
									<small class="">{$tag->name}{if !$tag@last}, {/if}</small>
								{/foreach}
							</p>
							{/if}
							<div class="">
								<!-- <button class="btn btn-info btn-sm" target="_blank" href="{$campusResource->url}">Preview</button> -->
								<a class="btn btn-info btn-sm" href="admin/syllabus/resources?edit={$campusResource->id}" id="editResource">Edit</a>
								<input class="btn btn-danger btn-sm" type="submit" name="command[delete][{$campusResource->id}]" id="saveResource" value="Delete" />
							</div>
						</div>
					</div>
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