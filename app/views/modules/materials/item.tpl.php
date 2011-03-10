<tr class="module_item {$module}_sort_item" id="material_{$i.material_id}">
    <td valign="top" class="controls_cell">{include file="modules/module_item_controls.tpl.php"}</td>
    <td valign="top">{if $i.material_required == 1}Required{else}Optional{/if}</td>
    <th valign="top" scope="row">{$i.material_title}</th>
    <td valign="top">{$i.material_info}</td>
</tr>