<table summary="List of Policy items in the repository" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <th scope="col" style="width: 30px;"><span class="icon"><span class="icon add inline-block"></span><span class="text">Select</span></span></th>
        <th scope="col" style="width: 250px;">Policy Title</th>
        <th scope="col">Policy Text</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$m.items item=i}
        <tr>
            <td valign="top"><input type="checkbox" name="add_ids[]" value="{$i.policy_id}" id="add_{$i.policy_id}" /></td>
            <td valign="top"><label for="add_{$i.policy_id}">{$i.policy_title}</label></td>
            <td valign="top">{$i.policy_text}</td>
        </tr>
        {/foreach}
    </tbody>
</table>