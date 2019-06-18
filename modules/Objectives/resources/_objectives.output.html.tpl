{assign var=realSection value=$sectionVersion->resolveSection()}

<h2 class="real-section-title objectives-title">{$realSection->title}</h2>
<div class="real-section-content objectives-description">
	{$realSection->description}
</div>
