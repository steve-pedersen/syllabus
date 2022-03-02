{assign var=realSection value=$sectionVersion->resolveSection()}
<div class="real-section-content schedules">
	{if $realSection->schedules}
	<table class="table table-sm table-striped">
		<thead class="thead-dark">
			<tr>
				<th scope="col" style="width:13%">{$realSection->header1}</th>
				<th scope="col">{$realSection->header2}</th>
				<th scope="col">{$realSection->header3}</th>
				{if $realSection->columns == 4}<th scope="col" style="min-width:13%">{$realSection->header4}</th>{/if}
			</tr>
		</thead>
		<tbody>
		{foreach $realSection->schedules as $i => $schedule}
			<tr>
				<td>{$schedule->column1}</td>
				<td>{$schedule->column2}</td>
				<td>{$schedule->column3}</td>
				{if $realSection->columns == 4}<td>{$schedule->column4}</td>{/if}
			</tr>
		{/foreach}
		</tbody>
	</table>
	{/if}

	{if $realSection->additionalInformation}
	<div class="col">
		{$realSection->additionalInformation}
	</div>
	{/if}
</div>
