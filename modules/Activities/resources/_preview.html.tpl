<div class="preview-container table-responsive">
	<table class="table table-sm table-striped ">
		<thead class="bg-primary text-white">
			<tr>
				<th scope="col">Name</th>
				<th scope="col">Value</th>
				<th scope="col">Description</th>
			</tr>			
		</thead>
	<tbody>
	{foreach $importable->section->latestVersion->resolveSection()->activities as $i => $activity}
		{if $i < 3}
		<tr class="">
			<td class="font-w700">{$activity->name|truncate:30}</td>
			<td>{$activity->value|truncate:10}</td>
			<td>{$activity->description|truncate:100}</td>
		</tr>
		{/if}
	{/foreach}
	</tbody>
	</table>
</div>