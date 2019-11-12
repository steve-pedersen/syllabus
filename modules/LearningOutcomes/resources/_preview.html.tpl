<div class="preview-container">
	<table class="table table-sm table-striped">
	<tbody>
	{foreach $importable->section->latestVersion->resolveSection()->learningOutcomes as $i => $learningOutcome}
		{if $i < 2}
		<tr>
			<td>{$learningOutcome->column1|truncate:50}</td>
			<td>{$learningOutcome->column2|truncate:50}</td>
			{if $learningOutcome->column3}<td>{$learningOutcome->column3|truncate:50}</td>{/if}
		</tr>
		{/if}
	{/foreach}
	</tbody>
	</table>
</div>