{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Resources Section - View --> 
<div class="col">
{foreach $realSection->resources as $i => $resource}

	<div class="mb-3 pb-3 syllabus-resources {if ($i+1)%2 == 0}even{else}odd{/if} {if !$resource@last}border-bottom{/if}">
		<h4 class="">{$resource->title}
			{if $resource->abbreviation} 
				&mdash; <span class="text-muted">{$resource->abbreviation}</span>
			{/if}
		</h4>
		{if $resource->url}
			<p class="d-block"><span class="dont-break-out">{l text=$resource->url href=$resource->url}</span></p>
		{/if}
		
		<div class="media">
			{if !$resource->isCustom}
		    <image style="width:100px;" class="align-self-top mr-3" alt="{$resource->title}" src="{$resource->getImageSrc()}">
		    {/if}
		    <div class="media-body">
		        {$resource->description|allow_basic_html}
		    </div>
		</div>
	</div>

{/foreach}
</div>

{if $realSection->additionalInformation}
<div class="col">
    {$realSection->additionalInformation}
</div>
{/if}
<!-- End Resources Section - View -->