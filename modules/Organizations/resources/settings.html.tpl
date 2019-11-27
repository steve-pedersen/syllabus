<div class="container-fluid">
	<h1>Email Settings</h1>
{if $sendSuccess}
<div class="alert alert-info">
	<p>{$sendSuccess}</p>
	<p><strong>If you have made changes to the templates please make sure to save the changes below.</strong></p>
</div>
{/if}


<form id="fileAttachment" method="post" action="{$smarty.server.REQUEST_URI}" enctype="multipart/form-data">
	{generate_form_post_key}

    <h2 class="email-header"><u>Attachment Files</u></h2>
	<p>Upload new files to the server, which can then be selected to be sent as attachments for each email below.</p>

    <div class="form-group row">
        <div class="col-xs-12">
			{foreach item='att' from=$removedFiles}
			<input type="hidden" name="removed-files[{$att->id}]" value="{$att->id}" />
			{/foreach}
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Attachment</th>
                        <th>Size</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            {foreach item='attachment' from=$attachments}
                <tr>
                    <td>{$attachment->getDownloadLink('admin')}</td>
                    <td>{$attachment->contentLength|bytes}</td>
                    <td>
                        <input type="submit" name="command[remove-attachment][{$attachment->id}]" value="Remove From Server" class="btn btn-xs btn-danger" />
                        <input type="hidden" name="attachments[{$attachment->id}]" value="{$attachment->id}" />
                    </td>
                </tr>
            {foreachelse}
                <tr><td colspan="3">There are no attachments on the server.</td></tr>
            {/foreach}
            </table>
        </div>
    </div>

    <div class="form-group upload-form row">
        <div class="col-xs-12">
            <label for="attachment" class="field-label field-linked">Upload file attachment</label>       
            <input class="form-control" type="file" name="attachment" id="attachment" />
        {foreach item='error' from=$errors.attachment}<div class="error">{$error}</div>{/foreach}
        </div>
        <div class="col-xs-12 help-block text-center">
            <p id="type-error" class="bg-danger" style="display:none"><strong>There was an error with the type of file you are attempting to upload.</strong></p>
        </div>          
    </div>

    <div class="form-group row">  
        <div class="col-xs-12">
            <label for="files-title" class="field-linked inline">Preferred filename (include extension)</label>
            <input class="form-control" type="text" name="file[title]" id="files-title" class="inline" />
        </div>
    </div>

    <div class="form-group commands file-submit row email-row">
        <div class="col-xs-12">
            <input type="submit" name="command[upload]" id="fileSubmit" value="Upload File" class="btn btn-info hide" />  <!-- onclick="this.form.submit();" /> -->
        </div>
    </div>    
</form>

<form action="{$smarty.server.REQUEST_URI}" method="post">
	{generate_form_post_key}
	

	<h2 class="email-header"><u>Settings</u></h2>

	<div class="row">
		<div class="col-xs-12">
			<div class="form-group">
				<label for="defaultAddress">Default email address</label>
				<input type="email" class="form-control" name="defaultAddress" id="defaultAddress" value="{$defaultAddress}" placeholder="children@sfsu.edu..." />				
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="form-group">
				<label for="signature">Email Signature</label>
				<textarea name="signature" id="signature" class="wysiwyg form-control" rows="5" placeholder="  ---<br>The Children's Campus">{$signature}</textarea>		
			</div>
		</div>
	</div>

	<div class="row email-row testing-row well">
		<h3 class="">Debug Testing Mode</h3>
		<p class="alert testingOnly"><strong>Note, this is most likely for AT use only.</strong> Turning on testing will make it so that ALL email will be sent only to the "Debug testing address". If no testing address is specified, but testing is turned on, <u>email will fail to send to anyone</u>.</p>
		<div class="col-xs-6">
			<div class="form-group testingOnly">
				<label for="testingOnly">Turn Testing On</label><br>
				<input type="checkbox"  name="testingOnly" id="testingOnly" value="{if $testingOnly}1{/if}" {if $testingOnly}checked aria-checked="true"{/if} />						
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label for="testAddress">Debug testing address</label>
				<input type="email" class="form-control" name="testAddress" id="testAddress" value="{$testAddress}" placeholder="e.g. testaddress@gmail.com" />				
			</div>
		</div>
	</div>

	<div class="row email-row">
		<div class="col-xs-8">
			<div class="form-group">
				<h3 id="reservationReminder" class="toc-header" aria-hidden></h3>
				<label class="lead" for="reservationReminderTime">Reservation Reminder Time: <span class="email-type-description">choose an amount of time prior to a reservation to send a reminder email.</span></label>
				<select class="form-control" name="reservationReminderTime" id="reservationReminderTime">
				{foreach from=$reminderOptions item=opt}
					<option value="{$opt}">{$opt}</option>
				{/foreach}
				</select>
			</div>
		</div>

		<div class="col-xs-8">
			<div class="form-group">
				<label class="lead" for="reservationReminder">Reservation Reminder: <span class="email-type-description">send reservation details to Student prior to start of reservation.</span></label>
				<textarea name="reservationReminder" id="reservationReminder" class="wysiwyg form-control" rows="{if $reservationReminder}{$reservationReminder|count_paragraphs*2}{else}8{/if}">{$reservationReminder}</textarea>
				<span class="help-block">
					You can use the following tokens for context replacements to fill out the template: 
					<code>|%FIRST_NAME%|</code>, <code>|%LAST_NAME%|</code>, <code>|%RESERVE_DATE%|</code>, <code>|%RESERVE_VIEW_LINK%|</code>, <code>|%RESERVE_CANCEL_LINK%|</code>, <code>|%PURPOSE_INFO%|</code>, <code>|%ROOM_NAME%|</code>
				</span>
			</div>
		</div>

		<div class="col-xs-4">
			<label id="testreservationreminder">Test Reservation-Reminder Template</label>
			<p class="lead">This will send an email to your account showing how the email will look to you.</p>
			<button type="submit" name="command[sendtest][reservationReminder]" aria-describedby="testreservationreminder" class="btn btn-default">Send Test</button>
		</div>

		<div class="col-xs-12 form-group">
			<label id="attachmentReservationReminder">File Attachment(s) <span class="email-type-description"> - Select to attach</label>
			<select multiple="multiple" class="form-control attach-select" name="attachment[reservationReminder][]" size="{if $attachments|@count < 5}{$attachments|@count}{else}5{/if}" id="attachmentReservationReminder">
			{assign var=attachCount value=0}
			{foreach item='attachment' from=$attachments}
				{assign var='isAttached' value=false}
				{foreach item='key' from=$attachment->attachedEmailKeys}
					{if $key === 'reservationReminder'}{assign var='isAttached' value=true}{assign var=attachCount value=($attachCount+1)}{/if}
				{/foreach}
				<option value="{$attachment->id}" {if $isAttached}selected{/if}>
				{if $attachment->title}{$attachment->title}{else}{$attachment->remoteName}{/if}
				</option>
			{/foreach}
			</select>
			
			<p class="text-right caption-text"><em>Cmd+click on Mac or ctrl+click on Windows to select/deselect options.</em></p>
			{if $attachCount > 0}<span class="label label-success label-attachment">{$attachCount} attachment{if $attachCount > 1}s{/if}</span>{/if}
		</div>
	</div>

	<div class="controls">
		<button type="submit" name="command[save]" class="btn btn-primary">Save</button>
		<a href="admin" class="btn btn-default pull-right">Cancel</a>
	</div>

</form>

</div>