<h1><a href="users/view/{$user.user_id}">{$user.user_fname} {$user.user_lname}</a></h1>

<form action="{$smarty.server.REQUEST_URI}" method="post">
	{$smarty.const.SUBMIT_TOKEN_HTML}
    <input type="hidden" name="user_id" value="{$user.user_id}" />
    
    <div class="label">First Name</div>
    <div class="input">{$user.user_fname}</div>
    <div style="clear: both;"></div>
    
    <div class="label">Last Name</div>
    <div class="input">{$user.user_lname}</div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="user_preferred_name">Display Name</label></div>
    <div class="input"><input type="text" style="width: 300px;" name="user_preferred_name" id="user_preferred_name" aria-required="true" value="{$user.user_preferred_name}" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="user_email">Preferred Email</label></div>
    <div class="input"><input type="text" style="width: 300px;" name="user_email" id="user_email" aria-required="true" value="{$user.user_email}" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="user_office">Office Location</label></div>
    <div class="input"><input type="text" style="width: 300px;" name="user_office" id="user_office" value="{$user.user_office}" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="user_phone">Office Phone Number</label></div>
    <div class="input"><input type="text" style="width: 300px;" name="user_phone" id="user_phone" value="{$user.user_phone}" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="user_mobile">Mobile Phone Number</label></div>
    <div class="input"><input type="text" style="width: 300px;" name="user_mobile" id="user_mobile" value="{$user.user_mobile}" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="user_fax">Fax</label></div>
    <div class="input"><input type="text" style="width: 300px;" name="user_fax" id="user_fax" value="{$user.user_fax}" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="user_website">Website</label></div>
    <div class="input"><input type="text" style="width: 300px;" name="user_website" id="user_website" value="{$user.user_website}" /></div>
    <div style="clear: both;"></div>

    <div class="label">&nbsp;</div>
    <div class="save_row">
        <input type="submit" name="command[editUser]" value="Save Changes" class="button submitButton" />
        <a href="users/view/{$user.user_id}" class="cancel_link">Cancel</a>
    </div>
    <div style="clear: both;"></div>
    
</form>
