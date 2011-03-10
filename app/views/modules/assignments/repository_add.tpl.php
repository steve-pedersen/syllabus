<table summary="List of Assignment items in the repository" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <th scope="col" style="width: 30px;"><span class="icon"><span class="icon add inline-block"></span><span class="text">Select</span></span></th>
        <th scope="col" style="width: 250px;">Assignment Title</th>
        <th scope="col">Assignment Description</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$m.items item=i}
        <tr>
            <td valign="top"><input type="checkbox" name="add_ids[]" value="{$i.assignment_id}" id="add_{$i.assignment_id}" /></td>
            <td valign="top"><label for="add_{$i.assignment_id}">{$i.assignment_title}</label></td>
            <td valign="top">{$i.assignment_desc}</td>
        </tr>
        {/foreach}
    </tbody>
</table>