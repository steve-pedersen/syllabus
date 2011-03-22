<h1>{$page_header}</h1>

<form action="{$smarty.const.CURRENT_URL}" method="post" id="module_save_form">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus_id}" />
	<input type="hidden" name="module_type" value="{$module.id}" />
	<input type="hidden" name="item_id" value="{$item.material_id}" />
	<!-- Hitting enter in IE will not submit the form if there is only one text field, so we conditionally add another one for IE and hide it -->
	<!--[if IE]><input type="text" disabled="disabled" style="display: none;" /><![endif]-->

    <div class="label"><label for="material_title">Title</label></div>
    <div class="input"><input type="text" name="material_title" id="material_title" style="width: 300px;" value="{$item.material_title}" /></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="material_info">Additional Info</label></div>
    <div class="input"><textarea name="material_info" id="material_info]" class="make_ckeditor" rows="*" cols="*" style="width: 300px; height: 100px;">{$item.material_info}</textarea></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="material_required">Required</label></div>
    <div class="input">
    <select name="material_required" id="material_required" style="width: 200px;">
        <option value="1" {if $item.material_required==1}selected="selected"{/if}>Required</option>
        <option value="0" {if $item.material_required==0}selected="selected"{/if}>Optional</option>
    </select>
    </div>
    <div style="clear: both;"></div>
    
    <div class="save_row">
        <div class="label">&nbsp;</div>
        <div class="input">
            <input type="submit" class="button" name="command[{$command}]" value="Save Material" />
            <a href="{$cancel}" class="cancel_link">Cancel</a>
        </div>
        <div style="clear: both;"></div>
    </div>
</form>
