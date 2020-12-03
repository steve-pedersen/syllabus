<div class="container campus-resources mb-5">
<h1 class="pb-2 mt-3">SF State Student Resources</h1>
<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
<a href="resources/feed">RSS Feed</a>
<div class="row my-4">
	<div class="col-12 my-2">
		<div class="card h-100 text-center">
			<div class="card-header ">
				<h2 class="card-title mb-0 ">Spotlight</h2>
			</div>
			<div class="card-body text-left">
				<div class="media campus-resource spotlight">
					<img class="align-self-center mr-2 ml-0 img-thumbnail" id="image" src="{$spotlight->imageSrc}" alt="{$spotlight->title} logo">
					<div class="media-body align-self-center pl-1">
						<h3 class="card-title" id="title">{$spotlight->title}{if $spotlight->abbreviation} <small>({$spotlight->abbreviation})</small>{/if}</h3>
						<div class="card-text">
							{$spotlight->description}
						</div>
						<span><strong class="mr-1">Website: </strong>
							<a href="{$spotlight->url}">{$spotlight->url}</a>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<h2>All campus resources</h2>
<p class="text-muted">Listed below are many of the resources available to students at SF State.</p>
	<div class="row ">
{foreach $resources as $i => $resource}	
	{if $i > 0 && ($i % 3) == 0}
		</div>
		<div class="row ">
	{/if}
	<div class="col-lg-4 col-md-6 my-2">
		<div class="card h-100">
			<div class="card-body" id="{$i}">
				<input type="hidden" value="{$resource->id}" id="campusResourceId{$i}">
				<div class="media campus-resource">
					<img class="align-self-center mr-2 ml-0 img-thumbnail w-25" id="image{$i}" src="{$resource->imageSrc}" alt="{$resource->title}">
					<div class="media-body pl-1">
						<h5 class="card-title" id="title{$i}">{$resource->title}{if $resource->abbreviation} <small>({$resource->abbreviation})</small>{/if}</h5>
						<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
						<div class="card-text text-muted" id="text{$i}">
							{$resource->description}
						</div>
						<span id="url{$i}" class="hidden" hidden>{$resource->url}</span>
						<div class="">
						<button id="preview{$i}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#resourcePreviewModal">
							More Info
						</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
{/foreach}
	</div> <!-- END row -->

</div>

<!-- Preview Modal -->
<div class="modal fade" id="resourcePreviewModal" tabindex="-1" role="dialog" aria-labelledby="resourceTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-sm" role="document">
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
				<div class="col-6 text-center">
					<img class="img-thumbnail mh-50" id="resourceImage" src="" alt="">
				</div>
				<div class="col-6 d-block">
					<span id="resourceDescription"></span>
					<span class="d-block mt-2 align-bottom">
						<strong class="d-block">Resource Website: </strong><a target="_blank" href="" id="resourceUrl" style="letter-spacing:0.8px;"></a>
					</span>
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