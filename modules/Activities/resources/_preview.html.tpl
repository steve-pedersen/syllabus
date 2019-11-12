<div class="preview-container">
	<table class="table table-sm table-striped table-responsive">
	<tbody>
	{foreach $importable->section->latestVersion->resolveSection()->activities as $i => $activity}
		{if $i < 2}
		<tr class="">
			<td>{$activity->name|truncate:30}</td>
			<td>{$activity->value|truncate:10}</td>
			<td>{$activity->description|truncate:100}</td>
		</tr>
		{/if}
	{/foreach}
	</tbody>
	</table>
</div>