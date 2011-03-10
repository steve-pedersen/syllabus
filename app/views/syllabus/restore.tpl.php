<div class="message warn">
Restoring a syllabus is a non-reversible procedure.  If you delete old content in the process, it will be lost.
</div>

<form action="{$smarty.const.CURRENT_URL}" method="post" enctype="multipart/form-data">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus.syllabus_id}" />

    <div class="label"><label for="backup_file">Select a Backup File</label></div>
    <div class="input"><input type="file" name="backup_file" id="backup_file" style="width: 300px;" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="restore_method">Restore Method</label></div>
    <div class="input">
        <select name="restore_method" id="restore_method" style="width: 400px;">
        <option value="append">Add restored modules and content to current content</option>
        <option value="delete">Delete old modules and content before restoring</option>
        </select>
    </div>
    <div style="clear: both;"></div>
    
    <div class="label">Restore these sections</div>
    <div class="input">
        <div style="padding-bottom: 3px; border: 1px solid #ccc; background-color: #eee;"><input type="checkbox" class="check-all" checked="checked" id="all_modules" /><label for="all_modules"><strong>Select All</strong></label></div>
        <div><input type="checkbox" name="restore_modules[]" id="restore_general" value="general" checked="checked" class="all_modules" /><label for="restore_general">Class / Contact information</label></div>
        {foreach from=$all_modules item=m}
        <div><input type="checkbox" name="restore_modules[]" id="restore_{$m.id}" value="{$m.id}" checked="checked" class="all_modules" /><label for="restore_{$m.id}">{$m.name}</label></div>
        {/foreach}
    </div>
    <div style="clear: both;"></div>
    
 	<div class="save_row">
        <div class="label">&nbsp;</div>
        <div class="input">
        	<input type="submit" name="command[restoreSyllabus]" class="button submitButton" value="Restore from Backup" />
        	<a href="syllabus/{$syllabus.syllabus_id}/edit" class="cancel_link">Cancel</a>
        </div>
        <div style="clear: both;"></div>
	</div>
</form>

