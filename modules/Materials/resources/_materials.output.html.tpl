{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Materials Section - View --> 
<div class="real-section-content materials-content">
{foreach $realSection->materials as $i => $material}
	<dl class="row {if ($i+1)%2 == 0}even{else}odd{/if}">
		<dt class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
			<em>{$material->title}</em>
			{if $material->required} 
				<br><span class="text-danger">(Required)</span>
			{/if}
		</dt>
		<dd class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
			{if $material->authors}
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">Author(s)</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
					{$material->authors}
				</dd>
			</dl>
			{/if}
			{if $material->publisher}
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">Publisher</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
					{$material->publisher}
				</dd>
			</dl>
			{/if}
			{if $material->url}
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">URL</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
					<span class="dont-break-out">{l text=$material->url href=$material->url}</span>
				</dd>
			</dl>
			{/if}
			{if $material->isbn}
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">ISBN</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">{$material->isbn}</dd>
			</dl>
			{/if}
		</dd>
	</dl>
{/foreach}

	{if $realSection->additionalInformation}
	<div class="col">
	    {$realSection->additionalInformation}
	</div>
	{/if}
</div>
<!-- End Materials Section - View -->