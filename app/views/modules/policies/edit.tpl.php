{include file="modules/module_header.tpl.php"}

<div class="module_items_container" id="{$module}_items_container">

{if is_array($repository.policies)}
    {include file="modules/module_browse_repository_link.tpl.php"}
{/if}

<table summary="Policies for this course" cellspacing="0" cellpadding="0" id="{$module}_sort_parent">
    <thead>
    <tr>
        <td class="table_padder"><a href="syllabus/add_item/{$syllabus.syllabus_id}/{$module}" class="colorbox button">Add Policy</a></td>
        <th scope="col">Policy</th>
    </tr>
    </thead>
    <tbody id="{$module}_items">
    
{foreach name="items_loop" from=$policies item=i}

    {assign var='item_id' value=$i.policy_id}
    {include file='modules/policies/item.tpl.php'}

{foreachelse}
    <tr>
        <td colspan="2">
            <div class="message error">
                There are no Policies for this syllabus.
            </div>
        </td>
    </tr>
{/foreach}

    </tbody>
</table>
</div>
