{include file="modules/module_header.tpl.php"}

<div class="module_items_container" id="{$module}_items_container">

{if is_array($repository.objectives)}
    {include file="modules/module_browse_repository_link.tpl.php"}
{/if}

    
<table summary="Objectives for this course" cellspacing="0" cellpadding="0" id="{$module}_sort_parent">
    <thead>
    <tr>
        <td class="table_padder"><a href="syllabus/add_item/{$syllabus.syllabus_id}/{$module}" class="colorbox button">Add Objective</a></td>
        <th scope="col">Objective</th>
    </tr>
    </thead>
    <tbody id="{$module}_items">
{foreach name="items_loop" from=$objectives item=i}

    {assign var='item_id' value=$i.objective_id}
    {include file='modules/objectives/item.tpl.php'}

{foreachelse}
    <tr>
        <td colspan="2">
            <div class="message error">
            There are no Objectives for this syllabus.
            </div>
        </td>
    </tr>
{/foreach}

    </tbody>
</table>

</div>