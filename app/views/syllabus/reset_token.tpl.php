<h1>Reset Syllabus Password</h1>

<form action="{$smarty.const.CURRENT_URL}" method="post">
    {$smarty.const.SUBMIT_TOKEN_HTML}
    <input type="hidden" name="syllabus_id" value="{$syllabus.syllabus_id}" />
    
    <div class="message warn">
    <p>
    Resetting the password will void the previous password. Anyone using the old password will no longer be able to access this syllabus.
    </p>
    <p>
    Are you sure you want to continue?
    </p>
        
    <div class="save_row">
        <input type="submit" name="command[resetToken]" class="button" value="Reset the Password" />
        <a href="syllabus/share/{$syllabus.syllabus_id}" class="cancel_link">Cancel</a>
    </div>
    </div>

</form>
