<div class="container-fluid">
	<h1>Department Settings</h1>
	<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>

	<div class="mt-3 p-3 rounded border">
		<h2 class="">Email</h2>
		{if $sendSuccess}
		<div class="alert alert-info">
			<p>{$sendSuccess}</p>
			<p><strong>If you have made changes to the templates please make sure to save the changes below.</strong></p>
		</div>
		{/if}

		<form action="{$smarty.server.REQUEST_URI}" method="post">
			{generate_form_post_key}

			<div class="row">
				<div class="col-8">
					<div class="form-group">
						<label for="defaultAddress">Sending Email Address</label>
						<input type="email" class="form-control" name="defaultAddress" id="defaultAddress" value="{$emailSettings->contactEmail}" placeholder="cob@sfsu.edu" required />				
					</div>
				</div>
			</div>

			<div class="row email-row border-top pt-4 my-5">
				<div class="col-xl-8 col-lg-12 col-md-11 col-sm-12">
					<div class="form-group">
						<h3 id="dueDateReminderTime" class="toc-header" aria-hidden></h3>
						<label class="lead font-w700" for="dueDateReminderTime">Reservation Reminder Time: <span class="email-type-description font-w400">choose an amount of time prior to the submission deadline to send a reminder email to faculty</span></label>
						<select class="form-control" name="dueDateReminderTime" id="dueDateReminderTime">
						{foreach from=$reminderOptions item=opt}
							<option value="{$opt}" {if $opt == $emailSettings->reminderTime}selected{/if}>{$opt}</option>
						{/foreach}
						</select>
					</div>
					<div class="form-group">
						<label class="lead font-w700" for="dueDateReminderEmail">Reservation Reminder Email Content: <span class="email-type-description font-w400">the email body</span></label>
						<textarea name="dueDateReminderEmail" id="dueDateReminderEmail" class="form-control wysiwyg wysiwyg-syllabus-full" rows="5}">{$emailSettings->body}</textarea>
						<span class="help-block">
							You can use the following tokens for context replacements to fill out the template: 
							<code>|%FIRST_NAME%|</code>, <code>|%LAST_NAME%|</code>, <code>|%DUE_DATE%|</code>, <code>|%DEPARTMENT_NAME%|</code>, <code>|%SEMESTER%|</code>, <code>|%SUBMISSION_DESCRIPTION%|</code>
						</span>
						<span class="help-block d-block pt-2">
							For example, an email to Jane Doe will automaticlaly change "<code>|%FIRST_NAME%| |%LAST_NAME%|</code>" to "Jane Doe."
						</span>
					</div>
				</div>

				<div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 bg-light rounded p-5 h-75">
					<label id="testreservationreminder" class="lead font-w700">Test Reservation-Reminder Template</label>
					<p class="lead">This will send an email to your own account showing how the email will look to you.</p>
					<button type="submit" name="command[sendtest][dueDateReminder]" aria-describedby="testreservationreminder" class="btn btn-info">Send Test</button>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					<div class="form-group">
						<label class="lead font-w700" for="signature">Email Signature: <span class="email-type-description font-w400">This signature will automatically be appended to your reminder emails.</span></label>
						<textarea name="signature" id="signature" class="form-control wysiwyg wysiwyg-syllabus-full" rows="5" >{$emailSettings->signature}</textarea>
					</div>
				</div>
			</div>

			<div class="controls mt-4">
				<button type="submit" name="command[save]" class="btn btn-primary">Save Email Settings</button>
				<a href="{$routeBase}" class="btn btn-defaul">Cancel</a>
			</div>

		</form>

	</div>




</div>