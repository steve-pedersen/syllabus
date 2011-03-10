<table summary="List of TA items in the repository" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <th scope="col" style="width: 30px;"><span class="icon"><span class="icon add inline-block"></span><span class="text">Select</span></span></th>
        <th scope="col" style="width: 250px;">TA Name</th>
        <th scope="col">Email</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$m.items item=i}
        <tr>
            <td valign="top"><input type="checkbox" name="add_ids[]" value="{$i.ta_id}" id="add_{$i.ta_id}" /></td>
            <td valign="top"><label for="add_{$i.ta_id}">{$i.ta_name}</label></td>
            <td valign="top">{$i.ta_email}</td>
        </tr>
        {/foreach}
    </tbody>
</table>