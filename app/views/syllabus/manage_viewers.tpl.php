<form action="{$smarty.server.REQUEST_URI}" method="post">
    <input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
    <input type="hidden" name="syllabus_id" value="{$syllabus.syllabus_id}" />
    <input type="hidden" name="return_url" value="syllabus/{$syllabus.syllabus_id}/share" />
    
    <fieldset><legend>View Information</legend>
        <div class="label">Syllabus URL</div>
        <div class="input"><input type="text" style="width: 100%;" value="{$smarty.const.BASEHREF}syllabus/view/{$syllabus.syllabus_id}" /></div>
        <div style="clear: both;"></div>
        
        <div class="label"><label for="syllabus_view_token">Syllabus Password</label></div>
        <div class="input">
            {$syllabus.syllabus_view_token}
            <span style="margin-left: 20px;">
                <a href="{$smarty.const.BASEHREF}syllabus/reset_token/{$syllabus.syllabus_id}" class="icon">
                    <span class="icon perms inline-block"></span>
                    Reset Password</a>
            </span>
        </div>
        <div style="clear: both;"></div>
    </fieldset>
    
    <fieldset><legend>View Permissions</legend>
        <div class="label"><label for="syllabus_visibility">Who can view?</label></div>
        <div class="input">
            <select name="syllabus_visibility" id="syllabus_visibility" style="width: 400px;">
                <option value="editors" {if $syllabus.syllabus_visibility == 'editors'} selected="selected"{/if}>Instructor and anyone listed as an Editor</option>
                <option value="members" {if $syllabus.syllabus_visibility == 'members'} selected="selected"{/if}>Instructor, Editors, and anyone enrolled in the class</option>
                <option value="public" {if $syllabus.syllabus_visibility == 'public'} selected="selected"{/if}>Everyone (Public)</option>
            </select>
        </div>
        <div style="clear: both;"></div>
        
        <div class="save_row">
            <div class="label">&nbsp;</div>
            <div class="input"><input type="submit" name="command[setSyllabusVisibility]" class="button" value="Save View Settings" /></div>
            <div style="clear: both;"></div>
        </div>
    </fieldset>
</form>
