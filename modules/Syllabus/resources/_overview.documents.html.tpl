<div class="container-fluid documents-overview mb-5">
<h2>Guidelines <span class="fancy-amp">&amp;</span> Documents</h2>
<p class="text-muted">Learn about the many ways you can improve your syllabus.</p>

<div class="row">
	{foreach $guideDocs as $guideDoc}
	<div class="col-lg-4 col-md-6 mb-3">
		<div class="card card-bordered card-bordered-left card-bordered-secondary h-100">
			<div class="card-body">
				<h5 class="card-title"><i class="{if $guideDoc->iconClass}{$guideDoc->iconClass}{else}far fa-file-alt{/if} mr-1"></i> {$guideDoc->title}</h5>
				<p class="card-text text-muted align-middle">{$guideDoc->description}</p>
				
			</div>
			<div class="card-footer bg-white border-0">
				{if $guideDoc->file && !$guideDoc->url}
					<a href="{$guideDoc->fileSrc}" target="_blank" class="btn btn-link text-dark font-weight-bold"><span class=""><i class="fas fa-download"></i></span> Download</a>
				{elseif $guideDoc->url}
					<a href="{$guideDoc->url}" target="_blank" class="btn btn-link text-info font-weight-bold"><span class=""><i class="fas fa-external-link-alt"></i></span> View</a>
				{/if}
			</div>
		</div>
	</div>
	{/foreach}
</div>
</div>