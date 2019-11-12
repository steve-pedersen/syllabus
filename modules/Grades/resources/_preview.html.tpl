{assign var=grades value=$importable->section->latestVersion->resolveSection()}

<div class="preview-container table-responsive">
	<table class="table table-sm table-striped">
		<thead class="bg-primary text-white">
			<tr>
				<th scope="col">{$grades->header1}</th>
				<th scope="col">{$grades->header2}</th>
				<th scope="col">{$grades->header3}</th>
			</tr>			
		</thead>
	<tbody>
	{foreach $grades as $i => $grade}
		{if $i < 5}
		<tr>
			<td class="font-w700">{$grade->column1|truncate:50}</td>
			<td>{$grade->column2|truncate:50}</td>
			{if $grade->column3}<td>{$grade->column3|truncate:50}</td>{/if}
		</tr>
		{/if}
	{/foreach}
	</tbody>
	</table>
</div>