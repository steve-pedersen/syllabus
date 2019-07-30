{assign var=realSection value=$sectionVersion->resolveSection()}
<div class="real-section-content instructors-description">
{foreach $realSection->instructors as $instructor}
	<div class="row">
		{if $instructor->name}
		<h3 class="real-section-title instructor-info-title">
		{$instructor->name}
		{if $instructor->title}
			<br><small class="text-muted">{$instructor->title}</small>
		{/if}
		</h3>
		{/if}
		<dl>
			<dt><h4>Contact</h4></dt>
			<dd>
				{if $instructor->email}{l href="mailto:{$instructor->email}" text=$instructor->email}<br>{/if}
				{if $instructor->phone}{$instructor->phone}<br>{/if}
				{if $instructor->website}<span class="dont-break-out">{l href=$instructor->website text=$instructor->website}</span><br>{/if}
			</dd>
		{if $instructor->office}
			<dt><h4>Office Location</h4></dt>
			<dd>
				{$instructor->office}
			</dd>
		{/if}
		{if $instructor->officeHours}
			<dt><h4>Office Hours</h4></dt>
			<dd>
				{$instructor->officeHours}
			</dd>
		{/if}
		{if $instructor->about}
			<dt><h4>About</h4></dt>
			<dd>
		    	{if $instructor->credentials}
		    		{$instructor->name} &mdash; <span class="text-muted">{$instructor->credentials}</span>
		    	{/if}
				{$instructor->about}
			</dd>
		{/if}
		</dl>
	</div>
{/foreach}
</div>