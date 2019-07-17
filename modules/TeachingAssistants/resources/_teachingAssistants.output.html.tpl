{assign var=realSection value=$sectionVersion->resolveSection()}
{foreach $realSection->teachingAssistants as $ta}
<div class="real-section-content teaching-assistants-description">
<div class="col">
    <strong>{$ta->name}{if $ta->email} - {/if}</strong>
   {if $ta->email}{l href="mailto:{$ta->email}"" text=$ta->email}{/if}
</div>
<div class="col">
    {$ta->additionalInformation}
</div>
</div>
{/foreach}