<div class="preview-container">
	<ul class="list-unstyled">
	{foreach $importable->section->latestVersion->resolveSection()->policies as $policy}
		<li>
			<strong>{$policy->title|truncate:50}</strong>
			{$policy->description|truncate:200}
		</li>
	{/foreach}
	</ul>
</div>