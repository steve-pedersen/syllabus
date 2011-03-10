<form method="post" action="{$form_action}">
    {$smarty.const.SUBMIT_TOKEN_HTML}

	<div class="label"><label for="login_id"><abbr title="San Francisco State University Identification Number">SF State ID</abbr></label></div>
	<div class="input"><input type="text" name="login_id" id="login_id" style="width: 200px; letter-spacing: 1px;" /></div>
	<div style="clear: both;"></div>

	
	<div class="label"><label for="login_password"><abbr title="San Francisco State University Password">SF State Password</abbr></label></div>
	<div class="input"><input type="password" name="login_password" id="login_password" style="width: 200px; letter-spacing: 1px;" /></div>
	<div style="clear: both;"></div>		
	
    <div class="save_row">
        <div class="label">&nbsp;</div>
        <div class="input">
            <input type="submit" class="button submitButton" name="command[login]" value="Login" />
        </div>
        <div style="clear: both;"></div>
    </div>
</form>