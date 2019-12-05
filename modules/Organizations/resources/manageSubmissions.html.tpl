<div class="container-fluid table-collapse-container mt-3">
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
		<form action="{$routeBase}submissions" method="post" class="ml-auto pt-2">
			{if $departmentEmail}
				<button name="command[sendreminder][{$campaign->id}]" type="submit" class="btn btn-dark">
					<i class="fas fa-paper-plane mr-2"></i> Send Reminder Email
				</button>
			{else}
				<a href="{$routeBase}settings?submissions=true" class="btn btn-dark">
					<i class="fas fa-paper-plane mr-2"></i> Send Reminder Email
				</a>
			{/if}
			<a href="{$routeBase}campaigns/{$campaign->id}" class="text-dark btn btn-info">
				<i class="fas fa-edit mr-2"></i> Edit This Campaign
			</a>
			{generate_form_post_key}
		</form>
	</div>

    <div id="collapse{$campaign@index}" class="collapse px-0 col-12 mt-0 {if $campaign->id == $activeCampaign->id}show{/if}" aria-labelledby="headingOne" data-parent="#termAccordion">

	<div class="col-xl-9 col-lg-10 col-md-12 ">
		<table class="table table-responsive text-center">
			<thead>
				<tr>
					<th class="text-left">Submission due date <span class="text-muted">{$campaign->dueDateInterval}</span></th>
					<th>Open</th>
					<th class="text-center">Pending (submitted)</th>
					<th>Approved</th>
					<th>Denied</th>
					<th>Disabled</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>{assign var=stats value=$campaign->statistics}
				<tr class="border-bottom align-top" style="line-height: 3em;">
					<td class="text-left">{$campaign->dueDate->format('F jS, Y - h:i a')}</td>
					<td>{$stats['open']}</td>
					<td class="text-center">{$stats['pending']}</td>
					<td>{$stats['approved']}</td>
					<td>{$stats['denied']}</td>
					<td>{$stats['disabled']}</td>
					<td>{$stats['total']}</td>
				</tr>
			</tbody>
		</table>
		<div class="col-12 mt-3">
			<strong>Campaign Description</strong>
			<div>{$campaign->description}</div>
		</div>
	</div>
	<div class="d-flex">		
		<a href="#" class="ml-auto mt-auto pb-4">
			<i class="mr-2 far fa-file-excel"></i>Download as CSV
		</a>
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
				{if $submission->status == 'open'}
			<td class="align-middle text-center font-w800"><small class="text-uppercase text-primary font-w800 text-center">Open</small></td>
				<td>
				<form action="{$routeBase}submissions/{$submission->id}" method="post" id="editSubmissionForm">
					<input name="command[disable]" type="submit" class="btn btn-primary btn-sm" value="Disable" id="disableButton">
					{generate_form_post_key}
				</form>
			</td>
				{elseif $submission->status == 'disabled'}
			<td class="align-middle text-center font-w800"><small class="font-w800 text-uppercase">Disabled</small></td>
				<td>
					<form action="{$routeBase}submissions/{$submission->id}" method="post" id="editSubmissionForm">
						<input name="command[enable]" type="submit" class="btn btn-secondary btn-sm" value="Re-Enable" id="enableButton">
						{generate_form_post_key}
					</form>
				</td>
				{elseif $submission->status == 'pending'}
				<td class="align-middle text-center"><small class="text-info font-w800 text-uppercase">Pending</small></td>
				<td>
					<button id="review{$i}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#reviewSubmissionModal" data-submission="{$submission->id}">
						Review
					</button>
				</td>
				{elseif $submission->status == 'approved'}
				<td class="align-middle text-center"><small class="text-success font-w800 text-uppercase">Approved</small></td>
					<td>
					<button id="review{$i}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#reviewSubmissionModal" data-submission="{$submission->id}">
						Review
					</button>
					</td>
				{elseif $submission->status == 'denied'}
				<td class="align-middle text-center">
					<small class="text-danger font-w800 text-uppercase">Revisions Requested</small>
				</td>
				<td>
					<button id="review{$i}" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reviewSubmissionModal" data-submission="{$submission->id}">
						Review
					</button>
				</td>
				{/if}

				<input type="hidden" class="courseSummary" value="{$submission->courseSection->getFullDisplayName()}">
				<input type="hidden" class="status" value="{$submission->status|ucfirst}">
				<input type="hidden" class="feedback" value="{$submission->feedback}">
				<input type="hidden" class="dueDate" value="{$campaign->dueDate->format('F jS, Y - h:i a')}">
				{if $submission->submittedDate}
				<input type="hidden" class="submittedDate" value="{$submission->submittedDate->format('F jS, Y - h:i a')}">
				{/if}
				{if $submission->approvedDate}
				<input type="hidden" class="approvedDate" value="{$submission->approvedDate->format('F jS, Y - h:i a')}">
				{/if}
				{if $submission->file->id}
					<input type="hidden" class="fileSrc" value="{$submission->fileSrc}">
					<input type="hidden" class="fileName" value="{$submission->file->remoteName}">
				{/if}
				{if $submission->syllabus->id}
					<input type="hidden" class="syllabusId" value="{$submission->syllabus->id}">
					<input type="hidden" class="syllabusTitle" value="{$submission->syllabus->title}">
				{/if}
			<!-- </td> -->
		</tr>
	{/foreach}
	</tbody>
	<tfoot>
		<tr class="table-warning">
			<td colspan="4">
				<form action="{$routeBase}submissions" method="post">
					<div class="text-center">
						<button name="command[resetall][{$submission->campaign->id}]" type="submit" class="btn btn-info mx-2" id="approveButton">Reset All to Open <i class="fas fa-arrow-up"></i></button>
						<button name="command[approveall][{$submission->campaign->id}]" type="submit" class="btn btn-success mx-2" id="approveButton">Approve All <i class="fas fa-arrow-up"></i></button>
					</div>
					{generate_form_post_key}
				</form>
			</td>
		</tr>
	</tfoot>
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
		<input name="command[deny][]" type="submit" class="btn btn-danger" value="Request Revisions" id="denyButton">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
	{generate_form_post_key}
	</form>
    </div>
  </div>
</div>


