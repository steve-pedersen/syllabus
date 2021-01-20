{assign var=realSection value=$sectionVersion->resolveSection()}
<div class="real-section-content activities">
	{if $realSection->activities}
	<table class="table table-sm table-striped">
		<thead class="thead-dark">
			<tr>
				<!-- <th scope="col">#</th> -->
				<th scope="col" style="width:17%;">Name</th>
				<th scope="col" style="width:17%;">Contribution to Grade</th>
				<th scope="col" style="min-width:40%;">Description</th>
			</tr>
		</thead>
		<tbody>
		{foreach $realSection->activities as $i => $activity}
			<tr>
				<!-- <th scope="row">{$i+1}</th> -->
				<td>{$activity->name}</td>
				<td>{$activity->value}</td>
				<td>{$activity->description}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	{/if}
</div>