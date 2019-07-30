{assign var=realSection value=$sectionVersion->resolveSection()}
{foreach $realSection->objectives as $objective}
{if $objective->title}<h3 class="real-section-title objectives-title">{$objective->title}</h3>{/if}
<div class="real-section-content objectives-description">
	{$objective->description}
</div>
{/foreach}