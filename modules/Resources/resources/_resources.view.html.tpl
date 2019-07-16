{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Resources Section - View --> 
<div class="col">
<ul class="">
{foreach $realSection->resources as $resource}
<li>{$resource->title} - <span class="dont-break-out">{l text=$resource->url href=$resource->url}</span></li>
{/foreach}
</ul>
</div>

{if $realSection->additionalInformation}
<div class="col">
    {$realSection->additionalInformation}
</div>
{/if}
<!-- End Resources Section - View -->