{assign var=realSection value=$sectionVersion->resolveSection()}
<div class="real-section-content learning-outcomes">
	<table class="table table-sm table-striped">
		<thead class="thead-dark">
			<tr>
				<th scope="col">{$realSection->header1}</th>
				<th scope="col">{$realSection->header2}</th>
				{if $realSection->columns == 3}<th scope="col">{$realSection->header3}</th>{/if}
			</tr>
		</thead>
		<tbody>
		{foreach $realSection->learningOutcomes as $i => $learningOutcome}
			<tr>
				<td>{$learningOutcome->column1}</td>
				<td>{$learningOutcome->column2}</td>
				{if $realSection->columns == 3}<td>{$learningOutcome->column3}</td>{/if}
			</tr>
		{/foreach}
		</tbody>
	</table>
	{if $realSection->additionalInformation}
	<div class="col">
		{$realSection->additionalInformation}
	</div>
	{/if}
</div>