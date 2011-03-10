<div class="message info">
Sharing a syllabus using the form below will send the link to the syllabus as well
as the syllabus password to the specified email addresses.  Please be aware that
the recipients will be able to view the syllabus regardless of what the current
<strong>View Settings</strong> may be. To revoke viewing permissions from these
recipients, you will have to <a href="syllabus/reset_token/{$syllabus.syllabus_id}">reset the password</a>.
</div>
    
<form action="{$smarty.const.CURRENT_URL}" method="post">
    {$smarty.const.SUBMIT_TOKEN_HTML}
    <input type="hidden" name="syllabus_id" value="{$syllabus.syllabus_id}" />
    <input type="hidden" name="to" value="pony@sfsu.edu" />
    
    <div class="label"><label for="invite_addresses">Email Address(es)<div class="form_note">(Separate with commas)</div></label></div>
    <div class="input">
        <textarea name="invite_addresses" id="invite_addresses" rows="*" cols="*" style="width: 380px; height: 50px;">{$syllabus.addresses}</textarea>
        <p class="form_note">
            To ensure privacy, this message will be sent to the course instructor's email address ({$syllabus.syllabus_email})
            and all of the listed recipients will be added to the Bcc field.
        </p>
    </div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="invite_message">Personal Message</label></div>
    <div class="input"><textarea name="invite_message" id="invite_message" class="make_ckeditor" rows="*" cols="*" style="width: 400px; height: 100px;">{$syllabus.message}</textarea></div>
    <div style="clear: both;"></div>
    
    <div class="save_row">
        <div class="label">&nbsp;</div>
        <div class="input"><input type="submit" name="command[sendInvites]" class="button" value="Send Invitations" /></div>
        <div style="clear: both;"></div>
    </div>
</form>
