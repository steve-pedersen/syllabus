{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Materials Section - View --> 
<div class="col">
<ul class="">
{foreach $realSection->materials as $material}
<li>{$material->title} - {l text=$material->url href=$material->url} {if $material->required} <span class="text-danger">*Required</span>{/if}</li>
{/foreach}
</ul>
</div>

{if $realSection->additionalInformation}
<div class="col">
    {$realSection->additionalInformation}
</div>
{/if}
<!-- End Materials Section - View -->