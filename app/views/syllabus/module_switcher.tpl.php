<form action="{$smarty.server.REQUEST_URI}" method="post">
	<input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
	<input type="hidden" name="syllabus_id" value="{$syllabus->syllabus_id}" />
	<input type="hidden" name="return_url" value="syllabus/view/{$syllabus->syllabus_id}" />
    <label for="switch_to_module">View Syllabus Section</label>
    <select name="switch_to_module" id="switch_to_module" style="width: 200px; font-size: 1.1em; padding: .2em;">
        <option value="syllabus/{$syllabus->syllabus_id}" {if $current_module=='all'}selected="selected"{/if}>Entire Syllabus</option>
        <option value="syllabus/{$syllabus->syllabus_id}/general" {if $current_module=='general'}selected="selected"{/if}>Class Information</option>
        {foreach from=$enabled_modules item=m}
        <option value="syllabus/{$syllabus->syllabus_id}/{$m.module_type}" {if $current_module==$m.module_type}selected="selected"{/if}>{$m.module_custom_name}</option>
        {/foreach}
    </select>
    <input type="submit" value="View This Section" name="command[doSwitchSyllabusModule]" class="button" />
    <a href="syllabus/{$syllabus->syllabus_id}?view=print" class="popup noicon noborder"><img src="images/print.png" alt="Printer-Friendly Version" title="Printer-Friendly Version" /></a>
    <a href="syllabus/{$syllabus->syllabus_id}?view=msword" class="popup noicon noborder"><img src="images/word.png" alt="Microsoft Word Version" title="Microsoft Word Version" /></a>
</form>
