{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Materials Section - View --> 
<div class="col">
<ul class="">
{foreach $realSection->materials as $material}
	<li>
		{$material->title} - <span class="dont-break-out">{l text=$material->url href=$material->url}</span> {if $material->required} <span class="text-danger">*Required</span>{/if}
		{if $material->publisher}<strong>Publisher: </strong>{$material->publisher}{/if}
		{if $material->isbn}<strong>ISBN: </strong>{$material->isbn}{/if}
	</li>
{/foreach}
</ul>
</div>

{if $realSection->additionalInformation}
<div class="col">
    {$realSection->additionalInformation}
</div>
{/if}
<!-- End Materials Section - View -->