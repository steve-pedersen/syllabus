<h1>{$page_header}</h1>

<form action="{$smarty.const.CURRENT_URL}" method="post" id="module_save_form">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus_id}" />
	<input type="hidden" name="module_type" value="{$module.id}" />
	<input type="hidden" name="item_id" value="{$item.policy_id}" />

    <div class="label"><label for="policy_title">Policy Title</label></div>
    <div class="input"><input type="text" name="policy_title" id="policy_title" style="width: 300px;" value="{$item.policy_title}" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="policy_text">Policy</label></div>
    <div class="input"><textarea name="policy_text" id="policy_text" cols="*" rows="*" class="make_ckeditor" style="width: 300px; height: 100px;">{$item.policy_text}</textarea></div>
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
