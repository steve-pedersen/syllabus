{include file="modules/module_header.tpl.php"}

<div class="module_items_container" id="{$module}_items_container">
    
{if is_array($repository.assignments)}
    {include file="modules/module_browse_repository_link.tpl.php"}
{/if}
    
<table summary="Assignments for this course" cellspacing="0" cellpadding="0" id="{$module}_sort_parent">
    <thead>
    <tr>
        <td class="table_padder"><a href="syllabus/add_item/{$syllabus.syllabus_id}/{$module}" class="colorbox button">Add Assignment</a></td>
        <th scope="col" style="width: 200px;">Assignment</th>
        <th scope="col" style="width: 80px;">Value</th>
        <th scope="col">Notes</th>
    </tr>
    </thead>
    <tbody id="{$module}_items">

{foreach name="items_loop" from=$assignments item=i}

    {assign var='item_id' value=$i.assignment_id}
    {include file='modules/assignments/item.tpl.php'}

{foreachelse}
    <tr>
        <td colspan="4"
            <div class="message error">
                There are no Assignments for this syllabus.
            </div>
        </td>
    </tr>
{/foreach}

    </tbody>
</table>

</div>
