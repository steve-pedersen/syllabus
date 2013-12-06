<div id="syllabus_header_container">
    <h1 id="syllabus_header_view">{$page_header}</h1>
    <div id="view_controls" class="hide_printer_friendly">
        {if $can_edit}
        <a href="syllabus/edit/{$syllabus.syllabus_id}" class="button_large inline-block"><span class="icon inline-block edit"></span>Edit</a>
        {/if}
        <a href="{$smarty.const.BASEHREF}{$smarty.server.REQUEST_URI}?view=print" class="button_large popup noicon inline-block"><span class="icon inline-block print"></span>Printer-Friendly</a>
        <a href="syllabus/export/{$syllabus.syllabus_id}?export_msg=true&amp;ref={$smarty.server.REQUEST_URI}" class="button_large inline-block colorbox"><span class="icon inline-block export"></span>Export</a>
    </div>
</div>

{foreach from=$enabled_modules item=m key=module}

    {include file="modules/$module/render.tpl.php"}

{/foreach}
