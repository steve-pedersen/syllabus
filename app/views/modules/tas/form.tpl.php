<h1>{$page_header}</h1>

<form action="{$smarty.const.CURRENT_URL}" method="post" id="module_save_form">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus_id}" />
	<input type="hidden" name="module_type" value="{$module.id}" />
	<input type="hidden" name="item_id" value="{$item.ta_id}" />

    <div class="label"><label for="ta_name" style="padding-left: 20px;">Name</abbr></label></div>
    <div class="input"><input type="text" name="ta_name" id="ta_name" style="width: 200px;" value="{$item.ta_name}" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="ta_email">Email</label></div>
    <div class="input"><input type="text" name="ta_email" id="ta_email" style="width: 200px;" value="{$item.ta_email}" /></div>
    <div style="clear: both;"></div>

    <div class="save_row">
        <div class="label">&nbsp;</div>
        <div class="input">
            <input type="submit" class="button" name="command[{$command}]" value="Save Changes" />
            <a href="{$cancel}" class="cancel_link">Cancel</a>
        </div>
        <div style="clear: both;"></div>
    </div>
</form>
