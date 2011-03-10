<h1>{$page_header}</h1>

<form action="{$smarty.const.CURRENT_URL}" method="post">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus_id}" />
    <input type="hidden" name="module_type" value="general" />
    
    {if !$is_draft }
    <div class="label"><label for="syllabus_class_number">Class Number</label></div>
    <div class="input">{$syllabus.syllabus_class_number}</div>
    <div style="clear: both;"></div>
    {/if}
    
    {if !$is_draft }
    <div class="label"><label for="syllabus_class_title">Class Title</label></div>
    <div class="input">{$syllabus.syllabus_class_title}</div>
    <div style="clear: both;"></div>	
    {else}
    <div class="label"><label for="syllabus_class_title">Draft Title</label></div>
    <div class="input"><input type="text" name="syllabus_class_title" id="syllabus_class_title" style="width: 300px;" value="{$syllabus.syllabus_class_title}" /></div>
    <div style="clear: both;"></div>	
    {/if}
    
    <div class="label"><label for="syllabus_class_description">Class Description</label></div>
    <div class="input"><textarea name="syllabus_class_description" id="syllabus_class_description" class="make_ckeditor" cols="*" rows="*" style="width: 300px; height: 100px;">{$syllabus.syllabus_class_description}</textarea></div>
    <div style="clear: both;"></div>	

    <div class="label"><label for="syllabus_instructor">Instructor</label></div>
    <div class="input"><input type="text" name="syllabus_instructor" id="syllabus_instructor" style="width: 300px;" value="{$syllabus.syllabus_instructor}" /></div>
    <div style="clear: both;"></div>	
    
    <div class="label"><label for="syllabus_office">Office</label></div>
    <div class="input"><input type="text" name="syllabus_office" id="syllabus_office" style="width: 300px;" value="{$syllabus.syllabus_office}" /></div>
    <div style="clear: both;"></div>	
    
    <div class="label"><label for="syllabus_office_hours">Office Hours</label></div>
    <div class="input"><textarea name="syllabus_office_hours" id="syllabus_office_hours" class="make_ckeditor" cols="*" rows="*" style="width: 300px; height: 100px;">{$syllabus.syllabus_office_hours}</textarea></div>
    <div style="clear: both;"></div>	
    
    <div class="label"><label for="syllabus_email">Email</label></div>
    <div class="input"><input type="text" name="syllabus_email" id="syllabus_email" style="width: 300px;" value="{$syllabus.syllabus_email}" /></div>
    <div style="clear: both;"></div>	
    
    <div class="label"><label for="syllabus_phone">Office Phone</label></div>
    <div class="input"><input type="text" name="syllabus_phone" id="syllabus_phone" style="width: 300px;" value="{$syllabus.syllabus_phone}" /></div>
    <div style="clear: both;"></div>	
    
    <div class="label"><label for="syllabus_mobile">Mobile Phone</label></div>
    <div class="input"><input type="text" name="syllabus_mobile" id="syllabus_mobile" style="width: 300px;" value="{$syllabus.syllabus_mobile}" /></div>
    <div style="clear: both;"></div>	
    
    <div class="label"><label for="syllabus_fax">Fax Number</label></div>
    <div class="input"><input type="text" name="syllabus_fax" id="syllabus_fax" style="width: 300px;" value="{$syllabus.syllabus_fax}" /></div>
    <div style="clear: both;"></div>	
    
    <div class="label"><label for="syllabus_website">Website</label></div>
    <div class="input"><input type="text" name="syllabus_website" id="syllabus_website" style="width: 300px;" value="{$syllabus.syllabus_website}" /></div>
    <div style="clear: both;"></div>	

 	<div class="save_row">
        <div class="label">&nbsp;</div>
        <div class="input">
            <input type="submit" name="command[{$command}]" class="button" value="Save General Info" />
            <a href="{$cancel}" class="cancel_link">Cancel</a>
        </div>
        <div style="clear: both;"></div>
	</div>
</form>