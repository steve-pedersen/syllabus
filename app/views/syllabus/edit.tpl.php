<div id="syllabus">

<form action="{$smarty.server.REQUEST_URI}" method="post">
	{$smarty.const.SUBMIT_TOKEN_HTML}
	<input type="hidden" name="syllabus_id" value="{$syllabus_id}" />

    <div class="syllabus_content">
        <div id="syllabus_header_container">
            <h1 id="syllabus_header_edit">{$page_header} <a href="syllabus/view/{$syllabus_id}" style="font-size: .6em; margin-left: 15px; vertical-align: middle;">[ Exit Edit Mode ]</a></h1>
            <div id="syllabus_global_controls">
                <a href="" id="expand-collapse-all" class="enableJS expand-collapse-all button_large inline-block" rel="expand-collapse-all-modules"><span class="icon inline-block expand"></span><span class="text">Expand All</span></a>
                <a href="syllabus/add_module/{$syllabus.syllabus_id}" id="add_module_link" class="button_large inline-block"><span class="icon inline-block add"></span>Add Module</a>
                <a href="syllabus/share/{$syllabus.syllabus_id}" class="button_large inline-block"><span class="icon inline-block share"></span>Share</a>
                <a href="syllabus/view/{$syllabus.syllabus_id}?view=print" class="button_large popup noicon inline-block"><span class="icon inline-block print"></span>Printer-Friendly</a>
                <a href="syllabus/export/{$syllabus.syllabus_id}?export_msg=true&ref={$smarty.server.REQUEST_URI}" class="button_large inline-block colorbox"><span class="icon inline-block export"></span>Export</a>
                <a href="syllabus/backup_restore/{$syllabus.syllabus_id}" class="button_large inline-block"><span class="icon inline-block restore"></span>Backup / Restore</a>
            </div>
        </div>
    </div>

    {include file="modules/general/edit.tpl.php"}

    <ul class="sortable modules" id="module_sort_parent">
    {foreach from=$enabled_modules item=m key=module name="modules_loop"}
        <li class="module_container module_sort_item" id="{$module}_module">
            {include file="modules/$module/edit.tpl.php"}
        </li>
    {/foreach}
    </ul>

	<div class="save_row">
        <input type="submit" name="command[saveOrder]" class="button saveButton removeJS" value="Save Syllabus" />
	</div>
    
</form>
</div>