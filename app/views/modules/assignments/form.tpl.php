<h1>{$page_header}</h1>

<form action="{$smarty.const.CURRENT_URL}" method="post" id="module_save_form">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus_id}" />
	<input type="hidden" name="module_type" value="{$module.id}" />
	<input type="hidden" name="item_id" value="{$item.assignment_id}" />
	<!-- Hitting enter in IE will not submit the form if there is only one text field, so we conditionally add another one for IE and hide it -->
	<!--[if IE]><input type="text" disabled="disabled" style="display: none;" /><![endif]-->

    <div class="label"><label for="assignment_title">Assignment Title</label></div>
    <div class="input"><input type="text" name="assignment_title" id="assignment_title" style="width: 300px;" value="{$item.assignment_title}" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="assignment_desc">Assignment Description</label></div>
    <div class="input"><textarea name="assignment_desc" id="assignment_desc" class="make_ckeditor" cols="*" rows="*" style="width: 300px; height: 100px;">{$item.assignment_desc}</textarea></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="assignment_value">Assignment Value</label></div>
    <div class="input"><input type="text" name="assignment_value" id="assignment_value" style="width: 300px;" value="{$item.assignment_value}" /></div>
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
