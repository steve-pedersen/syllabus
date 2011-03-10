<table summary="List of Method items in the repository" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <th scope="col" style="width: 30px;"><span class="icon"><span class="icon add inline-block"></span><span class="text">Select</span></span></th>
        <th scope="col" style="width: 250px;">Method</th>
        <th scope="col">Information</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$m.items item=i}
        <tr>
            <td valign="top"><input type="checkbox" name="add_ids[]" value="{$i.method_id}" id="add_{$i.method_id}" /></td>
            <td valign="top"><label for="add_{$i.method_id}">{$i.method_title}</label></td>
            <td valign="top">{$i.method_text}</td>
        </tr>
        {/foreach}
    </tbody>
</table>