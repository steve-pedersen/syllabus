<div class="container-fluid table-collapse-container">
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
			{if $submission->status == 'pending'}table-info{/if}
			{if $submission->status == 'approved'}table-success{/if}
			{if $submission->status == 'denied'}table-danger{/if}
			{if $submission->status == 'disabled'}table-light {/if}
		">
			<td class="align-middle">{$submission->courseSection->id}</td>
			<td class="align-middle">{$submission->courseSection->getFullDisplayName()}</td>
			<td class="align-middle">{$submission->status|ucfirst}</td>
			<td class="align-middle">
				{if $submission->status == 'open'}
				<form action="{$routeBase}submissions/{$submission->id}" method="post" id="editSubmissionForm">
					<input name="command[disable]" type="submit" class="btn btn-primary btn-sm" value="Disable" id="disableButton">
					{generate_form_post_key}
				</form>
				{elseif $submission->status == 'disabled'}
				<form action="{$routeBase}submissions/{$submission->id}" method="post" id="editSubmissionForm">
					<input name="command[enable]" type="submit" class="btn btn-secondary btn-sm" value="Re-Enable" id="enableButton">
					{generate_form_post_key}
				</form>
				{elseif $submission->status == 'pending'}
					<button id="review{$i}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#reviewSubmissionModal" data-submission="{$submission->id}">
						Review
					</button>
				{elseif $submission->status == 'approved'}
					<button id="review{$i}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#reviewSubmissionModal" data-submission="{$submission->id}">
						Approved
					</button>
				{elseif $submission->status == 'denied'}
					<button class="btn btn-warning btn-sm">Disable</button>
				{/if}

				<input type="hidden" id="courseSummary" value="{$submission->courseSection->getFullDisplayName()}">
				<input type="hidden" id="status" value="{$submission->status|ucfirst}">
				<input type="hidden" id="feedback" value="{$submission->feedback}">
				<input type="hidden" id="dueDate" value="{$campaign->dueDate->format('F jS, Y - h:i a')}">
				{if $submission->submittedDate}
				<input type="hidden" id="submittedDate" value="{$submission->submittedDate->format('F jS, Y - h:i a')}">
				{/if}
				{if $submission->approvedDate}
				<input type="hidden" id="approvedDate" value="{$submission->approvedDate->format('F jS, Y - h:i a')}">
				{/if}
				{if $submission->file->id}
					<input type="hidden" id="fileSrc" value="{$submission->fileSrc}">
					<input type="hidden" id="fileName" value="{$submission->file->remoteName}">
				{/if}
				{if $submission->syllabus->id}
					<input type="hidden" id="syllabusId" value="{$submission->syllabus->id}">
					<input type="hidden" id="syllabusTitle" value="{$submission->syllabus->title}">
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

<!-- Review Submission Modal -->
<div class="modal fade" id="reviewSubmissionModal" tabindex="-1" role="dialog" aria-labelledby="submissionTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-sm" role="document">
    <div class="modal-content">
	<form action="{$routeBase}submissions/" method="post" id="editSubmissionForm">
	<div class="modal-header text-left">
		<h3 id="submissionTitle"></h3>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<div class="modal-body">
		<div class="jumbotrons">
			<dl class="mb-0 p-3">
				<dt>Course Section</dt>
				<dd id="subCourseSection"></dd>
				<dt>Submission Due Date</dt>
				<dd id="subDueDate"></dd>
				<dt>Current Status</dt>
				<dd id="subStatus"></dd>
				<dt>Date Submitted</dt>
				<dd id="subSubmittedDate">N/A</dd>
				<dt>Date Approved</dt>
				<dd id="subApprovedDate">N/A</dd>
				<dt>Submitted Online Syllabus</dt>
				<dd id="subSyllabusView">Syllabus View URL (opens in new tab): <a href="" id="syllabusViewLink" target="_blank"></a></dd>
				<dd id="subSyllabusWord"><a href="" id="syllabusWordLink"></a></dd>
				<dt>Submitted File Syllabus</dt>
				<dd id="subFileDownload">Syllabus file (click to download): <a href="" id="fileDownloadLink"></a></dd>
			</dl>
		</div>
		<div class="form-group my-3 px-3">
			<label for="feedback">Feedback for instructor about this submission:</label>
			<textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="feedback" rows="5" id="subFeedback"></textarea>
		</div>
	</div>
	<div class="modal-footer">
		<input name="command[approve][]" type="submit" class="btn btn-success" value="Approve" id="approveButton">
		<input name="command[deny][]" type="submit" class="btn btn-danger" value="Deny" id="denyButton">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
	{generate_form_post_key}
	</form>
    </div>
  </div>
</div>


