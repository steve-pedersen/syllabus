{assign var=realSection value=$sectionVersion->resolveSection()}
{foreach $realSection->policies as $policy}
<h2 class="real-section-title policies-title">{$policy->title}</h2>
<div class="real-section-content policies-description">
	{$policy->description}
</div>
{/foreach}