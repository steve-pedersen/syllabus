{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Objectives Section - View --> 
{foreach $realSection->objectives as $objective}
<div class="col">
    <h4>{$objective->title}</h4>
</div>
<div class="col">
    {$objective->description}
</div>
	
{/foreach}
<!-- End Objectives Section - View -->