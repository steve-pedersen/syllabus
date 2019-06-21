{assign var=realSection value=$sectionVersion->resolveSection()}
{foreach $realSection->objectives as $objective}
<h2 class="real-section-title objectives-title">{$objective->title}</h2>
<div class="real-section-content objectives-description">
	{$objective->description}
</div>
{/foreach}