{assign var=realSection value=$sectionVersion->resolveSection()}
<div class="real-section-content grades">

	<div class="col">
	<table class="table table-responsive table-sm">
		<thead>
			<tr>
				<th scope="col">{$realSection->header1}</th>
				<th scope="col">{$realSection->header2}</th>
				{if $realSection->columns == 3}<th scope="col">{$realSection->header3}</th>{/if}
			</tr>
		</thead>
		<tbody>
		{foreach $realSection->grades as $i => $grade}
			<tr>
				<td>{$grade->column1}</td>
				<td>{$grade->column2}</td>
				{if $realSection->columns == 3}<td>{$grade->column3}</td>{/if}
			</tr>
		{/foreach}
		</tbody>
	</table>

	{if $realSection->additionalInformation}
		{$realSection->additionalInformation}
	{/if}
	</div>

</div>
