{assign var=realSection value=$sectionVersion->resolveSection()}
{foreach $realSection->policies as $policy}
<h3 class="real-section-title policies-title">{$policy->title}</h3>
<div class="real-section-content policies-description">
	{$policy->description}
</div>
{/foreach}