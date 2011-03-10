&nbsp;

{if !isset($smarty.session.user_id)}
<form method="post" action="admin" style="margin-top: 50px;">
	<div>
		<input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
	</div>
	    
	<div style="margin: 1em 0px;">
        <label for="sfsu_id"><abbr title="San Francisco State University Identification Number">SF State ID</abbr></label><br />
        <input type="text" name="sfsu_id" id="sfsu_id" style="width: 180px; letter-spacing: 1px;" />
    </div>
    
	<div style="margin: 1em 0px;">
        <label for="sfsu_password"><abbr title="San Francisco State University Password">SF State Password</abbr></label><br />
        <input type="password" name="sfsu_password" id="sfsu_password" style="width: 180px; letter-spacing: 1px;" />
    </div>
	
    <div class="save_row">
        <input type="submit" class="button submitButton" name="command[doLogin]" value="Login to Syllabus" />
    </div>
</form>
{/if}