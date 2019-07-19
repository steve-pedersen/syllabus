{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Instructors Section - View --> 
<div class="col">
{foreach $realSection->instructors as $instructor}
	<dl class="row">
		<dt class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
			<h3 class="real-section-title instructor-info-title">
			{$instructor->name}
			{if $instructor->title}
				<br><small class="text-muted">{$instructor->title}</small>
			{/if}
			</h3>
		</dt>
		<dd class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">Contact</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
					{if $instructor->email}{l href="mailto:{$instructor->email}" text=$instructor->email}<br>{/if}
					{if $instructor->phone}{$instructor->phone}<br>{/if}
					{if $instructor->website}<span class="dont-break-out">{l href=$instructor->website text=$instructor->website}</span><br>{/if}
				</dd>
			</dl>
			{if $instructor->office || $instructor->officeHours}
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">Office Information</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
					{if $instructor->office}<strong>Location:</strong> {$instructor->office}<br>{/if}
					{if $instructor->officeHours}{$instructor->officeHours}{/if}
				</dd>
			</dl>
			{/if}
			{if $instructor->about}
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">About</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
			    	{if $instructor->credentials}
			    		{$instructor->name} &mdash; <span class="text-muted">{$instructor->credentials}</span>
			    	{/if}
					{$instructor->about}
				</dd>
			</dl>
			{/if}
		</dd>
	</dl>
{/foreach}
</div>
<!-- End Instructors Section - View -->