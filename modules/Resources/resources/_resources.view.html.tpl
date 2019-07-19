{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Resources Section - View --> 
<div class="col">
{foreach $realSection->resources as $i => $resource}

	<dl class="row {if ($i+1)%2 == 0}even{else}odd{/if}">
		<dt class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
			{$resource->title}
			{if $resource->abbreviation} 
				&mdash; <span class="text-muted">{$resource->abbreviation}</span>
			{/if}
			{if !$resource->isCustom}	
				<br><image class="img-thumbnail w-25" alt="{$resource->title}" src="{$resource->getImageSrc()}">
			{/if}
		</dt>
		<dd class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
			{if $resource->url}
			<dl class="row mb-0">
				<!-- <dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">Website</dt> -->
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
					<span class="dont-break-out">{l text=$resource->url href=$resource->url}</span>
				</dd>
			</dl>
			{/if}
			{if $resource->description}
			<dl class="row mb-0">
				<!-- <dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">Publisher</dt> -->
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
					{$resource->description}
				</dd>
			</dl>
			{/if}
		</dd>
	</dl>

{/foreach}
</div>

{if $realSection->additionalInformation}
<div class="col">
    {$realSection->additionalInformation}
</div>
{/if}
<!-- End Resources Section - View -->