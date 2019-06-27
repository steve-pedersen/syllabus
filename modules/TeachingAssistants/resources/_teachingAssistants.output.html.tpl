{assign var=realSection value=$sectionVersion->resolveSection()}
{foreach $realSection->teachingAssistants as $ta}
<div class="real-section-content teaching-assistants-description">
<div class="col">
    <strong>{$ta->name}{if $ta->email} - {/if}</strong>
    {l href=$ta->email text=$ta->email}
</div>
<div class="col">
    {$ta->additionalInformation}
</div>
</div>
{/foreach}