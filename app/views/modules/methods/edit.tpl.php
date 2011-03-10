{include file="modules/module_header.tpl.php"}

<div class="module_items_container" id="{$module}_items_container">

{if is_array($repository.methods)}
    {include file="modules/module_browse_repository_link.tpl.php"}
{/if}

<table summary="Teaching Methods for this course" cellspacing="0" cellpadding="0" id="{$module}_sort_parent">
    <thead>
    <tr>
        <td class="table_padder"><a href="syllabus/add_item/{$syllabus.syllabus_id}/{$module}" class="colorbox button">Add Method</a></td>
        <th scope="col">Method</th>
    </tr>
    </thead>
    <tbody id="{$module}_items">
    
{foreach name="items_loop" from=$methods item=i}
    
    {assign var='item_id' value=$i.method_id}
    {include file='modules/methods/item.tpl.php'}

{foreachelse}
    <tr>
        <td colspan="2">
            <div class="message error">
                There are no Methods for this syllabus.
            </div>
        </td>
    </tr>
{/foreach}

    </tbody>
</table>

</div>
