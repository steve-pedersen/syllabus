

<div class="campus-resources">
<div class="bg-sfstate-blue-32 py-4" id="topSplash">
<div class="container ">
<h1 class="mt-1" style="color: #fff">
	<span class="pb-2">Student Resources at SF State</span>
</h1>
<div class="wrap pb-2"><div class="left h1"></div><div class="right"></div></div>
</div>

<div class="container py-3">
<div class="row spotlight">
	<div class="col-12 my-2 story dont-break-out">
	    <div class="story__text">
	    	<div class="row">
	    		<div class="col-sm-12 col-md-4 col-lg-3 text-center align-middle">
	    			<span class="d-inline-block align-middle h-100">
	    			<img src="{$spotlight->imageSrc}" alt="{$spotlight->title} logo" class="story__img img-fluid align-middle mb-3 mb-md-0 mt-1" style="">
	    			</span>
	    		</div>
	    		<div class="col-sm-12 col-md-8 col-lg-9">
			        <h3 class="heading-tertiary u-margin-bottom-small">
			        	{$spotlight->title}{if $spotlight->abbreviation} 
			        	<small>({$spotlight->abbreviation})</small>{/if}
			        </h3>
			        <p>
			            {$spotlight->description}
			        </p>
			        <p>
			            <strong class="mr-1">Website: </strong>
			            <a href="{$spotlight->url}" target="_blank">{$spotlight->url}</a>
			        </p>
			        {if $spotlight->tags && $spotlight->tags->count() > 0}
			        <p class="card-text mt-1">
			            <small>
			                <strong class="pr-2">Categories: </strong>
			                {foreach $spotlight->tags as $tag}
			                    {$tag->name}{if !$tag@last}, {/if}
			                {/foreach}
			            </small>
			        </p>
			        {/if}	    			
	    		</div>
	    	</div>
	    </div>
	</div>
</div>
</div>

</div>


<section class="color ss-style-bigtriangle"> 
</section>

<svg id="bigTriangleColor" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 102" preserveAspectRatio="none">
    <path d="M0 0 L50 100 L100 0 Z" />
</svg>

<div class="">

<div class="container mt-5">
<div class="row my-3">
	<div class="col-md-12 col-lg-8">
	<h2>All campus resources</h2>
	<p class="text-muteds">Listed below are many of the resources available to students at SF State.</p>
	</div>

{if $tags}
	<div class="col-md-12 col-lg-4">
	<select class="form-control" id="filterResources">
		<option value="" default>Select category...</option>
		{foreach $tags as $tag}
			<option value="{$tag->name}" {if $filter|ucfirst == $tag->name}selected{/if}>{$tag->name}</option>
		{/foreach}
	</select>
	<button class="btn btn-link" id="removeFilterResources" style="display:none;">Remove filter</button>
	</div>
{/if}

</div>


<div class="row all-resources">
{foreach $resources as $i => $resource}	
	<div class="col-lg-4 col-md-6 my-2 resource {foreach $resource->tags as $tag}{$tag->name} {/foreach}">
		<div class="card" style="min-height:12rem;">
			<div class="card-body" id="{$i}">
				<input type="hidden" value="{$resource->id}" id="campusResourceId{$i}">
				<div class="media campus-resource">
					<img class="align-self-center mr-2 ml-0 img-thumbnail" id="image{$i}" src="{$resource->imageSrc}" alt="{$resource->title}" style="max-width:5rem;">
					<div class="media-body pl-1 d-block">
						<h5 class="card-title" id="title{$i}">{$resource->title}{if $resource->abbreviation} <small>({$resource->abbreviation})</small>{/if}</h5>
						<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
						<div class="card-text text-muted" id="text{$i}">
							{$resource->description}
						</div>
						{if $resource->tags}
						<p class="card-text text-muted" id="tags{$i}" style="display:none;">
							{if $resource->tags->count() > 0}
							<small>
								<strong class="pr-1">Tags: </strong>
								{foreach $resource->tags as $tag}
									{$tag->name}{if !$tag@last}, {/if}
								{/foreach}
							</small>
							{/if}
						</p>
						{/if}
						<span id="url{$i}" class="hidden" hidden>{$resource->url}</span>
						<div class="align-bottom">
						<button id="preview{$i}" class="btn btn-sfstate-purple-1 btn-sm" data-toggle="modal" data-target="#resourcePreviewModal">
							More Info
						</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
{/foreach}

</div>
</div>

</div>


<!-- Preview Modal -->
<div class="modal fade" id="resourcePreviewModal" tabindex="-1" role="dialog" aria-labelledby="resourceTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="resourceTitle"></h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<div class="modal-body">
		<div class="container-fluid">
			<div class="row mb-3">
				<div class="col-sm-12 col-md-4 col-lg-6 text-center">
					<img class="img-thumbnail mh-50 border-0" id="resourceImage" src="" alt="">
				</div>
				<div class="col-sm-12 col-md-8 col-lg-6 d-block dont-break-out">
					<span id="resourceDescription"></span>
					<span class="d-block mt-2 align-bottom">
						<strong class="d-block">Website: </strong><a target="_blank" href="" id="resourceUrl" style="letter-spacing:0.8px;"></a>
					</span>
					<p id="resourceTags" class="mt-2">
						
					</p>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
    </div>
  </div>
</div>