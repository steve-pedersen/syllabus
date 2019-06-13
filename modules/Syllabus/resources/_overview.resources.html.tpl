<div class="container-fluid campus-resources-overview mb-5">
<h2>Campus Resources</h2>
<p class="text-muted">Add links to various campus resources to your syllabi. Choose "Add to syllabus" to select which syllabi to import each resource into.</p>
	<div class="row mb-4">
	{assign var=hasSeeMore value=false}
{foreach $campusResources as $i => $campusResource}	
	{if $i > 0 && ($i % 3) == 0}
		</div>
		{if $i == 6}
			<div class="collapse" id="seeMoreResources">
			{assign var=hasSeeMore value=true}
		{/if}
		<div class="row mb-4">
	{/if}
	<div class="col-lg-4 col-md-6">
		<div class="card h-100">
			<div class="card-body" id="{$i}">
				<div class="media campus-resource">
					<img class="align-self-center mr-2 ml-0 img-thumbnail w-25" id="image{$i}" src="{$campusResource->imageSrc}" alt="{$campusResource->title}">
					<div class="media-body pl-1">
						<h5 class="card-title" id="title{$i}">{$campusResource->title}{if $campusResource->abbreviation} <small>({$campusResource->abbreviation})</small>{/if}</h5>
						<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>
						<div class="card-text text-muted" id="text{$i}">
							{$campusResource->description}
						</div>
						<span id="url{$i}" class="hidden" hidden>{$campusResource->url}</span>
						<div class="">
						<button id="preview{$i}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#resourcePreviewModal">
							More Info
						</button>
						{if $syllabi}
						<button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#resourceAddModal">
							Add to Syllabus
						</button>
						{/if}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
{/foreach}
	</div> <!-- END row -->
{if $hasSeeMore}
</div> <!-- END collapse -->
<div class="float-right">
	<a class="collapsed ml-auto see-more-toggle" id="seeMoreToggle1" data-toggle="collapse" href="#seeMoreResources" aria-expanded="false" aria-controls="seeMoreResources"></a>
</div>
{/if}
</div>

<!-- Add to Syllabus Modal -->
<div class="modal fade" id="resourceAddModal" tabindex="-1" role="dialog" aria-labelledby="addTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-sm" role="document">
    <div class="modal-content">
	<div class="modal-header">
		<div class="modal-title d-block-inline">
			<h5>
			<img class="mw-10 mr-3" id="addImage" src="" alt="">
			<span id="addTitle"></span>
			</h5>
		</div>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<span id="addText"></span>
	</div>
	<div class="modal-body">
		<div class="container-fluid">
			<p class="text-muted">Check each syllabus that you want to import this resource into.</p>
			<h6 class="">Most recent syllabi:</h6>
			<div class="row mb-3">	
			{foreach $syllabi as $i => $syllabus}
			{if $i == 6}{break}{/if}
			{if $i > 0 && ($i % 3) == 0}</div><div class="row mb-3">{/if}
			<div class="col-lg-4 col-md-6 px-2">
				<div class="card">
					<label class="form-check-label" for="overlayCheck{$i}">
					<div class="card-body h-100">
						<div class="ml-auto text-right">
							<div class="form-check">
								<input type="checkbox" class="form-check-input overlay-checkbox" id="overlayCheck{$i}">
							</div>
						</div>
						<div class="card-img-top-overlay p-0">
							<img src="assets/images/testing0{$i}.jpg" class="card-img-top crop-top crop-top-13" alt="{$syllabus->title}" />
						</div>
						<h6 class="mt-3 text-dark">{$syllabus->title}</h6>
						<small class="d-block">
							<p class="card-text">{$syllabus->description}
								<strong class="d-block">Last Modified:</strong> 
								{$syllabus->modifiedDate->format('F jS, Y - h:i a')}
							</p>
						</small>
					</div>
					</label>
				</div>
			</div>
			{foreachelse}
				<p>You have no syllabi yet!</p>
			{/foreach}
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-success">Add Resource to Selected Syllabi</button>
	</div>
    </div>
  </div>
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
						<strong class="d-block">Resource Website: </strong><a target="_blank" href="" id="resourceUrl"></a>
					</span>
				</div>
			</div>
			<div class="row bg-primary rounded">
				<h6 class="pt-2 pl-2 text-white">Example syllabus with a new resource added:</h6>
				<img src="assets/images/resource-preview.png" alt="Example of importing a resource into a syllabus" class="img-fluid border border-primary rounded">
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
    </div>
  </div>
</div>
