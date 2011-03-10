{include file="modules/module_header.tpl.php"}

<div class="module_items_container" id="{$module}_items_container">

{if is_array($repository.schedules)}
    {include file="modules/module_browse_repository_link.tpl.php"}
{/if}

<table summary="Schedule for this course" cellspacing="0" cellpadding="0" id="{$module}_sort_parent">
    <thead>
    <tr>
        <td class="table_padder"><a href="syllabus/add_item/{$syllabus.syllabus_id}/{$module}" class="colorbox button">Add Schedule</a></td>
        <th scope="col" style="width: 80px;">Date</th>
        <th scope="col">Notes</th>
        <th scope="col">Deliverables</th>
    </tr>
    </thead>
    <tbody id="{$module}_items">

{foreach name="items_loop" from=$schedules item=i}

    {assign var='item_id' value=$i.schedule_id}
    {include file='modules/schedules/item.tpl.php'}

{foreachelse}
    <tr>
        <td colspan="4">
            <div class="message error">
                There are no Schedules for this syllabus.
            </div>
        </td>
    </tr>
{/foreach}

    </tbody>
</table>

</div>
