{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Materials Section - View --> 
<div class="real-section-content materials-content">
{foreach $realSection->materials as $i => $material}
	<div class="row {if ($i+1)%2 == 0}even{else}odd{/if}">
		<h3><em>{$material->title}</em>
		{if $material->required} 
			<span class="pl-2 text-danger">(Required)</span>
		{/if}	
		</h3>
		<dl class="row mb-0">
		{if $material->authors}
			<dt>Author(s)</dt>
			<dd>
				{$material->authors}
			</dd>
		{/if}
		{if $material->publisher}
			<dt>Publisher</dt>
			<dd>
				{$material->publisher}
			</dd>
		{/if}
		{if $material->url}
			<dt>URL</dt>
			<dd>
				<span class="dont-break-out">{l text=$material->url href=$material->url}</span>
			</dd>
		{/if}
		{if $material->isbn}
			<dt>ISBN</dt>
			<dd>{$material->isbn}</dd>
		{/if}
		</dl>
	</div>
{/foreach}

	{if $realSection->additionalInformation}
	<div class="col">
	    {$realSection->additionalInformation}
	</div>
	{/if}
</div>
<!-- End Materials Section - View -->