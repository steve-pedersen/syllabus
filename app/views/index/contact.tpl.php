<h1>Contact the Syllabus Team</h1>

Please use the contact form below to send a message to the Syllabus Team.  We will respond to you as quickly as possible.

    
<form action="{$smarty.const.CURRENT_URL}" method="post">
    {$smarty.const.SUBMIT_TOKEN_HTML}
    
    <div class="label"><label for="name">Your Name</label></div>
    <div class="input"><input type="text" name="name" id="name" value="{$contact->name}" style="width: 300px;" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="from">Your Email Address</label></div>
    <div class="input"><input type="text" name="from" id="from" value="{$contact->from}" style="width: 300px;" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="subject">Subject</label></div>
    <div class="input"><input type="text" name="subject" id="subject" value="{$contact->subject}" style="width: 300px;" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="addresses">Your Message</label></div>
    <div class="input"><textarea name="body" id="body" rows="*" cols="*" class="make_ckeditor" style="width: 400px; height: 50px;">{$contact->body}</textarea></div>
    <div style="clear: both;"></div>
    
    <div class="save_row">
        <div class="label">&nbsp;</div>
        <div class="input">
            <input type="submit" name="command[contactForm]" class="button" value="Send Message" />
            <a href="{$cancel}" class="cancel_link">Cancel</a>
        </div>
        <div style="clear: both;"></div>
    </div>
</form>
