<div class="container-fluid student-container">
<h1 class="">Manage Submissions</h1>
<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>

{if $allCampaigns}

<div class="d-block my-3">
	<a href="{$routeBase}campaigns/new" class="ml-auto btn btn-success btn-lgs">+ Create New Submission Campaign</a>
</div>

<div class="accordion mt-4" id="termAccordion">
{foreach $allCampaigns as $campaign}
	
	<div class="d-flex w-100 p-3 mt-3 bg-secondary rounded-top">
		<a href="#" class="{if $campaign->id != $activeCampaign->id}collapsed{/if} mt-1 mr-auto d-block collapse-term" data-toggle="collapse" data-target="#collapse{$campaign@index}" aria-expanded="true" aria-controls="collapse{$campaign@index}">
			<h3 class="text-white">
				<i class="fas fa-plus mx-2 minimized text-dark"></i>
				<i class="fas fa-minus mx-2 expanded text-dark"></i>
				{$campaign->semester->display}
			</h3>
		</a>
		<a href="{$routeBase}campaigns/{$campaign->id}" class="ml-auto text-dark btn btn-info pt-2">
			Edit this campaign
		</a>
	</div>

    <div id="collapse{$campaign@index}" class="collapse px-0 col-12 mt-0 {if $campaign->id == $activeCampaign->id}show{/if}" aria-labelledby="headingOne" data-parent="#termAccordion">

	<div class="px-3 pt-3 bg-light">
		<div class="d-flex">
			<dl class="mb-0 p-3">
				<dt>Submission due date</dt>
				<dd>{$campaign->dueDate->format('F jS, Y - h:i a')}</dd>
				<dd>{$campaign->dueDateInterval}</dd>
				<dt>Submission statuses for this campaign</dt>
					{assign var=stats value=$campaign->statistics}
				<dd>
					{$stats['open']} open, {$stats['pending']} pending (submitted), 
					{$stats['approved']} approved, {$stats['denied']} denied
				</dd>
				<dd>
					{$stats['total']} total
				</dd>
				<dt>Campaign description</dt>
				<dd>{$campaign->description}</dd>
			</dl>			
			<a href="#" class="ml-auto mt-auto pb-4">
				<i class="mr-2 far fa-file-excel"></i>Download as CSV
			</a>
		</div>

	</div>

	<table class="table table-sm table-bordered">
	<thead class="thead-dark">
		<tr class="">
			<th scope="col" class="" style="">ID</th>
			<th scope="col" class="" style="">Course Info</th>
			<th scope="col" class="">Submission Status</th>
			<th scope="col" class="">Options</th>
		</tr>
	</thead>
	<tbody>
	{foreach $campaign->submissions as $i => $submission}
		<tr class="
			{if $submission->status == 'pending'}submission-required info{/if}
			{if $submission->status == 'approved'}submission-required success{/if}
			{if $submission->status == 'denied'}submission-required danger{/if}
		">
			<td class="align-middle">[{$submission->courseSection->id}]</td>
			<td class="align-middle">{$submission->courseSection->getFullDisplayName()}</td>
			<td class="align-middle">{$submission->status|ucfirst}</td>
			<td class="align-middle">
				{if $submission->status == 'open'}
					<button class="btn btn-primary btn-sm">Disable</button>
				{elseif $submission->status == 'pending'}
					<button class="btn btn-info btn-sm">Review</button>
				{elseif $submission->status == 'approved'}
					Approved
				{elseif $submission->status == 'denied'}
					<button class="btn btn-warning btn-sm">Disable</button>
				{/if}
			</td>
		</tr>
	{/foreach}
	</tbody>
	</table>
	</div>
	

{/foreach}
</div>


{else}

<p class="mb-3">You have no submissions at this time.</p>
<div class="d-block my-3">
	<a href="{$routeBase}campaigns/new" class="btn btn-success btn-lgs">+ Create New Submission Campaign</a>
</div>
<div class="d-block">
	<a href="" class="text-sm">What's this?</a>
</div>

{/if}
</div>