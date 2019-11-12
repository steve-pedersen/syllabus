<div class="preview-container">
	<table class="table table-sm table-striped">
	<tbody>
	{foreach $importable->section->latestVersion->resolveSection()->schedules as $i => $schedule}
		{if $i < 3}
		<tr>
			<td>{$schedule->column1|truncate:50}</td>
			<td>{$schedule->column2|truncate:50}</td>
			<td>{$schedule->column3|truncate:50}</td>
			{if $schedule->column4}<td>{$schedule->column4|truncate:50}</td>{/if}
		</tr>
		{/if}
	{/foreach}
	</tbody>
	</table>
</div>