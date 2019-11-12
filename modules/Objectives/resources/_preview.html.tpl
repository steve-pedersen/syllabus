<div class="preview-container">
	<ul class="list-unstyled">
	{foreach $importable->section->latestVersion->resolveSection()->objectives as $objective}
		<li>
			<strong>{$objective->title|truncate:50}</strong>
			{$objective->description|truncate:200}
		</li>
	{/foreach}
	</ul>
</div>