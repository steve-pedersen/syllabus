<h1>{$page_header}</h1>

{if $m.items} 
    <form action="{$smarty.server.REQUEST_URI}" method="post">
        {$smarty.const.SUBMIT_TOKEN_HTML}
        <input type="hidden" name="syllabus_id" value="{$syllabus.syllabus_id}" />
        <input type="hidden" name="module_type" value="{$m.id}" />
        
        {assign var='template_dir' value=$m.id}
        {include file="modules/$template_dir/repository_add.tpl.php"}
        
        <div class="save_row">
            <input type="submit" class="button" name="command[{$command}]" value="Add Item to Syllabus" />
            <a href="{$cancel}" class="cancel_link">Cancel</a>
        </div>
    </form>
{else}

    <div class="message info">
    There are no items in the repository for this module.
    </div>
    
{/if}
