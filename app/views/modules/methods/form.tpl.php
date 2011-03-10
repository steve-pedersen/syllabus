<h1>{$page_header}</h1>

<form action="{$smarty.const.CURRENT_URL}" method="post" id="module_save_form">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus_id}" />
	<input type="hidden" name="module_type" value="{$module.id}" />
	<input type="hidden" name="item_id" value="{$item.method_id}" />

    <div class="label"><label for="method_title">Method Title</label></div>
    <div class="input"><input type="text" name="method_title" id="method_title" style="width: 300px;"" value="{$item.method_title}" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="method_text">Method</label></div>
    <div class="input"><textarea name="method_text" id="method_text" class="make_ckeditor" cols="*" rows="*" style="width: 300px; height: 100px;">{$item.method_text}</textarea></div>
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
