{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Materials Section - View --> 
<div class="col">
<ul class="">
{foreach $realSection->materials as $material}
	<li>
		<em>{$material->title}</em>  {if $material->required} <span class="ml-3 text-danger">*Required</span>{/if}
		{if $material->url}<span class="ml-3"></span><span class="dont-break-out">{l text=$material->url href=$material->url}</span>{/if}
		{if $material->publisher}<span class="ml-3"></span><strong>Publisher: </strong>{$material->publisher}{/if}
		{if $material->isbn}<span class="ml-3"></span><strong>ISBN: </strong>{$material->isbn}{/if}
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