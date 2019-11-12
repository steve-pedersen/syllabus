<div class="preview-container">
	<table class="table table-sm table-striped">
	<tbody>
	{foreach $importable->section->latestVersion->resolveSection()->grades as $i => $grade}
		{if $i < 2}
		<tr>
			<td>{$grade->column1|truncate:50}</td>
			<td>{$grade->column2|truncate:50}</td>
			{if $grade->column3}<td>{$grade->column3|truncate:50}</td>{/if}
		</tr>
		{/if}
	{/foreach}
	</tbody>
	</table>
</div>