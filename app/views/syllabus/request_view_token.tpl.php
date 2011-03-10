<h1>Private Syllabus</h1>

{if $syllabus.syllabus_visibility == 'editors'}
<div class="messages warn">
Sorry, this syllabus is only available to specific people.  If the instructor has given you a temporary password to view
the syllabus, please use the form below to enter the password.
</div>
{/if}


{if $syllabus.syllabus_visibility == 'members'}
<div class="messages warn">
This syllabus is only available to students enrolled in the class.  If you are enrolled in the
class, please <a href="login" class="button" style="font-size: .9em;">Login</a> to the system to access the syllabus.  Otherwise, if
the instructor has given you a temporary password to view the syllabus, please use the form below
to enter the password.
</div>
{/if}

<div class="message info">
<form action="{$smarty.const.CURRENT_URL}" method="get" style="margin: 0px;">
    <label for="token">Password for this syllabus</label>
    <input type="text" name="token" id="token" style="width: 300px;" />
    <input type="submit" class="button" value="Submit Password" />
</form>
</div>

