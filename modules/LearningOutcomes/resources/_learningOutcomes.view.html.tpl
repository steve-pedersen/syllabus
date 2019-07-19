{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- LearningOutcomes Section - View -->
<div class="col">
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
	<h5>Additional Information</h5>
	{$realSection->additionalInformation}
{/if}

</div>
<!-- End LearningOutcomes Section - View -->