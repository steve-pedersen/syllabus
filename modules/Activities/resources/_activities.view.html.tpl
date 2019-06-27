{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Activities Section - View -->
<div class="col">
<table class="table table-sm">
	<thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col">Name</th>
			<th scope="col">Value</th>
			<th scope="col">Description</th>
		</tr>
	</thead>
	<tbody>
	{foreach $realSection->activities as $i => $activity}
		<tr>
			<th scope="row">{$i+1}</th>
			<td>{$activity->name}</td>
			<td>{$activity->value}</td>
			<td>{$activity->description}</td>
		</tr>
	{foreachelse}
		<tr colspan=4>There aren't any activities in this section.</tr>
	{/foreach}
	</tbody>
</table>
</div>
<!-- End Activities Section - View -->