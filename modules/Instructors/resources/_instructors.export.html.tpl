{assign var=realSection value=$sectionVersion->resolveSection()}
<div class="real-section-content instructors-description">
{foreach $realSection->instructors as $instructor}
	<div class="row">
		{if $instructor->name}
		<h3 class="real-section-title instructor-info-title">
		{$instructor->name}
		{if $instructor->title}
			<p class="text-muted">{$instructor->title}</p>
		{/if}
		</h3>
		{/if}
		<dl>
			<dt><h4>Contact</h4></dt>
			<dd>
				{if $instructor->email}
					<strong>Email: </strong>{l href="mailto:{$instructor->email}" text=$instructor->email}
				{/if}
				{if $instructor->phone}
					<br>
					<strong>Phone: </strong>{$instructor->phone}
				{/if}
				{if $instructor->website}
					<br>
					<span class="dont-break-out">
						<strong>Website: </strong>{l href=$instructor->website text=$instructor->website}
					</span>
				{/if}
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