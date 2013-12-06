<h1>{$page_header}</h1>

<form action="{$smarty.server.REQUEST_URI}" method="post" id="module_save_form">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus_id}" />
	<input type="hidden" name="module_type" value="{$module.id}" />
	<input type="hidden" name="item_id" value="{$item.schedule_id}" />
	<!-- Hitting enter in IE will not submit the form if there is only one text field, so we conditionally add another one for IE and hide it -->
	<!--[if IE]><input type="text" disabled="disabled" style="display: none;" /><![endif]-->

    <div class="label"><label for="schedule_period">Duration</label></div>
    <div class="input">
        <select name="schedule_period" id="schedule_period" style="width: 140px;">
            <option value="d"{if $item.schedule_period == 'd'} selected="selected"{/if}>For the Day of</option>
            <option value="w"{if $item.schedule_period == 'w'} selected="selected"{/if}>For the Week beginning</option>
        </select>
        
        <label for="schedule_date" style="padding-left: 10px;">Date <span class="form_note">(<abbr title="Please enter date in a month / day / year format">mm/dd/yy</abbr>)</span></label>
        <input type="text" name="schedule_date" id="schedule_date" style="width: 60px;" value="{$item.schedule_date}" />
    </div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="schedule_desc">Description</label></div>
    <div class="input"><textarea name="schedule_desc" id="schedule_desc" class="make_ckeditor" style="width: 300px; height: 100px;">{$item.schedule_desc}</textarea></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="schedule_due">Due</label></div>
    <div class="input"><textarea name="schedule_due" id="schedule_due" class="make_ckeditor" style="width: 300px; height: 100px;">{$item.schedule_due}</textarea></div>
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
