<h1>{$page_header}</h1>

<div class="message info">
Required fields are marked with an asterisk (<span class="required">*</span>)
</div>

<form action="{$smarty.const.CURRENT_URL}" method="post" id="module_save_form">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus.syllabus_id}" />
	<!-- Hitting enter in IE will not submit the form if there is only one text field, so we conditionally add another one for IE and hide it -->
	<!--[if IE]><input type="text" disabled="disabled" style="display: none;" /><![endif]-->
    
    {if isset($module) }
    <div class="label">Module Type</div>
    <div class="input">{$module.name}</div>
	<input type="hidden" name="module_type" value="{$module.id}" />
    {else}
    <div class="label"><label for="module_type">Module Type<span class="required">*</span></label></div>
    <div class="input">
        <select name="module_type" id="module_type" style="width: 250px;">
            {foreach from=$add_modules item=m}
            <option value="{$m.id}">{$m.name}</option>       
            {/foreach}
        </select>
    </div>
    {/if}
    <div style="clear: both;""></div>
    
    <div class="label"><label for="module_custom_name">Module Title</label></div>
    <div class="input"><input type="text" name="module_custom_name" id="module_custom_name" value="{$module_custom_name}" style="width: 250px;" /></div>
    <div style="clear: both;"></div>
	
	<div class="save_row">
        <div class="label">&nbsp;</div>
        <div class="input">
            <input type="submit" name="command[{$command}]" class="button saveButton" value="Save Module" />
            <a href="syllabus/edit/{$syllabus.syllabus_id}" class="cancel_link">Cancel</a>
        </div>
        <div style="clear: both;"></div>
	</div>
</form>
