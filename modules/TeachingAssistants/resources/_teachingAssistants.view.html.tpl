{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- TeachingAssistants Section - View --> 
{foreach $realSection->teachingAssistants as $ta}
<div class="col">
    <strong>{$ta->name}{if $ta->email} - {/if}</strong>
   {if $ta->email}{l href="mailto:{$ta->email}"" text=$ta->email}{/if}
</div>
<div class="col">
    {$ta->additionalInformation}
</div>
	
{/foreach}
<!-- End TeachingAssistants Section - View -->