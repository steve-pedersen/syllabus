{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Instructors Section - View --> 
<div class="col">
{foreach $realSection->instructors as $instructor}
<div class="row border-bottom mb-3">
	<div class="col-4">
	    <h5>
	    	{$instructor->name}
	    	{if $instructor->credentials}
	    		&mdash; <small>{$instructor->credentials}</small>
	    	{/if}
			{if $instructor->title}
				<br><small>{$instructor->title}</small>
			{/if}
	    </h5>
	    {if $instructor->email}{l href="mailto:{$instructor->email}" text=$instructor->email}<br>{/if}
	    {if $instructor->phone}{$instructor->phone}<br>{/if}
	    {if $instructor->website}<span class="dont-break-out">{l href=$instructor->website text=$instructor->website}</span><br>{/if}
	</div>
	<div class="col-4">
		<h6 class="">Office Information</h6>
		{if $instructor->office}<strong>Location:</strong> {$instructor->office}<br>{/if}
		{if $instructor->officeHours}{$instructor->officeHours}{/if}
	</div>
	<div class="col-4">
		<h6 class="">About</h6>
		{if $instructor->about}{$instructor->about}{/if}
	</div>
</div>
{/foreach}
</div>
<!-- End Instructors Section - View -->