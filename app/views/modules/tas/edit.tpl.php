{include file="modules/module_header.tpl.php"}

<div class="module_items_container" id="{$module}_items_container">

{if is_array($repository.tas)}
    {include file="modules/module_browse_repository_link.tpl.php"}
{/if}

<table summary="Teaching Assistants for this course" cellspacing="0" cellpadding="0" id="{$module}_sort_parent">
    <thead>
    <tr>
        <td class="table_padder"><a href="syllabus/add_item/{$syllabus.syllabus_id}/{$module}" class="colorbox button">Add TA</a></td>
        <th scope="col" style="width: 250px;">Name</th>
        <th scope="col">Email</th>
    </tr>
    </thead>
    <tbody id="{$module}_items">

{foreach name="items_loop" from=$tas item=i}

    {assign var='item_id' value=$i.ta_id}
    {include file='modules/tas/item.tpl.php'}

{foreachelse}
    <tr>
        <td colspan="3">
            <div class="message error">
                There are no Teaching Assistants for this syllabus.
            </div>
        </td>
    </tr>
{/foreach}

    </tbody>
</table>

</div>
