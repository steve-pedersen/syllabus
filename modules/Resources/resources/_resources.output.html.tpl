{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Resources Section - View --> 
<div class="col">
<ul class="">
{foreach $realSection->resources as $resource}
<li class="text-break">{$resource->title} - <span class="dont-break-out"><a href="{$resource->url}">{$resource->url}</a></span></li>
{/foreach}
</ul>
</div>

{if $realSection->additionalInformation}
<div class="col">
    {$realSection->additionalInformation}
</div>
{/if}
<!-- End Resources Section - View -->