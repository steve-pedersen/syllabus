{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Policies Section - View --> 
{foreach $realSection->policies as $policy}
<div class="col">
    <h4>{$policy->title}</h4>
</div>
<div class="col">
    {$policy->description}
</div>
	
{/foreach}
<!-- End Policies Section - View -->