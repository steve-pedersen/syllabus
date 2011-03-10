<table summary="List of Material items in the repository" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <th scope="col" style="width: 30px;"><span class="icon"><span class="icon add inline-block"></span><span class="text">Select</span></span></th>
        <th scope="col" style="width: 250px;">Material Title</th>
        <th scope="col">Information</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$m.items item=i}
        <tr>
            <td valign="top"><input type="checkbox" name="add_ids[]" value="{$i.material_id}" id="add_{$i.material_id}" /></td>
            <td valign="top"><label for="add_{$i.material_id}">{$i.material_title}</label></td>
            <td valign="top">{$i.material_info}</td>
        </tr>
        {/foreach}
    </tbody>
</table>