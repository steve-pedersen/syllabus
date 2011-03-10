<table summary="List of TA items in the repository" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <th scope="col" style="width: 20px;"><span class="icon"><span class="icon edit inline-block"></span><span class="text">Edit</span></span></th>
        <th scope="col" style="width: 20px;"><span class="icon"><span class="icon remove inline-block"></span><span class="text">Delete</span></span></th>
        <th scope="col">TA Name</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$m.items item=i}
        <tr>
            <td>
                <a href="repository/edit/{$k}/{$i.ta_id}" class="icon">
                    <span class="icon edit inline-block"></span>
                    <span class="text">Edit</span>
                </a>
            </td>
            <td>
                <a href="repository/delete/{$k}/{$i.ta_id}" class="icon">
                    <span class="icon remove inline-block"></span>
                    <span class="text">Delete</span>
                </a>
            </td>
            <td>{$i.ta_name}</td>
        </tr>
        {/foreach}
    </tbody>
</table>