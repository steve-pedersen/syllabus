<table summary="List of Objective items in the repository" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <th scope="col" style="width: 30px;"><span class="icon"><span class="icon add inline-block"></span><span class="text">Select</span></span></th>
        <th scope="col" style="width: 250px;">Objective Title</th>
        <th scope="col">Objective Text</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$m.items item=i}
        <tr>
            <td valign="top"><input type="checkbox" name="add_ids[]" value="{$i.objective_id}" id="add_{$i.objective_id}" /></td>
            <td valign="top"><label for="add_{$i.objective_id}">{$i.objective_title}</label></td>
            <td valign="top">{$i.objective_text}</td>
        </tr>
        {/foreach}
    </tbody>
</table>