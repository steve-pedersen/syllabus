{include file="modules/module_header.tpl.php"}

<div class="module_items_container" id="{$module}_items_container">

{if is_array($repository.materials)}
    {include file="modules/module_browse_repository_link.tpl.php"}
{/if}
    
<table summary="Materials for this course" cellspacing="0" cellpadding="0" id="{$module}_sort_parent">
    <thead>
    <tr>
        <td class="table_padder"><a href="syllabus/add_item/{$syllabus.syllabus_id}/{$module}" class="colorbox button">Add Material</a></td>
        <th scope="col" style="width: 50px;">Required</th>
        <th scope="col" style="width: 250px;">Material</th>
        <th scope="col">Notes</th>
    </tr>
    </thead>
    <tbody id="{$module}_items">

{foreach name="items_loop" from=$materials item=i}

    {assign var='item_id' value=$i.material_id}
    {include file='modules/materials/item.tpl.php'}

{foreachelse}
    <tr>
        <td colspan="4">
            <div class="message error">
                There are no Materials for this syllabus.
            </div>
        </td>
    </tr>
{/foreach}

    </tbody>
</table>

</div>
